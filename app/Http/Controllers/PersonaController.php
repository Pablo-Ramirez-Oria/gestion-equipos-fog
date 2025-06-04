<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonaPrestamo;
use Illuminate\Pagination\LengthAwarePaginator;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PersonaPrestamo::query();

        if ($request->filled('tipo') && in_array($request->tipo, ['alumno', 'profesor'])) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('busqueda')) {
            $search = $request->busqueda;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                ->orWhere('correo', 'like', "%{$search}%");
            });
        }

        $personas = $query->orderBy('nombre_completo')->paginate(13)->withQueryString();

        $tipos = [
            'profesor' => 'Profesor',
            'alumno' => 'Alumno',
        ];

        return view('modules.personas.index', [
            'personas' => $personas,
            'tipos' => $tipos,
            'filtros' => [
                'tipo' => $request->tipo,
                'busqueda' => $request->busqueda,
            ],
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
