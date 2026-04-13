<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'nombre',
        'nit',
        'db_name',
        'db_host',
        'db_port',
        'db_user',
        'db_password',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}