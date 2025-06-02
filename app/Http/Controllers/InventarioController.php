<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\InventarioDetalle;
use App\Models\Estado;
use App\Models\Ubicacion;

class InventarioController extends Controller
{
    public function __construct()
    {
        // Middleware para autenticación de usuarios
        $this->middleware('role:admin')->only([
            'create', 'store', 'edit', 'update', 'destroy'
        ]);
    }

    public function index(Request $request)
    {
        // Obtener filtros de la solicitud
        $estadoId = $request->input('estado');
        $ubicacionId = $request->input('ubicacion');
        $busqueda = $request->input('busqueda');

        // Petición a la API de FOG para obtener los hosts
        $response = Http::withHeaders([
            'fog-api-token' => env('FOG_API_TOKEN'),
            'fog-user-token' => env('FOG_USER_TOKEN'),
        ])->get(env('FOG_SERVER_URL') . '/fog/host');

        if (!$response->successful()) {
            \Log::error('Error al obtener datos de FOG', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return redirect()->route('home')->with('error', 'No se pudo obtener los datos del inventario.');
        }

        // Procesar la respuesta de FOG
        $fogData = $response->json();
        $productosFog = $fogData['hosts'] ?? [];

        // Obtener IDs de FOG para evitar N+1
        $fogIds = array_column($productosFog, 'id');
        $detalles = InventarioDetalle::with(['ubicacion', 'estado'])
            ->whereIn('fog_id', $fogIds)
            ->get()
            ->keyBy('fog_id');

        // Mapeo de productos con detalles y filtros
        $productos = collect($productosFog)->map(function ($producto) use ($detalles, $estadoId, $ubicacionId, $busqueda) {
            $id = $producto['id'];
            $detalle = $detalles[$id] ?? null;

            $mapped = [
                'id_equipo' => $id,
                'nombre' => $producto['name'] ?? '-',
                'descripcion' => $producto['description'] ?? '-',
                'ip' => $producto['ip'] ?? '-',
                'fecha_creacion' => $producto['createdTime'] ?? '-',
                'mac' => $producto['primac'] ?? '-',
                'ubicacion' => $detalle?->ubicacion?->nombre ?? '-',
                'ubicacion_id' => $detalle?->ubicacion?->id,
                'estado' => $detalle?->estado?->nombre ?? '-',
                'estado_id' => $detalle?->estado?->id,
                'finalidad_actual' => $detalle?->finalidad_actual ?? '-',
                'inventory' => $producto['inventory'] ?? [],
            ];

            // Filtrar por estado, ubicación y búsqueda
            if ($estadoId && $mapped['estado_id'] != $estadoId) return null;
            if ($ubicacionId && $mapped['ubicacion_id'] != $ubicacionId) return null;

            if ($busqueda) {
                $busquedaLower = mb_strtolower($busqueda);
                if (
                    !str_contains(mb_strtolower($mapped['nombre']), $busquedaLower) &&
                    !str_contains(mb_strtolower($mapped['ip']), $busquedaLower)
                ) {
                    return null;
                }
            }

            return $mapped;
        })->filter()->values();

        // Paginación
        $perPage = 13;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $productos->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginator = new LengthAwarePaginator(
            $currentItems,
            $productos->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Envio de datos a la vista
        $estados = Estado::all();
        $ubicaciones = Ubicacion::all();

        return view('modules.inventario.index', [
            'productos' => $paginator,
            'estados' => $estados,
            'ubicaciones' => $ubicaciones,
        ]);
    }

    public function create()
    {
        // Devolver los estados y ubicaciones para los dropdowns
        $estados = Estado::all();
        $ubicaciones = Ubicacion::all();

        return view('modules.inventario.create', [
            'estados' => $estados,
            'ubicaciones' => $ubicaciones,
        ]);
    }

    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:15|regex:/^\S+$/',
            'descripcion' => 'nullable|string|max:255',
            'mac' => 'required|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
            'estado_id' => 'nullable|exists:estados,id',
            'ubicacion_id' => 'nullable|exists:ubicaciones,id',
            'finalidad' => 'nullable|string|max:255',
        ]);

        $macInput = strtolower($request->mac);

        // Verificar si la MAC ya está registrada en FOG (Causa error 500 en la API si ya existe)
        $response = Http::withHeaders([
            'fog-api-token' => env('FOG_API_TOKEN'),
            'fog-user-token' => env('FOG_USER_TOKEN'),
        ])->get(env('FOG_SERVER_URL') . '/fog/host');

        if (!$response->successful()) {
            return redirect()->back()->with('error', 'No se pudieron obtener los hosts desde FOG para validar la MAC.');
        }

        $hosts = $response->json()['hosts'] ?? [];

        // Extraer todas las MACs de todos los hosts
        $macsRegistradas = collect($hosts)->flatMap(function ($host) {
            $macs = [];

            if (!empty($host['primac'])) {
                $macs[] = strtolower($host['primac']);
            }

            if (!empty($host['macs']) && is_array($host['macs'])) {
                foreach ($host['macs'] as $macEntry) {
                    if (!empty($macEntry['mac'])) {
                        $macs[] = strtolower($macEntry['mac']);
                    }
                }
            }

            return $macs;
        })->unique();

        if ($macsRegistradas->contains($macInput)) {
            return redirect()->back()->withInput()
                ->with('error', 'La dirección MAC ya está registrada en FOG. Debe ser única.');
        }

        // Preparar los datos para la creación del host en FOG
        $payload = [
            'name' => $request->nombre,
            'description' => $request->descripcion,
            'macs' => [$macInput],
        ];

        try {
            // Enviar la solicitud a la API de FOG
            $fogResponse = Http::withHeaders([
                'fog-api-token' => env('FOG_API_TOKEN'),
                'fog-user-token' => env('FOG_USER_TOKEN'),
            ])->post(env('FOG_SERVER_URL') . '/fog/host/create', $payload);

            if (!$fogResponse->successful()) {
                $status = $fogResponse->status();
                $errorBody = $fogResponse->body();

                \Log::error('Error al crear el host en FOG', [
                    'status' => $status,
                    'body' => $errorBody,
                ]);

                return redirect()->back()->withInput()->with('error', 'No se pudo crear el equipo en FOG. Código de estado: ' . $status);
            }

            $data = $fogResponse->json();
            $fogId = $data['id'] ?? null;

            if (!$fogId) {
                \Log::error('Respuesta de FOG sin ID de host', ['response' => $data]);
                return redirect()->back()->withInput()->with('error', 'La respuesta de FOG no contiene un ID de host válido.');
            }

            // Guardar los detalles en la base de datos local
            InventarioDetalle::create([
                'fog_id' => $fogId,
                'estado_id' => $request->estado_id,
                'ubicacion_id' => $request->ubicacion_id,
                'finalidad_actual' => $request->finalidad,
            ]);

            return redirect()->route('inventario.index')->with('success', 'Equipo creado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Excepción al crear el host en FOG', ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error inesperado al crear el equipo en FOG.');
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        // Obtener datos desde la API
        $response = Http::withHeaders([
            'fog-api-token' => env('FOG_API_TOKEN'),
            'fog-user-token' => env('FOG_USER_TOKEN'),
        ])->get(env('FOG_SERVER_URL') . '/fog/host/' . $id);

        if (!$response->successful()) {
            return redirect()->route('inventario.index')->with('error', 'No se pudo obtener la información del equipo.');
        }

        $data = $response->json();

        $producto = [
            'id_equipo' => $data['id'],
            'nombre' => $data['name'],
            'descripcion' => $data['description'],
            'ip' => $data['ip'],
            'mac' => $data['primac'],
        ];

        $detalle = InventarioDetalle::with(['ubicacion', 'estado'])->where('fog_id', $id)->first() ?? new InventarioDetalle([
            'fog_id' => $id,
            'ubicacion_id' => null,
            'estado_id' => null,
            'finalidad_actual' => null,
        ]);

        $estados = Estado::all();
        $ubicaciones = Ubicacion::all();

        return view('modules.inventario.edit', compact('producto', 'detalle', 'estados', 'ubicaciones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:15|regex:/^\S+$/',
            'descripcion' => 'nullable|string|max:255',
            'estado_id' => 'nullable|exists:estados,id',
            'ubicacion_id' => 'nullable|exists:ubicaciones,id',
            'finalidad' => 'nullable|string|max:255',
        ]);

        // Actualiza en la API de FOG
        $fogUpdate = Http::withHeaders([
            'fog-api-token' => env('FOG_API_TOKEN'),
            'fog-user-token' => env('FOG_USER_TOKEN'),
        ])->put(env('FOG_SERVER_URL') . "/fog/host/{$id}", [
            'name' => $request->nombre,
            'description' => $request->descripcion,
        ]);

        if (!$fogUpdate->successful()) {
            return redirect()->back()->with('error', 'Error al actualizar el equipo en FOG.');
        }

        // Actualiza los detalles en la base de datos
        $detalle = InventarioDetalle::firstOrNew(['fog_id' => $id]);
        $detalle->estado_id = $request->estado_id;
        $detalle->ubicacion_id = $request->ubicacion_id;
        $detalle->finalidad_actual = $request->input('finalidad');
        $detalle->save();

        return redirect()->route('inventario.index')->with('success', 'Equipo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Eliminar en FOG
        $response = Http::withHeaders([
            'fog-api-token' => env('FOG_API_TOKEN'),
            'fog-user-token' => env('FOG_USER_TOKEN'),
        ])->delete(env('FOG_SERVER_URL') . "/fog/host/{$id}");

        if (!$response->successful()) {
            return redirect()->back()->with('error', 'No se pudo eliminar el equipo en FOG.');
        }

        // Eliminar los detalles del equipo en la base de datos local
        InventarioDetalle::where('fog_id', $id)->delete();

        return redirect()->route('inventario.index')->with('success', 'Equipo eliminado correctamente.');
    }

}
