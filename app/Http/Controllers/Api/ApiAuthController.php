<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    // ─── Login ───────────────────────────────────────────────────
    public function login(Request $request)
    {
        // 1. Validar los datos recibidos
        $request->validate([
            'tenant_id' => 'required|exists:mysql.tenants,id',
            'email'     => 'required|email',
            'password'  => 'required',
        ], [
            'tenant_id.required' => 'Debes indicar la empresa.',
            'tenant_id.exists'   => 'La empresa no existe.',
            'email.required'     => 'El correo es obligatorio.',
            'email.email'        => 'El formato del correo no es válido.',
            'password.required'  => 'La contraseña es obligatoria.',
        ]);

        // 2. Buscar la empresa en la BD central
        $tenant = Tenant::on('mysql')
                        ->where('id', $request->tenant_id)
                        ->where('activo', true)
                        ->first();

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa no encontrada o inactiva.',
            ], 403);
        }

        // 3. Conectar a la BD del tenant
        Config::set('database.connections.tenant.database', $tenant->db_name);
        Config::set('database.connections.tenant.host',     $tenant->db_host);
        Config::set('database.connections.tenant.port',     $tenant->db_port);
        Config::set('database.connections.tenant.username', $tenant->db_user);
        Config::set('database.connections.tenant.password', $tenant->db_password);

        DB::purge('tenant');
        DB::reconnect('tenant');

        // 4. Buscar el usuario en la BD del tenant
        $user = User::on('tenant')
                    ->where('email', $request->email)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Correo o contraseña incorrectos.',
            ], 401);
        }

        // 5. Revocar tokens anteriores del mismo dispositivo (opcional)
        $user->tokens()->where('name', $request->device_name ?? 'mobile')->delete();

        // 6. Crear token con tenant embebido — expira en 24 horas
        $token = $user->createToken(
            $request->device_name ?? 'mobile',
            ["tenant:{$tenant->id}"],
            now()->addHours(24)
        );

        // 7. Devolver token y datos del usuario
        return response()->json([
            'success' => true,
            'message' => 'Login exitoso.',
            'token'   => $token->plainTextToken,
            'user'    => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'tenant_id'      => $tenant->id,
                'tenant_nombre'  => $tenant->nombre,
            ],
            'expires_at' => now()->addHours(24)->toDateTimeString(),
        ], 200);
    }

    // ─── Datos del usuario activo ─────────────────────────────────
    public function me(Request $request)
    {
        $user   = $request->user();
        $tenant = $request->tenant;

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'tenant_id'     => $tenant->id,
                'tenant_nombre' => $tenant->nombre,
            ],
        ], 200);
    }

    // ─── Logout ───────────────────────────────────────────────────
    public function logout(Request $request)
    {
        // Obtener el token desde el header y eliminarlo directamente de la BD
        $bearerToken = $request->bearerToken();
        $tokenId     = explode('|', $bearerToken, 2)[0];
    
        DB::connection('tenant')
          ->table('personal_access_tokens')
          ->where('id', $tokenId)
          ->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.',
        ], 200);
    }
}