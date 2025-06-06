<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Prestamo;
use App\Models\PersonaPrestamo;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrestamoController extends Controller
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
        $estadoId = $request->input('estado');  // 'En curso', 'Retrasado', 'Finalizado'
        $tipoPrestamo = $request->input('tipo'); // 'clase' o 'casa'
        $busqueda = $request->input('busqueda');

        $query = Prestamo::with('persona');
        $query->orderByRaw('fecha_entrega IS NOT NULL, fecha_entrega DESC');
        
        // Obtener todos los préstamos
        $prestamos = $query->get()->filter(function ($prestamo) use ($estadoId, $tipoPrestamo, $busqueda) {

            if ($estadoId && $prestamo->estado !== $estadoId) {
                return false;
            }

            if ($tipoPrestamo && $prestamo->tipo_prestamo !== $tipoPrestamo) {
                return false;
            }

            if ($busqueda) {
                $busquedaLower = mb_strtolower($busqueda);

                $nombreCompleto = $prestamo->persona ? mb_strtolower($prestamo->persona->nombre_completo) : '';
                $enPersona = mb_strpos($nombreCompleto, $busquedaLower) !== false;
                $enFogId = mb_strpos((string)$prestamo->fog_id, $busquedaLower) !== false;

                if (!$enPersona && !$enFogId) {
                    return false;
                }
            }

            return true;
        });

        // Paginación
        $perPage = 13;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $prestamos->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $currentItems,
            $prestamos->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Los estados y tipos siempre son los mismos, así que los definimos aquí
        $estados = ['En curso' => 'En curso', 'Retrasado' => 'Retrasado', 'Finalizado' => 'Finalizado'];
        $tipos = ['clase' => 'Clase', 'casa' => 'Casa'];

        return view('modules.prestamos.index', [
            'prestamos' => $paginator,
            'estados' => $estados,
            'tipos' => $tipos,
            'filtros' => ['estado' => $estadoId, 'tipo' => $tipoPrestamo, 'busqueda' => $busqueda],
        ]);
    }

    public function create()
    {
        return view('modules.prestamos.create', [
            'personas' => PersonaPrestamo::all(),
            'tiposPrestamo' => ['clase', 'casa'],
        ]);
    }

    public function store(Request $request)
    {
        // Validaciones completas
        $request->validate([
            'fog_id' => ['required', 'integer'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_estimacion' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'fecha_entrega' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],

            // Persona ya existente o nueva
            'persona_prestamo_id' => ['required_without:nombre_completo', 'nullable', 'exists:personas_prestamo,id'],
            'nombre_completo' => ['required_without:persona_prestamo_id', 'nullable', 'string', 'max:255'],
            'correo' => ['nullable', 'email'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'curso' => ['nullable', 'string', 'max:50'],
            'unidad' => ['nullable', 'string', 'max:10'],
            'tipo' => ['required_without:persona_prestamo_id', 'nullable', 'in:alumno,profesor'],
            'mayor_edad' => ['nullable', 'boolean'],
            'tipo_prestamo' => ['required', 'in:clase,casa'],
        ]);

        $fogId = $request->fog_id;

        // Verificar que el host exista en FOG
        $response = Http::withHeaders([
            'fog-api-token' => env('FOG_API_TOKEN'),
            'fog-user-token' => env('FOG_USER_TOKEN'),
        ])->get(env('FOG_SERVER_URL') . "/fog/host/{$fogId}");
        
        if (!$response->successful()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['fog_id' => 'El equipo con ese fog_id no existe en FOG.']);
        }

        // Verificar que no haya un préstamo activo del mismo equipo
        $prestamoActivo = Prestamo::where('fog_id', $fogId)
            ->whereNull('fecha_entrega')
            ->exists();

        if ($prestamoActivo) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['fog_id' => 'Este equipo ya tiene un préstamo activo.']);
        }

        // Obtener o crear persona
        if ($request->persona_prestamo_id) {
            $personaId = $request->persona_prestamo_id;
        } else {
            $nuevaPersona = PersonaPrestamo::create([
                'nombre_completo' => $request->nombre_completo,
                'mayor_edad' => $request->boolean('mayor_edad', false),
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'curso' => $request->curso,
                'unidad' => $request->unidad,
                'tipo' => $request->tipo,
            ]);
            $personaId = $nuevaPersona->id;
        }

        // Crear préstamo
        Prestamo::create([
            'fog_id' => $fogId,
            'persona_prestamo_id' => $personaId,
            'tipo_prestamo' => $request->tipo_prestamo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_estimacion' => $request->fecha_estimacion,
            'fecha_entrega' => $request->fecha_entrega,
        ]);

        return redirect()->route('prestamos.index')->with('success', 'Préstamo creado correctamente.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $prestamo = Prestamo::with('persona')->findOrFail($id);

        // Obtener datos del equipo desde FOG
        $response = Http::withHeaders([
            'fog-api-token' => env('FOG_API_TOKEN'),
            'fog-user-token' => env('FOG_USER_TOKEN'),
        ])->get(env('FOG_SERVER_URL') . '/fog/host/' . $prestamo->fog_id);

        if (!$response->successful()) {
            return redirect()->route('prestamos.index')->with('error', 'No se pudo obtener la información del equipo desde FOG.');
        }

        $equipoFOG = $response->json();

        $producto = [
            'id_equipo' => $equipoFOG['id'],
            'nombre' => $equipoFOG['name'],
            'descripcion' => $equipoFOG['description'],
            'ip' => $equipoFOG['ip'],
            'mac' => $equipoFOG['primac'],
        ];

        // Personas disponibles (por si se quiere reasignar el préstamo)
        $personas = PersonaPrestamo::all();

        return view('modules.prestamos.edit', compact('prestamo', 'producto', 'personas'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'fog_id' => ['required', 'integer'],
            'tipo_prestamo' => ['required', 'in:clase,casa'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_estimacion' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'fecha_entrega' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'persona_prestamo_id' => ['nullable', 'exists:personas_prestamo,id'],
            'nombre_completo' => ['required_without:persona_prestamo_id', 'string', 'max:255'],
            'mayor_edad' => ['nullable', 'boolean'],
            'correo' => ['nullable', 'email'],
            'telefono' => ['nullable', 'string'],
            'curso' => ['nullable', 'string'],
            'unidad' => ['nullable', 'string'],
            'tipo' => ['required_without:persona_prestamo_id', 'in:alumno,profesor'],
        ]);

        // Obtener el préstamo
        $prestamo = Prestamo::findOrFail($id);

        // Obtener o crear persona
        if ($request->persona_prestamo_id) {
            $personaId = $request->persona_prestamo_id;
        } else {
            $nuevaPersona = PersonaPrestamo::create([
                'nombre_completo' => $request->nombre_completo,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'curso' => $request->curso,
                'unidad' => $request->unidad,
                'tipo' => $request->tipo,
                'mayor_edad' => $request->boolean('mayor_edad', false),
            ]);
            $personaId = $nuevaPersona->id;
        }

        // Actualizar préstamo
        $prestamo->update([
            'fog_id' => $request->fog_id,
            'tipo_prestamo' => $request->tipo_prestamo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_estimacion' => $request->fecha_estimacion,
            'fecha_entrega' => $request->fecha_entrega,
            'persona_prestamo_id' => $personaId,
        ]);

        return redirect()->route('prestamos.index')->with('success', 'Préstamo actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $prestamo = Prestamo::findOrFail($id);

        // Solo eliminamos el préstamo, no la persona ni nada más
        $prestamo->delete();

        return redirect()->route('prestamos.index')->with('success', 'Préstamo eliminado correctamente.');
    }

    public function exportarCSV()
    {
        $prestamos = Prestamo::with('persona')->get();

        $csvData = [];
        // Cabecera CSV con el orden solicitado
        $csvData[] = ['ID', 'Nombre', 'Fog ID', 'Tipo', 'Estado', 'Fecha de creación', 'Fecha Estimación', 'Fecha Entrega'];

        foreach ($prestamos as $prestamo) {
            $csvData[] = [
                $prestamo->id,
                $prestamo->persona->nombre_completo ?? '-',
                $prestamo->fog_id ?? '-',
                $prestamo->tipo_prestamo ?? '-',
                $prestamo->estado, // accesorio getEstadoAttribute
                $prestamo->fecha_inicio ? $prestamo->fecha_inicio->format('Y-m-d H:i:s') : '-',
                $prestamo->fecha_estimacion ? $prestamo->fecha_estimacion->format('Y-m-d H:i:s') : '-',
                $prestamo->fecha_entrega ? $prestamo->fecha_entrega->format('Y-m-d H:i:s') : '-',
            ];
        }

        // Crear archivo CSV en memoria
        $filename = 'prestamos_export_' . date('Ymd_His') . '.csv';

        $handle = fopen('php://memory', 'r+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);

        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

}
