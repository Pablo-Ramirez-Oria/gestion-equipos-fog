<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Ubicacion;

class UbicacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ubicacion::insert([
            ['nombre' => 'Aula 2.5'],
            ['nombre' => 'Secretaría'],
            ['nombre' => 'Salón Actos - Superior'],
            ['nombre' => 'Salón Actos - Inferior'],
            ['nombre' => 'Carrito 1'],
            ['nombre' => 'Carrito 2'],
            ['nombre' => 'Carrito 3'],
        ]);
    }
}
