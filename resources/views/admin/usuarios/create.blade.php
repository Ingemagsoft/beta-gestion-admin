<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS Global — Nuevo usuario</title>
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
            <a href="{{ route('admin.usuarios.index', $tenant->id) }}" class="btn-nav">← Volver</a>
            <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <main class="admin-main">

        <h2 class="section-title">Nuevo usuario — {{ $tenant->nombre }}</h2>

        <div class="info-card">
            <p><strong>Empresa:</strong> {{ $tenant->nombre }}</p>
            <p><strong>Código:</strong> {{ $tenant->codigo }}</p>
        </div>

        <div class="form-card">
            <form method="POST" action="{{ route('admin.usuarios.store', $tenant->id) }}">
                @csrf

                <div class="form-grid">

                    <div class="field">
                        <label>NOMBRE COMPLETO <span class="requerido">*</span></label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Ej: Juan Pérez"
                               class="{{ $errors->has('name') ? 'error' : '' }}">
                        @error('name')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label>CORREO <span class="requerido">*</span></label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Ej: juan@empresa.com"
                               class="{{ $errors->has('email') ? 'error' : '' }}">
                        @error('email')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label>CONTRASEÑA <span class="requerido">*</span></label>
                        <input type="password"
                               name="password"
                               placeholder="Mínimo 6 caracteres"
                               class="{{ $errors->has('password') ? 'error' : '' }}">
                        @error('password')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label>CONFIRMAR CONTRASEÑA <span class="requerido">*</span></label>
                        <input type="password"
                               name="password_confirmation"
                               placeholder="Repetir contraseña">
                    </div>

                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.usuarios.index', $tenant->id) }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Crear usuario</button>
                </div>

            </form>
        </div>

    </main>

</body>
</html>