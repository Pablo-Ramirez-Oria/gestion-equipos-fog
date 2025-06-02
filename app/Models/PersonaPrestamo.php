<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonaPrestamo extends Model
{
    protected $table = 'personas_prestamo';

    protected $fillable = [
        'nombre_completo',
        'mayor_edad',
        'correo',
        'telefono',
        'curso',
        'unidad',
        'tipo',
    ];

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }
}
