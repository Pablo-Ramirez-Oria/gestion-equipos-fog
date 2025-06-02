<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $fillable = [
        'persona_prestamo_id',
        'equipo_id',
        'tipo_prestamo',
        'fecha_inicio',
        'fecha_estimacion',
        'fecha_entrega',
    ];
    
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_estimacion' => 'datetime',
        'fecha_entrega' => 'datetime',
    ];

    public function persona()
    {
        return $this->belongsTo(PersonaPrestamo::class, 'persona_prestamo_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function getEstadoAttribute()
    {
        if (is_null($this->fecha_entrega)) {
            if (now()->lessThan($this->fecha_estimacion)) {
                return 'En curso';
            } else {
                return 'Retrasado';
            }
        } else {
            return 'Finalizado';
        }
    }
}
