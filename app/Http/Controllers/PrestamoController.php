<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Prestamo;
use App\Models\PersonaPrestamo;
use Illuminate\Support\Facades\Http;

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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
