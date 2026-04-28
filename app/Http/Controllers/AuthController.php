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
    
        return view('auth.login');
    }

    // ─── Procesar el login ───────────────────────────────────────
    public function login(Request $request)
    {
        // 1. Validar los datos del formulario
        $request->validate([
            'codigo'   => 'required|string|max:20',
            'email'     => 'required|email',
            'password'  => 'required|min:6',
        ], [
            'codigo.required' => 'El código de empresa es obligatorio.',
            'codigo.string'   => 'El código de empresa no es válido.',
            'email.required'     => 'El correo es obligatorio.',
            'email.email'        => 'El correo no tiene un formato válido.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        // 2. Buscar la empresa en la BD central
        $tenant = Tenant::where('codigo', strtoupper(trim($request->codigo)))
                        ->where('activo', true)
                        ->first();

        if (!$tenant) {
            return back()->withErrors([
                'login' => 'Credenciales incorrectas'
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
            ])->withInput($request->only('email', 'codigo'));
        }

        // 5. Guardar sesión del tenant y usuario
        session([
            'tenant_id'     => $tenant->id,
            'tenant_codigo' => $tenant->codigo,
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