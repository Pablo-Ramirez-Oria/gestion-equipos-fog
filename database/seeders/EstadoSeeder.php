<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Estado;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Estado::insert([
            ['nombre' => 'Disponible'],
            ['nombre' => 'En uso'],
            ['nombre' => 'En reparación'],
            ['nombre' => 'En préstamo'],
            ['nombre' => 'Dañado'],
        ]);
    }
}
