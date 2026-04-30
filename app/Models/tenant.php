<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class Tenant extends Model
{
    protected $fillable = [
        'codigo',
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

    // ─── Crear BD y correr migraciones del tenant ────────────────
    public static function crearBaseDeDatos(self $tenant): void
    {
        // Crear la BD
        DB::statement("CREATE DATABASE `{$tenant->db_name}` 
                       CHARACTER SET utf8mb4 
                       COLLATE utf8mb4_unicode_ci");

        // Apuntar la conexión tenant a la nueva BD
        Config::set('database.connections.tenant.database', $tenant->db_name);
        Config::set('database.connections.tenant.host',     $tenant->db_host);
        Config::set('database.connections.tenant.port',     $tenant->db_port);
        Config::set('database.connections.tenant.username', $tenant->db_user);
        Config::set('database.connections.tenant.password', $tenant->db_password);

        DB::purge('tenant');
        DB::reconnect('tenant');

        // Correr migraciones sobre la nueva BD
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path'     => 'database/migrations/tenant',
            '--force'    => true,
        ]);
    }
}

