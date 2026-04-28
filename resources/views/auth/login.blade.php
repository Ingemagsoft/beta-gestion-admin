<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CLICK Pyme — Iniciar sesión</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Sora:wght@400;600;700&display=swap" rel="stylesheet"/>
  @vite(['resources/css/login.css'])
</head>
<body>
<div class="screen">

  {{-- LEFT PANEL --}}
  <div class="left-panel">
    <div class="logo-wrap">
        <img src="{{ asset('img/logo_ims_blanco.png') }}" alt="CLICK Pyme" />
      <!--<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="100" r="94" fill="none" stroke="#E8960C" stroke-width="5.5"/>
        <circle cx="100" cy="100" r="86" fill="#0E0E0E"/>
        <circle cx="100" cy="44" r="11" fill="#E8960C"/>
        <ellipse cx="100" cy="44" rx="5.5" ry="11" fill="none" stroke="#0E0E0E" stroke-width="1.3"/>
        <line x1="89" y1="44" x2="111" y2="44" stroke="#0E0E0E" stroke-width="1.3"/>
        <text x="100" y="118" text-anchor="middle"
          font-family="Georgia, serif" font-size="46" font-weight="700"
          fill="#FFFFFF" letter-spacing="-2">ims</text>
        <text x="100" y="150" text-anchor="middle"
          font-family="'DM Sans', sans-serif" font-size="17" font-weight="300"
          fill="#777777" letter-spacing="4">global</text>
      </svg>-->
    </div>

    <div class="brand-text">
      <div class="brand-product">CLICK <span>Pyme</span></div>
      <div class="brand-sub">Sistema de Gestión Administrativa<br>Ventas · Clientes · Inventario · DIAN</div>
    </div>

    <div class="divider"></div>

    <div class="modules">
      <div class="module-badge">
        <div class="dot dot-orange"></div>
        <span class="module-name">CLICK Pyme — Gestión administrativa</span>
      </div>
      <div class="module-badge">
        <div class="dot dot-green"></div>
        <span class="module-name">CLICK Hotel — Gestión hotelera</span>
      </div>
      <div class="module-badge">
        <div class="dot dot-red"></div>
        <span class="module-name">CLICK Telco — Telecomunicaciones</span>
      </div>
    </div>

    <div class="ims-footer">Ingeniería y Marketing de Software Global SAS</div>
  </div>

  {{-- RIGHT PANEL --}}
  <div class="right-panel">
    <div class="form-inner">
      <div class="welcome-label">BIENVENIDO</div>
      <div class="welcome-title">Inicia sesión<br>en tu cuenta</div>
      <div class="welcome-sub">Ingresa tus credenciales para continuar</div>

      {{-- Errores generales --}}
      @if ($errors->any())
        <div class="alert-error">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}">
        @csrf

        {{-- Código de empresa --}}
        <div class="field">
          <label>CÓDIGO DE EMPRESA</label>
          <input type="text"
                 name="codigo"
                 value="{{ strtoupper(old('codigo')) }}"
                 placeholder="Ej: DEMO"
                 maxlength="20"
                 autocomplete="off"
                 style="text-transform: uppercase;"
                 class="{{ $errors->has('codigo') ? 'error' : '' }}">
          @error('codigo')
            <span class="error-msg">{{ $message }}</span>
          @enderror
        </div>

        {{-- Email --}}
        <div class="field">
          <label>USUARIO</label>
          <input type="email" name="email"
            placeholder="correo@empresa.com"
            value="{{ old('email') }}"
            class="{{ $errors->has('email') ? 'error' : '' }}" />
          @error('email')
            <span class="error-msg">{{ $message }}</span>
          @enderror
        </div>

        {{-- Password --}}
        <div class="field">
          <div class="field-row">
            <label>CONTRASEÑA</label>
          </div>
          <input type="password" name="password"
            placeholder="••••••••"
            class="{{ $errors->has('password') ? 'error' : '' }}" />
          @error('password')
            <span class="error-msg">{{ $message }}</span>
          @enderror
        </div>

        <button type="submit" class="btn-login">Ingresar al sistema</button>
      </form>

      <div class="security-bar">
        <div class="sec-item"><div class="sec-dot"></div> Conexión segura</div>
        <div class="sec-item"><div class="sec-dot"></div> Sesión cifrada</div>
        <div class="sec-item"><div class="sec-dot"></div> Multitenant</div>
      </div>

      <div class="footer-note">
        ¿Problemas para ingresar?
        <a href="mailto:soporte@imsglobal.co">soporte@imsglobal.co</a>
      </div>
    </div>
  </div>

</div>
</body>
</html>