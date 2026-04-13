<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;

class AuthController extends Controller
{
    // ─── Mostrar formulario de login ────────────────────────────
    public function showLogin()
    {
        // Si ya hay sesión activa, ir directo al dashboard
        if (session()->has('user_id')) {
            return redirect()->route('dashboard');
        }

        // Cargar lista de empresas activas para el selector
        $empresas = Tenant::where('activo', true)
                          ->orderBy('nombre')
                          ->get(['id', 'nombre']);

        return view('auth.login', compact('empresas'));
    }

    // ─── Procesar el login ───────────────────────────────────────
    public function login(Request $request)
    {
        // 1. Validar los datos del formulario
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'email'     => 'required|email',
            'password'  => 'required|min:6',
        ], [
            'tenant_id.required' => 'Debes seleccionar una empresa.',
            'tenant_id.exists'   => 'La empresa seleccionada no existe.',
            'email.required'     => 'El correo es obligatorio.',
            'email.email'        => 'El correo no tiene un formato válido.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        // 2. Buscar la empresa en la BD central
        $tenant = Tenant::where('id', $request->tenant_id)
                        ->where('activo', true)
                        ->first();

        if (!$tenant) {
            return back()->withErrors([
                'empresa' => 'Empresa no encontrada o inactiva.'
            ])->withInput();
        }

        // 3. Conectar dinámicamente a la BD del tenant
        Config::set('database.connections.tenant.database', $tenant->db_name);
        Config::set('database.connections.tenant.host',     $tenant->db_host);
        Config::set('database.connections.tenant.port',     $tenant->db_port);
        Config::set('database.connections.tenant.username', $tenant->db_user);
        Config::set('database.connections.tenant.password', $tenant->db_password);

        DB::purge('tenant');
        DB::reconnect('tenant');

        // 4. Buscar el usuario en la BD del tenant
        $usuario = DB::connection('tenant')
                     ->table('users')
                     ->where('email', $request->email)
                     ->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return back()->withErrors([
                'email' => 'Correo o contraseña incorrectos.'
            ])->withInput($request->only('email', 'tenant_id'));
        }

        // 5. Guardar sesión del tenant y usuario
        session([
            'tenant_id'     => $tenant->id,
            'tenant_db'     => $tenant->db_name,
            'tenant_nombre' => $tenant->nombre,
            'user_id'       => $usuario->id,
            'user_nombre'   => $usuario->name,
            'user_email'    => $usuario->email,
        ]);

        return redirect()->route('dashboard');
    }

    // ─── Cerrar sesión ───────────────────────────────────────────
    public function logout(Request $request)
    {
        session()->flush();
        return redirect()->route('login');
    }
}