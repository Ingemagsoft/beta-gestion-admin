<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AdminUsuarioController extends Controller
{
    // ─── Conectar dinámicamente a la BD del tenant ───────────────
    private function conectarTenant(int $empresa_id): Tenant
    {
        $tenant = Tenant::buscarPorId($empresa_id); // Método que obtiene el tenant por ID

        // Configurar conexión dinámica para el tenant
        Config::set('database.connections.tenant.database', $tenant->db_name);
        Config::set('database.connections.tenant.host',     $tenant->db_host);
        Config::set('database.connections.tenant.port',     $tenant->db_port);
        Config::set('database.connections.tenant.username', $tenant->db_user);
        Config::set('database.connections.tenant.password', $tenant->db_password);

        DB::purge('tenant'); // Limpiar la conexión anterior
        DB::reconnect('tenant'); // Reconectar con la nueva configuración

        return $tenant;     // Retornar el tenant para usar en vistas
    }

    // ─── Listar usuarios del tenant ──────────────────────────────
    public function index(int $empresa_id)  // Recibe el ID de la empresa para conectar al tenant
    {
        $tenant   = $this->conectarTenant($empresa_id);  // Conectar a la BD del tenant
        $usuarios = User::listarTodos();                // Método que obtiene todos los usuarios del tenant

        return view('admin.usuarios.index', compact('tenant', 'usuarios'));    // Pasar el tenant para mostrar su nombre en la vista
    }

    // ─── Formulario nuevo usuario ────────────────────────────────
    public function create(int $empresa_id)   // Recibe el ID de la empresa para conectar al tenant
    {
        $tenant = $this->conectarTenant($empresa_id); // Metodo privado reutilizable, Conectar a la BD del tenant

        return view('admin.usuarios.create', compact('tenant')); // Pasar el tenant para mostrar su nombre en la vista
    }

    // ─── Guardar nuevo usuario ───────────────────────────────────
    public function store(Request $request, int $empresa_id)   
    {
        $tenant = $this->conectarTenant($empresa_id);

        $request->validate([                                // Validación de campos
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:tenant.users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El correo es obligatorio.',
            'email.unique'       => 'Este correo ya está registrado en esta empresa.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        User::crearEnTenant($request->only('name', 'email', 'password'));  // Método que crea un usuario en la BD del tenant

        return redirect()->route('admin.usuarios.index', $empresa_id)
                         ->with('success', 'Usuario creado correctamente.');
    }
}