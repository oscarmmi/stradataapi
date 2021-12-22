<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Busquedas extends Model
{
    protected $table = 'busquedas';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'nombre_buscado',
        'porcentaje_buscado',
        'registros_encontrados',
        'estado_ejecucion'
    ];
}
