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

    // ─── Listar todas las empresas ordenadas ─────────────────────
    public static function listarTodas() 
    
    {
        return self::orderBy('nombre')->get();
    }

    // ─── Buscar empresa por ID ────────────────────────────────────
    public static function buscarPorId(int $id): self
    {
        return self::findOrFail($id);
    }

    // ─── Registrar nueva empresa con defaults de conexión ────────
    public static function registrarNueva(array $datos): self
    {
        return self::create([
            'codigo'      => strtoupper(trim($datos['codigo'])),
            'nombre'      => trim($datos['nombre']),
            'nit'         => trim($datos['nit']),
            'db_name'     => strtolower(trim($datos['db_name'])),
            'db_host'     => '127.0.0.1',
            'db_port'     => 3306,
            'db_user'     => 'root',
            'db_password' => '',
            'activo'      => true,
        ]);
    }

    // ─── Actualizar nombre y NIT ──────────────────────────────────
    public static function actualizarDatos(int $id, array $datos): void
    {
        self::findOrFail($id)->update([
            'nombre' => trim($datos['nombre']),
            'nit'    => trim($datos['nit']),
        ]);
    }

    // ─── Alternar estado activo/inactivo ─────────────────────────
    public static function toggleActivo(int $id): void
    {
        $tenant = self::findOrFail($id);
        $tenant->update(['activo' => !$tenant->activo]);
    }
}

