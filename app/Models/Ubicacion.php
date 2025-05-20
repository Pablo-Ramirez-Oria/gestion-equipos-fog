<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $table = 'ubicaciones';
    
    protected $fillable = ['nombre'];

    public function inventarios()
    {
        return $this->hasMany(InventarioDetalle::class);
    }
}
