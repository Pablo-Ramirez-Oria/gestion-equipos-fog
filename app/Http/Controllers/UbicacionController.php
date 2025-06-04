<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ubicacion;

class UbicacionController extends Controller
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

        $ubicaciones = Ubicacion::query()
            ->when($busqueda, function ($query, $busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%");
            })
            ->orderBy('nombre')
            ->paginate(13)
            ->withQueryString();

        return view('modules.ubicaciones.index', compact('ubicaciones', 'busqueda'));
    }

    public function create()
    {
        return view('modules.ubicaciones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        Ubicacion::create([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('ubicaciones.index')->with('success', 'Ubicación creada correctamente.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Ubicacion $ubicacion)
    {
        return view('modules.ubicaciones.edit', compact('ubicacion'));
    }

    public function update(Request $request, Ubicacion $ubicacion)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:ubicaciones,nombre,' . $ubicacion->id,
            'descripcion' => 'nullable|string|max:500',
        ]);

        $ubicacion->update($validated);

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Ubicación actualizada correctamente.');
    }

    public function destroy(Ubicacion $ubicacion)
    {
        $ubicacion->delete();

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Ubicación eliminada correctamente.');
    }

}
