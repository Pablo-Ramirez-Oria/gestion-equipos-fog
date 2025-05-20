<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventarioDetalle;
use App\Models\Ubicacion;
use App\Models\Estado;

class ActualizarEquipoSeeder extends Seeder
{
    public function run(): void
    {
        // Crear o buscar ubicación y estado
        $ubicacion = Ubicacion::firstOrCreate(['nombre' => 'Aula 2.5']);
        $estado = Estado::firstOrCreate(['nombre' => 'Disponible']);

        // Crear o actualizar el detalle con fog_id = 100
        $detalle = InventarioDetalle::updateOrCreate(
            ['fog_id' => 100], // criterio de búsqueda
            [ // campos a actualizar
                'ubicacion_id' => $ubicacion->id,
                'estado_id' => $estado->id,
                'finalidad_actual' => 'Prueba del sistema',
            ]
        );

        $this->command->info('✅ Se creó o actualizó InventarioDetalle con fog_id = 100.');
    }
}
