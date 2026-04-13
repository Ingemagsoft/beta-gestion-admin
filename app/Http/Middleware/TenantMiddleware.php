<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificar que hay una sesión de tenant activa
        if (!session()->has('tenant_db')) {
            return redirect()->route('login')
                ->withErrors(['empresa' => 'Sesión expirada. Por favor inicia sesión nuevamente.']);
        }

        // 2. Obtener el nombre de la BD del tenant desde la sesión
        $tenantDb = session('tenant_db');

        // 3. Configurar la conexión dinámica al tenant
        Config::set('database.connections.tenant.database', $tenantDb);

        // 4. Limpiar la conexión anterior y reconectar
        DB::purge('tenant');
        DB::reconnect('tenant');

        // 5. Establecer tenant como conexión por defecto para este request
        DB::setDefaultConnection('tenant');

        return $next($request);
    }
}