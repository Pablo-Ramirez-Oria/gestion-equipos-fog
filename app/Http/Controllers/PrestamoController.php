<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrestamoController extends Controller
{
    public function __construct()
    {
        // Middleware para autenticación de usuarios
        $this->middleware('role:admin')->only([
            'create', 'store', 'edit', 'update', 'destroy'
        ]);
    }
    
    public function index()
    {
        return view('modules.prestamos.index');
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
