<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = [
            [
                'Número de serie' => 'NKNJ50LU0000I05091',
                'Tipo de dispositivo' => 'Microordenador portátil',
                'Ubicación' => 'Aula 2.5',
                'Estado' => 'En uso',
            ],
            [
                'Número de serie' => 'R2BHK9VZ0000H21563',
                'Tipo de dispositivo' => 'Microordenador portátil',
                'Ubicación' => 'Aula 3.1',
                'Estado' => 'En uso',
            ],
            [
                'Número de serie' => 'JX6K74PU0000T30041',
                'Tipo de dispositivo' => 'Microordenador portátil',
                'Ubicación' => 'Aula 1.2',
                'Estado' => 'En uso',
            ],
            [
                'Número de serie' => 'FP8L21UE0000G78629',
                'Tipo de dispositivo' => 'Microordenador portátil',
                'Ubicación' => 'Aula 4.3',
                'Estado' => 'Disponible',
            ],
            [
                'Número de serie' => 'B3VQ0PZZ0000M42376',
                'Tipo de dispositivo' => 'Microordenador portátil',
                'Ubicación' => 'Aula 2.4',
                'Estado' => 'En uso',
            ],
            [
                'Número de serie' => 'D1EJ45LG0000S67012',
                'Tipo de dispositivo' => 'Microordenador portátil',
                'Ubicación' => 'Aula 5.2',
                'Estado' => 'Disponible',
            ],
            [
                'Número de serie' => 'L4VZ21XY0000Q50673',
                'Tipo de dispositivo' => 'Microordenador portátil',
                'Ubicación' => 'Aula 2.3',
                'Estado' => 'En uso',
            ],
        ];

        return view('modules.inventario.index', compact('productos'));
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
