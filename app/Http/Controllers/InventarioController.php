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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $estadoId = $request->input('estado');
        $ubicacionId = $request->input('ubicacion');
        $busqueda = $request->input('busqueda');

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

        $fogData = $response->json();
        $productosFog = $fogData['hosts'] ?? [];

        // Obtener IDs de FOG para evitar N+1
        $fogIds = array_column($productosFog, 'id');
        $detalles = InventarioDetalle::with(['ubicacion', 'estado'])
            ->whereIn('fog_id', $fogIds)
            ->get()
            ->keyBy('fog_id');

        // Mapear y filtrar productos
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

            // Filtros
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

        // Datos para la vista
        $estados = Estado::all();
        $ubicaciones = Ubicacion::all();

        return view('modules.inventario.index', [
            'productos' => $paginator,
            'estados' => $estados,
            'ubicaciones' => $ubicaciones,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
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

        // Actualiza en la API FOG
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

        // Actualiza el detalle en la base de datos
        $detalle = InventarioDetalle::firstOrNew(['fog_id' => $id]);
        $detalle->estado_id = $request->estado_id;
        $detalle->ubicacion_id = $request->ubicacion_id;
        $detalle->finalidad_actual = $request->input('finalidad'); // ¡IMPORTANTE!
        $detalle->save();

        return redirect()->route('inventario.index')->with('success', 'Equipo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
