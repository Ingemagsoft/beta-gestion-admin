<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $connection = 'tenant';
    protected $table = 'ciudades'; // Especificar el nombre de la tabla si no sigue la convención

    protected $fillable = [
        'nombre',
        'departamento',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Una ciudad tiene muchos clientes
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
}