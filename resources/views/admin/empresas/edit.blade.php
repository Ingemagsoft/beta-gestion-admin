<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS Global — Editar empresa</title>
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
            <a href="{{ route('admin.empresas.index') }}" class="btn-nav">← Volver</a>
            <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <main class="admin-main">

        <h2 class="section-title">Editar empresa — {{ $empresa->codigo }}</h2>

        {{-- Campos de solo lectura — no se editan ─────────────── --}}
        <div class="info-card">
            <p><strong>Código:</strong> {{ $empresa->codigo }}</p>
            <p><strong>Base de datos:</strong> {{ $empresa->db_name }}</p>
            <p class="info-nota">El código y la base de datos no se pueden modificar — son identificadores permanentes de la empresa.</p>
        </div>

        <div class="form-card">
            <form method="POST" action="{{ route('admin.empresas.update', $empresa->id) }}">
                @csrf
                @method('PUT')

                <div class="form-grid">

                    <div class="field">
                        <label>RAZÓN SOCIAL <span class="requerido">*</span></label>
                        <input type="text"
                               name="nombre"
                               value="{{ old('nombre', $empresa->nombre) }}"
                               class="{{ $errors->has('nombre') ? 'error' : '' }}">
                        @error('nombre')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label>NIT <span class="requerido">*</span></label>
                        <input type="text"
                               name="nit"
                               value="{{ old('nit', $empresa->nit) }}"
                               class="{{ $errors->has('nit') ? 'error' : '' }}">
                        @error('nit')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.empresas.index') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Guardar cambios</button>
                </div>

            </form>
        </div>

    </main>

</body>
</html>