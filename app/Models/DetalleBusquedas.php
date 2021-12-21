<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleBusquedas extends Model
{
    protected $table = 'detalle_busquedas';
    
    protected $fillable = [
        'nombre_encontrado',
        'tipo_persona',
        'tipo_cargo',
        'departamento',
        'municipio', 
        'porcentaje_encontrado', 
        'uuid'
    ];
}
