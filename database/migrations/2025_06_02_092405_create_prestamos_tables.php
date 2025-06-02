<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla para almacenar información de la persona a la que se presta
        Schema::create('personas_prestamo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->boolean('mayor_edad')->default(false);
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('curso')->nullable();   // Puede ser nulo si es profesor
            $table->string('unidad')->nullable();  // Puede ser nulo si es profesor
            $table->enum('tipo', ['alumno', 'profesor']);
            $table->timestamps();
        });

        // Tabla principal de préstamos
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_prestamo_id')->constrained('personas_prestamo')->onDelete('cascade');
            $table->unsignedBigInteger('fog_id');
            $table->enum('tipo_prestamo', ['clase', 'casa']);
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_estimacion');
            $table->dateTime('fecha_entrega')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos');
        Schema::dropIfExists('personas_prestamo');
    }
};
