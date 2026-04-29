<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS Global — Empresas</title>
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
            <a href="{{ route('admin.dashboard') }}" class="btn-nav">Dashboard</a>
            <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <main class="admin-main">

        <div class="page-header">
            <h2 class="section-title">Empresas registradas</h2>
            <a href="{{ route('admin.empresas.create') }}" class="btn-accion">+ Nueva empresa</a>
        </div>

        {{-- Mensaje de éxito --}}
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <table class="tabla-empresas">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Razón social</th>
                    <th>NIT</th>
                    <th>Base de datos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($empresas as $empresa)
                    <tr class="{{ $empresa->activo ? '' : 'fila-inactiva' }}">
                        <td><span class="badge-codigo">{{ $empresa->codigo }}</span></td>
                        <td>{{ $empresa->nombre }}</td>
                        <td>{{ $empresa->nit }}</td>
                        <td><code>{{ $empresa->db_name }}</code></td>
                        <td>
                            <span class="badge-estado {{ $empresa->activo ? 'activo' : 'inactivo' }}">
                                {{ $empresa->activo ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="acciones-col">
                            <a href="{{ route('admin.empresas.edit', $empresa->id) }}" class="btn-editar">Editar</a>

                            <form method="POST"
                                  action="{{ route('admin.empresas.toggle', $empresa->id) }}"
                                  style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-toggle {{ $empresa->activo ? 'desactivar' : 'activar' }}">
                                    {{ $empresa->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="tabla-vacia">No hay empresas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </main>

</body>
</html>