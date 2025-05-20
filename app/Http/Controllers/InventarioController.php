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

        if ($response->successful()) {
            $fogData = $response->json();
            $productos = $fogData['hosts'];

            $productos = array_filter(array_map(function ($producto) use ($estadoId, $ubicacionId, $busqueda) {
                $producto['id_equipo'] = $producto['id'];
                $producto['nombre'] = $producto['name'];
                $producto['descripcion'] = $producto['description'];
                $producto['ip'] = $producto['ip'];
                $producto['fecha_creacion'] = $producto['createdTime'];
                $producto['mac'] = $producto['primac'];

                unset($producto['id'], $producto['name'], $producto['description'], $producto['createdTime'], $producto['primac'], $producto['image'], $producto['hostscreen'], $producto['hostalo'], $producto['macs']);

                $detalle = InventarioDetalle::with(['ubicacion', 'estado'])
                    ->where('fog_id', $producto['id_equipo'])
                    ->first();

                $producto['ubicacion'] = $detalle?->ubicacion?->nombre ?? 'Sin definir';
                $producto['ubicacion_id'] = $detalle?->ubicacion?->id;
                $producto['estado'] = $detalle?->estado?->nombre ?? 'Sin definir';
                $producto['estado_id'] = $detalle?->estado?->id;
                $producto['finalidad_actual'] = $detalle?->finalidad_actual ?? 'Sin definir';

                // Filtros
                if ($estadoId && $producto['estado_id'] != $estadoId) return null;
                if ($ubicacionId && $producto['ubicacion_id'] != $ubicacionId) return null;

                // Filtro de búsqueda por nombre o IP
                if ($busqueda) {
                    $busquedaLower = mb_strtolower($busqueda);
                    if (
                        !str_contains(mb_strtolower($producto['nombre']), $busquedaLower) &&
                        !str_contains(mb_strtolower($producto['ip']), $busquedaLower)
                    ) {
                        return null;
                    }
                }

                return $producto;
            }, $productos));

            // Paginación
            $perPage = 13;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = array_slice($productos, ($currentPage - 1) * $perPage, $perPage);
            $productos = new LengthAwarePaginator(
                $currentItems,
                count($productos),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            // Pasar datos a la vista
            $estados = Estado::all();
            $ubicaciones = Ubicacion::all();

            return view('modules.inventario.index', compact('productos', 'estados', 'ubicaciones'));
        }

        return redirect()->route('home')->with('error', 'No se pudo obtener los datos del inventario.');
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
