<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // ─── Mostrar formulario de login admin ──────────────────────
    public function showLogin()
    {
        if (session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    // ─── Procesar login admin ────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'El correo no tiene un formato válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        // Buscar admin en BD central — lógica en el modelo
        $admin = Admin::buscarActivo($request->email);

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->withErrors([
                'login' => 'Credenciales incorrectas.',
            ])->withInput($request->only('email'));
        }

        session([
            'admin_id'     => $admin->id,
            'admin_nombre' => $admin->nombre,
            'admin_email'  => $admin->email,
        ]);

        return redirect()->route('admin.dashboard');
    }

    // ─── Cerrar sesión admin ─────────────────────────────────────
    public function logout(Request $request)
    {
        session()->forget(['admin_id', 'admin_nombre', 'admin_email']);
        return redirect()->route('admin.login');
    }
}