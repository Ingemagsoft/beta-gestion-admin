<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $connection = 'mysql';
    protected $table      = 'admins';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'activo',
    ];

    protected $hidden = [
        'password',
    ];

    // ─── Buscar admin activo por email ───────────────────────────
    public static function buscarActivo(string $email): ?self
    {
        return self::where('email', $email)
                   ->where('activo', true)
                   ->first();
    }
}