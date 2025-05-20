<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioDetalle extends Model
{
    protected $fillable = ['fog_id', 'ubicacion_id', 'estado_id', 'finalidad_actual'];

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
}
