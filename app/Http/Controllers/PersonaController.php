<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonaPrestamo;
use Illuminate\Pagination\LengthAwarePaginator;

class PersonaController extends Controller
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

    public function create()
    {
        $tipos = [
            'profesor' => 'Profesor',
            'alumno' => 'Alumno',
        ];

        return view('modules.personas.create', compact('tipos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:255'],
            'mayor_edad' => ['required', 'boolean'],
            'correo' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'curso' => ['nullable', 'string', 'max:255'],
            'unidad' => ['nullable', 'string', 'max:255'],
            'tipo' => ['required', 'in:alumno,profesor'],
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'mayor_edad.required' => 'Debe indicar si es mayor de edad.',
            'mayor_edad.boolean' => 'El valor de mayor de edad no es válido.',
            'correo.email' => 'El correo debe ser una dirección válida.',
            'telefono.max' => 'El teléfono debe tener como máximo 20 caracteres.',
            'tipo.required' => 'Debe seleccionar un tipo de persona.',
            'tipo.in' => 'El tipo seleccionado no es válido.',
        ]);

        try {
            PersonaPrestamo::create($validated);
            return redirect()->route('personas.index')->with('success', 'Persona creada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ocurrió un error al guardar la persona: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(PersonaPrestamo $persona)
    {
        return view('modules.personas.edit', compact('persona'));
    }

    public function update(Request $request, PersonaPrestamo $persona)
    {
        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:255'],
            'mayor_edad' => ['required', 'boolean'],
            'correo' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'curso' => ['nullable', 'string', 'max:255'],
            'unidad' => ['nullable', 'string', 'max:255'],
            'tipo' => ['required', 'in:alumno,profesor,otro'],
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'mayor_edad.required' => 'Debe indicar si es mayor de edad.',
            'mayor_edad.boolean' => 'El valor de mayor de edad no es válido.',
            'correo.email' => 'El correo debe ser una dirección válida.',
            'telefono.max' => 'El teléfono debe tener como máximo 20 caracteres.',
            'tipo.required' => 'Debe seleccionar un tipo de persona.',
            'tipo.in' => 'El tipo seleccionado no es válido.',
        ]);

        try {
            $persona->update($validated);
            return redirect()->route('personas.index')->with('success', 'Persona actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $persona = PersonaPrestamo::find($id);

        if (!$persona) {
            return redirect()->route('personas.index')
                            ->with('error', 'Persona no encontrada.');
        }

        $persona->delete();

        return redirect()->route('personas.index')
                        ->with('success', 'Persona eliminada correctamente.');
    }
}
