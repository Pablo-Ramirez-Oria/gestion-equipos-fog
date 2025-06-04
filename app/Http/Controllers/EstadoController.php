<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estado;

class EstadoController extends Controller
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
        $busqueda = $request->input('busqueda', '');

        $estados = Estado::query()
            ->when($busqueda, function ($query, $busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%");
            })
            ->orderBy('nombre')
            ->paginate(13)
            ->withQueryString();

        return view('modules.estados.index', compact('estados', 'busqueda'));
    }

    public function create()
    {
        return view('modules.estados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        Estado::create([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('estados.index')->with('success', 'Estado creado correctamente.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Estado $estado)
    {
        return view('modules.estados.edit', compact('estado'));
    }

    public function update(Request $request, Estado $estado)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre,' . $estado->id,
            'descripcion' => 'nullable|string|max:500',
        ]);

        $estado->update($validated);

        return redirect()->route('estados.index')
            ->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(Estado $estado)
    {
        $estado->delete();

        return redirect()->route('estados.index')
            ->with('success', 'Estado eliminado correctamente.');
    }
}
