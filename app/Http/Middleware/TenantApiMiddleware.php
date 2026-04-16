<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class TenantApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificar que el token existe en la petición
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Token de acceso requerido.',
            ], 401);
        }

        // 2. Obtener el usuario autenticado via Sanctum
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado.',
            ], 401);
        }

        // 3. Extraer el tenant_id del token
        //    Lo guardamos en "abilities" al momento de crear el token
        $tokenAbilities = $user->currentAccessToken()->abilities;
        $tenantId = null;

        foreach ($tokenAbilities as $ability) {
            if (str_starts_with($ability, 'tenant:')) {
                $tenantId = (int) str_replace('tenant:', '', $ability);
                break;
            }
        }

        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Token sin empresa asociada.',
            ], 401);
        }

        // 4. Buscar la empresa en la BD central
        $tenant = Tenant::find($tenantId);

        if (!$tenant || !$tenant->activo) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa no encontrada o inactiva.',
            ], 403);
        }

        // 5. Conectar a la BD del tenant
        Config::set('database.connections.tenant.database', $tenant->db_name);
        Config::set('database.connections.tenant.host',     $tenant->db_host);
        Config::set('database.connections.tenant.port',     $tenant->db_port);
        Config::set('database.connections.tenant.username', $tenant->db_user);
        Config::set('database.connections.tenant.password', $tenant->db_password);

        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');
        Config::set('database.default', 'tenant');

        // 6. Compartir el tenant con el resto de la petición
        $request->merge(['tenant' => $tenant]);

        return $next($request);
    }
}