<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Realizamos el fetch a la API de FOG
        $response = Http::withHeaders([
            'fog-api-token' => env('FOG_API_TOKEN'),
            'fog-user-token' => env('FOG_USER_TOKEN'),
        ])->get(env('FOG_SERVER_URL') . '/fog/host');

        // Verificamos si la petición fue exitosa
        if ($response->successful()) {
            // Extraemos los datos de la respuesta
            $fogData = $response->json();
            $productos = $fogData['hosts'];

            // Limpiamos datos innecesarios y formateamos las claves
            $productos = array_map(function ($producto) {
                // Aquí eliminamos campos innecesarios y cambiamos nombres de claves
                unset($producto['image'], $producto['hostscreen'], $producto['hostalo'], $producto['macs']);

                // Renombramos las claves
                $producto['id_equipo'] = $producto['id'];
                $producto['nombre'] = $producto['name'];
                $producto['descripcion'] = $producto['description'];
                $producto['ip'] = $producto['ip'];
                $producto['fecha_creacion'] = $producto['createdTime'];
                $producto['mac'] = $producto['primac'];
                unset($producto['id'], $producto['name'], $producto['description'], $producto['createdTime'], $producto['primac']);
                
                return $producto;
            }, $productos);

            // Paginamos los productos
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

            // Pasamos los datos a la vista
            return view('modules.inventario.index', compact('productos'));
        } else {
            // Si no fue exitosa la petición, redirigimos a la página principal con un mensaje de error
            return redirect()->route('home')->with('error', 'No se pudo obtener los datos del inventario.');
        }
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
