<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Prestamo;

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
