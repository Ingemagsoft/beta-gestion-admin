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

// ─── Estadísticas para el dashboard admin ───────────────────
    public static function estadisticasDashboard(): array
    {
        return [
            'total'     => self::count(),
            'activas'   => self::where('activo', true)->count(),
            'inactivas' => self::where('activo', false)->count(),
        ];
    }
}