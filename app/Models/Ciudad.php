<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $connection = 'tenant'; // asegura que este modelo use la conexión tenant
    protected $table = 'ciudades'; // Especificar el nombre de la tabla si no sigue la convención

    protected $fillable = [  // Campos que se pueden asignar masivamente, útil para crear o actualizar registros
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