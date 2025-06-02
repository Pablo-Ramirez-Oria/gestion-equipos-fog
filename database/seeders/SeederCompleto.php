<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Ubicacion;
use App\Models\Estado;

class SeederCompleto extends Seeder
{
    public function run(): void
    {
        // Crear o buscar ubicación y estado con Eloquent
        $ubicacion = Ubicacion::firstOrCreate(['nombre' => 'Aula 2.5']);
        $estado = Estado::firstOrCreate(['nombre' => 'Disponible']);

        // Crear o actualizar inventario_detalle con fog_id = 100
        DB::table('inventario_detalles')->updateOrInsert(
            ['fog_id' => 100],
            [
                'ubicacion_id' => $ubicacion->id,
                'estado_id' => $estado->id,
                'finalidad_actual' => 'Prueba del sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $inventario_detalle_id = DB::table('inventario_detalles')->where('fog_id', 100)->value('id');

        // Crear personas préstamo
        $persona1 = DB::table('personas_prestamo')->insertGetId([
            'nombre' => 'Lucía',
            'apellidos' => 'García Pérez',
            'mayor_edad' => true,
            'correo' => 'lucia@example.com',
            'telefono' => '612345678',
            'curso' => '2º',
            'unidad' => 'DAW',
            'tipo' => 'alumno',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $persona2 = DB::table('personas_prestamo')->insertGetId([
            'nombre' => 'Antonio',
            'apellidos' => 'Sánchez López',
            'mayor_edad' => true,
            'correo' => 'antonio.sanchez@centro.edu',
            'telefono' => null,
            'curso' => null,
            'unidad' => null,
            'tipo' => 'profesor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear préstamos
        DB::table('prestamos')->insert([
            [
                'persona_prestamo_id' => $persona1,
                'inventario_detalle_id' => $inventario_detalle_id,
                'tipo_prestamo' => 'casa',
                'fecha_inicio' => Carbon::now()->subDays(3),
                'fecha_estimacion' => Carbon::now()->addDays(4),
                'fecha_entrega' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persona_prestamo_id' => $persona2,
                'inventario_detalle_id' => $inventario_detalle_id,
                'tipo_prestamo' => 'clase',
                'fecha_inicio' => Carbon::now()->subDay(),
                'fecha_estimacion' => Carbon::now()->addDay(),
                'fecha_entrega' => Carbon::now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ Seeder completo ejecutado con éxito.');
    }
}
