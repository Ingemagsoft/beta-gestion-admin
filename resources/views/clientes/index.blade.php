<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Clientes — CLICK Pyme</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Sora:wght@400;600;700&display=swap" rel="stylesheet"/>
  @vite(['resources/css/clientes.css'])
</head>
<body>

  {{-- TOPBAR --}}
  <div class="topbar">
    <div class="topbar-left">
      <img src="{{ asset('img/logo_ims_blanco.png') }}" alt="IMS Global" style="height:32px;"/>
      <div class="topbar-empresa">{{ session('tenant_nombre') }}</div>
    </div>
    <div class="topbar-right">
      <div class="topbar-user">Hola, <span>{{ session('user_nombre') }}</span></div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">Cerrar sesión</button>
      </form>
    </div>
  </div>

  {{-- CONTENT --}}
  <div class="content">

    {{-- PAGE HEADER --}}
    <div class="page-header">
      <div class="page-header-left">
        <div class="page-title">Clientes</div>
        <div class="page-breadcrumb">
          <a href="{{ route('dashboard') }}">Dashboard</a> / Clientes
        </div>
      </div>
      <a href="{{ route('clientes.create') }}" class="btn-nuevo">
        + Nuevo cliente
      </a>
    </div>

    {{-- MENSAJE DE ÉXITO --}}
    @if(session('success'))
      <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- BARRA DE BÚSQUEDA --}}
    <form method="GET" action="{{ route('clientes.index') }}">
      <div class="search-bar">
        <input
          type="text"
          name="busqueda"
          class="search-input"
          placeholder="Buscar por nombre, razón social, NIT o código..."
          value="{{ $busqueda }}"
          autofocus
        />
        <button type="submit" class="btn-buscar">Buscar</button>
        @if($busqueda)
          <a href="{{ route('clientes.index') }}" class="btn-accion btn-editar">Limpiar</a>
        @endif
      </div>
    </form>

    {{-- RESULTADOS --}}
    @if($busqueda === '')

      {{-- Estado inicial — sin búsqueda --}}
      <div class="no-search">
        <div class="no-search-icon">🔍</div>
        <div class="no-search-title">Busca un cliente para comenzar</div>
        <div class="no-search-sub">
          Escribe un nombre, razón social, NIT o código y presiona Buscar.<br>
          Para crear un nuevo cliente usa el botón "+ Nuevo cliente".
        </div>
      </div>

    @elseif($clientes->count() === 0)

      {{-- Sin resultados --}}
      <div class="table-wrap">
        <div class="empty-state">
          <div class="empty-icon">👥</div>
          <div class="empty-title">No se encontraron clientes</div>
          <div class="empty-sub">No hay resultados para "{{ $busqueda }}"</div>
        </div>
      </div>

    @else

      {{-- Tabla de resultados --}}
      <div class="table-wrap">
        <div class="table-header">
          <span class="table-title">Resultados para "{{ $busqueda }}"</span>
          <span class="table-count">{{ $clientes->total() }} cliente(s) encontrado(s)</span>
        </div>

        <table>
          <thead>
            <tr>
              <th>CÓDIGO</th>
              <th>NOMBRE / RAZÓN SOCIAL</th>
              <th>DOCUMENTO</th>
              <th>CIUDAD</th>
              <th>CORREO</th>
              <th>ACCIONES</th>
            </tr>
          </thead>
          <tbody>
            @foreach($clientes as $cliente)
            <tr>
              <td class="td-codigo">{{ $cliente->codigo }}</td>
              <td class="td-nombre">{{ $cliente->nombre_completo }}</td>
              <td class="td-documento">
                <span class="badge {{ $cliente->tipo_documento === 'NIT' ? 'badge-nit' : 'badge-cc' }}">
                  {{ $cliente->tipo_documento }}
                </span>
                {{ $cliente->documento_formateado }}
              </td>
              <td>{{ $cliente->ciudad?->nombre ?? '—' }}</td>
              <td>{{ $cliente->email }}</td>
              <td>
                <div class="acciones">
                  <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn-accion btn-editar">
                    Editar
                  </a>
                  <form method="POST" action="{{ route('clientes.destroy', $cliente->id) }}"
                    onsubmit="return confirm('¿Desactivar este cliente?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-accion btn-eliminar">Desactivar</button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>

        {{-- Paginación --}}
        @if($clientes->hasPages())
          <div class="pagination-wrap">
            {{ $clientes->links() }}
          </div>
        @endif

      </div>

    @endif

  </div>
</body>
</html>