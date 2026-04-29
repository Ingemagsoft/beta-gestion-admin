<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS Global — Administración</title>
    @vite(['resources/css/admin-login.css'])
</head>
<body>
    <div class="login-wrapper">

        <div class="login-box">
            <div class="login-logo">
                <img src="{{ asset('img/logo_ims_color.png') }}" alt="IMS Global">
            </div>

            <h1>Panel de Administración</h1>
            <p class="subtitle">Acceso exclusivo equipo IMS Global</p>

            {{-- Errores generales --}}
            @if ($errors->any())
                <div class="alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf

                <div class="field">
                    <label>CORREO</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="correo@imsglobal.co"
                           autocomplete="off">
                    @error('email')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label>CONTRASEÑA</label>
                    <input type="password"
                           name="password"
                           placeholder="••••••••">
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Ingresar</button>

            </form>
        </div>

    </div>
</body>
</html>