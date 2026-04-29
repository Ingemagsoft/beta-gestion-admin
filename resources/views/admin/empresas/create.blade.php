<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS Global — Nueva empresa</title>
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

        <h2 class="section-title">Nueva empresa</h2>

        <div class="form-card">
            <form method="POST" action="{{ route('admin.empresas.store') }}">
                @csrf

                <div class="form-grid">

                    <div class="field">
                        <label>CÓDIGO DE EMPRESA <span class="requerido">*</span></label>
                        <input type="text"
                               name="codigo"
                               value="{{ old('codigo') }}"
                               placeholder="Ej: PSJ"
                               maxlength="20"
                               style="text-transform: uppercase;"
                               class="{{ $errors->has('codigo') ? 'error' : '' }}">
                        <span class="field-hint">Mayúsculas. Ejemplo: PSJ, HLM, CMN</span>
                        @error('codigo')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label>RAZÓN SOCIAL <span class="requerido">*</span></label>
                        <input type="text"
                               name="nombre"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Smooth Jeans S.A.S"
                               class="{{ $errors->has('nombre') ? 'error' : '' }}">
                        @error('nombre')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label>NIT <span class="requerido">*</span></label>
                        <input type="text"
                               name="nit"
                               value="{{ old('nit') }}"
                               placeholder="Ej: 900111222-3"
                               class="{{ $errors->has('nit') ? 'error' : '' }}">
                        @error('nit')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label>NOMBRE DE BASE DE DATOS <span class="requerido">*</span></label>
                        <input type="text"
                               name="db_name"
                               value="{{ old('db_name') }}"
                               placeholder="Ej: tenant_smoothjeans"
                               class="{{ $errors->has('db_name') ? 'error' : '' }}">
                        <span class="field-hint">Convención: tenant_ + nombre descriptivo en minúsculas</span>
                        @error('db_name')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.empresas.index') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Registrar empresa</button>
                </div>

            </form>
        </div>

    </main>

</body>
</html>