<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class TenantApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificar que viene un token
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token de acceso requerido.',
            ], 401);
        }

        // 2. Decodificar el token — formato: "ID|hash"
        $tokenParts = explode('|', $bearerToken, 2);

        if (count($tokenParts) !== 2) {
            return response()->json([
                'success' => false,
                'message' => 'Formato de token inválido.',
            ], 401);
        }

        $tokenId   = $tokenParts[0];
        $tokenHash = hash('sha256', $tokenParts[1]);

        // 3. Buscar el token en TODAS las BDs de tenants activos
        //    Para esta beta con un solo tenant, buscamos directo en tenant_demo
        //    En producción esto se optimiza con un índice de tokens en la BD central
        $tenants = Tenant::on('mysql')->where('activo', true)->get();

        $accessToken = null;
        $tenant      = null;

        foreach ($tenants as $t) {
            // Configurar conexión al tenant
            Config::set('database.connections.tenant.database', $t->db_name);
            Config::set('database.connections.tenant.host',     $t->db_host);
            Config::set('database.connections.tenant.port',     $t->db_port);
            Config::set('database.connections.tenant.username', $t->db_user);
            Config::set('database.connections.tenant.password', $t->db_password);
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Buscar el token en esta BD
            $found = DB::connection('tenant')
                       ->table('personal_access_tokens')
                       ->where('id', $tokenId)
                       ->where('token', $tokenHash)
                       ->first();

            if ($found) {
                $accessToken = $found;
                $tenant      = $t;
                break;
            }
        }

        // 4. Verificar que el token existe
        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido.',
            ], 401);
        }

        // 5. Verificar que el token no ha expirado
        if ($accessToken->expires_at && now()->isAfter($accessToken->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Token expirado. Por favor inicia sesión nuevamente.',
            ], 401);
        }

        // 6. Establecer la conexión del tenant encontrado como default
        Config::set('database.connections.tenant.database', $tenant->db_name);
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');
        Config::set('database.default', 'tenant');

        // 7. Cargar el usuario y autenticarlo en Laravel
        $user = \App\Models\User::find($accessToken->tokenable_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.',
            ], 401);
        }

        // 8. Actualizar last_used_at del token
        DB::connection('tenant')
          ->table('personal_access_tokens')
          ->where('id', $tokenId)
          ->update(['last_used_at' => now()]);

        // 9. Autenticar el usuario en el request
        Auth::setUser($user);

        // 10. Compartir el tenant con el resto de la petición
        $request->merge(['tenant' => $tenant]);

        return $next($request);
    }
}