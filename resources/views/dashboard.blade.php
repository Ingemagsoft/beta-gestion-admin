<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — CLICK Pyme</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Sora:wght@400;600;700&display=swap" rel="stylesheet"/>
  @vite(['resources/css/dashboard.css'])
</head>
<body>

  {{-- TOPBAR --}}
  <div class="topbar">
    <div class="topbar-left">
      <div class="topbar-logo">CLICK <span>Pyme</span></div>
      <div class="topbar-empresa">{{ session('tenant_nombre') }}</div>
    </div>
    <div class="topbar-right">
      <div class="topbar-user">
        Hola, <span>{{ session('user_nombre') }}</span>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">Cerrar sesión</button>
      </form>
    </div>
  </div>

  {{-- CONTENT --}}
  <div class="content">

    <div class="page-title">Panel de control</div>
    <div class="page-sub">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</div>

    {{-- STATS --}}
    <div class="cards">
      <div class="card card-accent">
        <div class="card-label">CLIENTES</div>
        <div class="card-value">0</div>
        <div class="card-sub">registrados en el sistema</div>
      </div>
      <div class="card">
        <div class="card-label">VENTAS HOY</div>
        <div class="card-value">0</div>
        <div class="card-sub">transacciones</div>
      </div>
      <div class="card">
        <div class="card-label">PRODUCTOS</div>
        <div class="card-value">0</div>
        <div class="card-sub">en inventario</div>
      </div>
      <div class="card">
        <div class="card-label">FACTURAS DIAN</div>
        <div class="card-value">0</div>
        <div class="card-sub">emitidas este mes</div>
      </div>
    </div>

    {{-- MÓDULOS --}}
    <div class="modules-title">MÓDULOS DEL SISTEMA</div>
    <div class="modules-grid">

      <a href="#" class="module-card">
        <div class="module-icon">👥</div>
        <div class="module-name">Clientes</div>
        <div class="module-desc">Gestión de clientes</div>
      </a>

      <div class="module-card module-soon">
        <div class="module-icon">🛒</div>
        <div class="module-name">Ventas</div>
        <div class="module-desc">Próximamente</div>
      </div>

      <div class="module-card module-soon">
        <div class="module-icon">📦</div>
        <div class="module-name">Inventario</div>
        <div class="module-desc">Próximamente</div>
      </div>

      <div class="module-card module-soon">
        <div class="module-icon">🧾</div>
        <div class="module-name">Facturación DIAN</div>
        <div class="module-desc">Próximamente</div>
      </div>

      <div class="module-card module-soon">
        <div class="module-icon">📊</div>
        <div class="module-name">Reportes</div>
        <div class="module-desc">Próximamente</div>
      </div>

      <div class="module-card module-soon">
        <div class="module-icon">⚙️</div>
        <div class="module-name">Configuración</div>
        <div class="module-desc">Próximamente</div>
      </div>

    </div>
  </div>

</body>
</html>