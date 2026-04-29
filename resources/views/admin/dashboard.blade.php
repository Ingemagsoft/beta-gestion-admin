<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS Global — Panel Admin</title>
    @vite(['resources/css/admin.css'])
</head>
<body>

    <header class="admin-header">
        <div class="header-left">
            <img src="{{ asset('img/logo_ims_blanco.png') }}" alt="IMS Global" class="header-logo">
            <span class="header-title">Panel de Administración</span>
        </div>
        <div class="header-right">
            <span class="admin-nombre">{{ session('admin_nombre') }}</span>
            <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <main class="admin-main">

        <h2 class="section-title">Resumen del sistema</h2>

        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number">{{ $stats['total'] }}</span>
                <span class="stat-label">Empresas registradas</span>
            </div>
            <div class="stat-card activas">
                <span class="stat-number">{{ $stats['activas'] }}</span>
                <span class="stat-label">Empresas activas</span>
            </div>
            <div class="stat-card inactivas">
                <span class="stat-number">{{ $stats['inactivas'] }}</span>
                <span class="stat-label">Empresas inactivas</span>
            </div>
        </div>

        <div class="acciones">
            <h2 class="section-title">Gestión de empresas</h2>
            <a href="#" class="btn-accion">+ Nueva empresa</a>
        </div>

    </main>

</body>
</html>