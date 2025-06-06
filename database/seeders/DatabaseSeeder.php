<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::where('email', 'admin@iesmartinezm.es')->delete();
        User::where('email', 'user@iesmartinezm.es')->delete();

        // Roles de usuario
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        // Usuario administrador
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@iesmartinezm.es',
            'password' => Hash::make('password')
        ]);
        $admin->assignRole('admin');

        // Usuario de solo lectura
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@iesmartinezm.es',
            'password' => Hash::make('password')
        ]);
        $user->assignRole('user');

        // Llamada a los otros seeders
        $this->call([
            UbicacionSeeder::class,
            EstadoSeeder::class,
            // Seeders para testing (Se eliminarán en producción)
            // SeederCompleto::class,
        ]);
    }
}
