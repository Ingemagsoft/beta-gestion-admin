<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS Global — Usuarios {{ $tenant->nombre }}</title>
    @vite(['resources/css/admin.css', 'resources/css/admin-empresas.css'])
</head>
<body>

    <header class="admin-header">
        <div class="header-left">
            <img src="{{ asset('img/logo_ims_blanco.png') }}" alt="IMS Global" class="header-logo">
            <span class="header-title">Panel de Administración</span>
        </div>
        <div class="header-right">
            <span class="admin-nombre">{{ session('admin_nombre') }}</span>
            <a href="{{ route('admin.empresas.index') }}" class="btn-nav">← Empresas</a>
            <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <main class="admin-main">

        <div class="page-header">
            <div>
                <h2 class="section-title">Usuarios — {{ $tenant->nombre }}</h2>
                <p class="empresa-meta">Código: <strong>{{ $tenant->codigo }}</strong>
                   &nbsp;|&nbsp; BD: <code>{{ $tenant->db_name }}</code></p>
            </div>
            <a href="{{ route('admin.usuarios.create', $tenant->id) }}" class="btn-accion">+ Nuevo usuario</a>
        </div>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <table class="tabla-empresas">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Creado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="tabla-vacia">No hay usuarios registrados en esta empresa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </main>

</body>
</html>