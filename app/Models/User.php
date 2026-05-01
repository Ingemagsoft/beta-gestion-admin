<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable  // Modelo de usuario que se conecta a la base de datos del tenant activo
{
    use HasApiTokens, HasFactory, Notifiable;

    // ─── Conexión al tenant activo ──────────────────────────────
    protected $connection = 'tenant'; // asegura que este modelo use la conexión tenant
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Listar todos los usuarios del tenant activo ─────────────
    public static function listarTodos()   // Método que obtiene todos los usuarios del tenant activo
    {
        return self::orderBy('name')->get();
    }

    // ─── Crear usuario en el tenant activo ───────────────────────
    public static function crearEnTenant(array $datos): self // Método que crea un usuario en la BD del tenant activo
    {
        return self::create([
            'name'     => trim($datos['name']),
            'email'    => strtolower(trim($datos['email'])),
            'password' => bcrypt($datos['password']),
        ]);
    }    

}