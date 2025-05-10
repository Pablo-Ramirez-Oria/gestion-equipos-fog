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
                'name' => 'Apple MacBook Pro 17"',
                'color' => 'Silver',
                'category' => 'Laptop',
                'accessories' => 'Yes',
                'available' => 'Yes',
                'price' => '$2999',
                'weight' => '3.0 lb.',
            ],
            [
                'name' => 'Microsoft Surface Pro',
                'color' => 'White',
                'category' => 'Laptop PC',
                'accessories' => 'No',
                'available' => 'Yes',
                'price' => '$1999',
                'weight' => '1.0 lb.',
            ],
            [
                'name' => 'Magic Mouse 2',
                'color' => 'Black',
                'category' => 'Accessories',
                'accessories' => 'Yes',
                'available' => 'No',
                'price' => '$99',
                'weight' => '0.2 lb.',
            ],
            [
                'name' => 'Apple Watch',
                'color' => 'Black',
                'category' => 'Watches',
                'accessories' => 'Yes',
                'available' => 'No',
                'price' => '$199',
                'weight' => '0.12 lb.',
            ],
            [
                'name' => 'Apple iMac',
                'color' => 'Silver',
                'category' => 'PC',
                'accessories' => 'Yes',
                'available' => 'Yes',
                'price' => '$2999',
                'weight' => '7.0 lb.',
            ],
            [
                'name' => 'Apple AirPods',
                'color' => 'White',
                'category' => 'Accessories',
                'accessories' => 'No',
                'available' => 'Yes',
                'price' => '$399',
                'weight' => '38 g',
            ],
            [
                'name' => 'iPad Pro',
                'color' => 'Gold',
                'category' => 'Tablet',
                'accessories' => 'No',
                'available' => 'Yes',
                'price' => '$699',
                'weight' => '1.3 lb.',
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
