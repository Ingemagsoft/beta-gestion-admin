<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nuevo Cliente — CLICK Pyme</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Sora:wght@400;600;700&display=swap" rel="stylesheet"/>
  @vite(['resources/css/clientes.css', 'resources/css/clientes-reg.css'])
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
        <div class="page-title">Nuevo cliente</div>
        <div class="page-breadcrumb">
          <a href="{{ route('dashboard') }}">Dashboard</a> /
          <a href="{{ route('clientes.index') }}">Clientes</a> /
          Nuevo
        </div>
      </div>
    </div>

    {{-- ERRORES --}}
    @if($errors->any())
      <div class="alert-error-box">
        <strong>Por favor corrige los siguientes errores:</strong>
        <ul>
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('clientes.store') }}">
      @csrf

      {{-- BLOQUE PRINCIPAL --}}
      <div class="form-card">
        <div class="form-card-header">
          <span class="form-card-title">Datos de identificación</span>
        </div>
        <div class="form-card-body">

          {{-- Fila 1: Código y Tipo cliente --}}
          <div class="form-grid form-grid-2" style="margin-bottom:16px;">
            <div class="field field-required">
              <label>CÓDIGO</label>
              <input type="text" name="codigo" id="codigo"
                value="{{ old('codigo') }}"
                placeholder="Ej: CLI001"
                class="{{ $errors->has('codigo') ? 'is-error' : '' }}"
                autocomplete="off"/>
              @error('codigo')
                <span class="error-msg">{{ $message }}</span>
              @enderror
            </div>
            <div class="field field-required">
              <label>TIPO DE CLIENTE</label>
              <select name="tipo_cliente">
                <option value="Cliente" selected>Cliente</option>
              </select>
            </div>
          </div>

          {{-- Fila 2: Número documento + DV --}}
          <div class="form-grid form-grid-2" style="margin-bottom:16px;">
            <div class="field field-required">
              <label>NRO. NIT / CC / OTRO</label>
              <input type="text" name="numero_documento" id="numero_documento"
                value="{{ old('numero_documento') }}"
                placeholder="Sin puntos ni espacios"
                class="{{ $errors->has('numero_documento') ? 'is-error' : '' }}"
                autocomplete="off"/>
              @error('numero_documento')
                <span class="error-msg">{{ $message }}</span>
              @enderror
            </div>
            <div class="field">
              <label>CON DV (DÍGITO DE VERIFICACIÓN)</label>
              <input type="text" name="digito_verificacion" id="digito_verificacion"
                value="{{ old('digito_verificacion') }}"
                placeholder="Ej: 1"
                maxlength="1"
                autocomplete="off"/>
            </div>
          </div>

            {{-- Tipo de documento visible --}}
            <div class="form-grid form-grid-2" style="margin-bottom:16px;">
              <div class="field field-required">
                <label>TIPO DE DOCUMENTO</label>
                <select name="tipo_documento" id="tipo_documento">
                  <option value="CC"  {{ old('tipo_documento', 'CC') === 'CC'  ? 'selected' : '' }}>Cédula de ciudadanía</option>
                  <option value="TI"  {{ old('tipo_documento') === 'TI'  ? 'selected' : '' }}>Tarjeta de identidad</option>
                  <option value="CE"  {{ old('tipo_documento') === 'CE'  ? 'selected' : '' }}>Cédula de extranjería</option>
                  <option value="NIT" {{ old('tipo_documento') === 'NIT' ? 'selected' : '' }}>NIT</option>
                  <option value="Otro"{{ old('tipo_documento') === 'Otro'? 'selected' : '' }}>Otro</option>
                </select>
              </div>
            </div>

            {{-- Persona JURÍDICA — visible si hay DV --}}
            <div id="bloque-juridico" class="field-hidden">

              {{-- Razón social --}}
              <div class="form-grid form-grid-2" style="margin-bottom:16px;">
                <div class="field">
                  <label>RAZÓN SOCIAL / NOMBRE ESTABLECIMIENTO</label>
                  <input type="text" name="razon_social"
                    value="{{ old('razon_social') }}"
                    placeholder="Razón social de la empresa"/>
                </div>
              </div>

              {{-- Representante legal --}}
              <div style="margin-bottom:8px;">
                <span style="font-size:11px;font-weight:600;color:#888888;letter-spacing:0.5px;">
                  REPRESENTANTE LEGAL
                </span>
              </div>
              <div class="form-grid form-grid-2" style="margin-bottom:16px;">
                <div class="field">
                  <label>PRIMER NOMBRE</label>
                  <input type="text" name="rep_legal_primer_nombre"
                    value="{{ old('rep_legal_primer_nombre') }}"
                    placeholder="Primer nombre"/>
                </div>
                <div class="field">
                  <label>SEGUNDO NOMBRE</label>
                  <input type="text" name="rep_legal_segundo_nombre"
                    value="{{ old('rep_legal_segundo_nombre') }}"
                    placeholder="Segundo nombre"/>
                </div>
              </div>
              <div class="form-grid form-grid-2" style="margin-bottom:16px;">
                <div class="field">
                  <label>PRIMER APELLIDO</label>
                  <input type="text" name="rep_legal_primer_apellido"
                    value="{{ old('rep_legal_primer_apellido') }}"
                    placeholder="Primer apellido"/>
                </div>
                <div class="field">
                  <label>SEGUNDO APELLIDO</label>
                  <input type="text" name="rep_legal_segundo_apellido"
                    value="{{ old('rep_legal_segundo_apellido') }}"
                    placeholder="Segundo apellido"/>
                </div>
              </div>
              <div class="form-grid form-grid-2" style="margin-bottom:16px;">
                <div class="field">
                  <label>NRO. DOCUMENTO REP. LEGAL</label>
                  <input type="text" name="rep_legal_documento"
                    value="{{ old('rep_legal_documento') }}"
                    placeholder="Número de documento"/>
                </div>
              </div>

            </div>


          {{-- Persona NATURAL — visible si NO hay DV --}}
          <div id="bloque-natural">
            <div class="form-grid form-grid-2" style="margin-bottom:16px;">
              <div class="field field-required">
                <label>PRIMER APELLIDO</label>
                <input type="text" name="primer_apellido"
                  value="{{ old('primer_apellido') }}"
                  placeholder="Primer apellido"
                  class="{{ $errors->has('primer_apellido') ? 'is-error' : '' }}"/>
                @error('primer_apellido')
                  <span class="error-msg">{{ $message }}</span>
                @enderror
              </div>
              <div class="field">
                <label>SEGUNDO APELLIDO</label>
                <input type="text" name="segundo_apellido"
                  value="{{ old('segundo_apellido') }}"
                  placeholder="Segundo apellido"/>
              </div>
            </div>
            <div class="form-grid form-grid-2" style="margin-bottom:16px;">
              <div class="field field-required">
                <label>PRIMER NOMBRE</label>
                <input type="text" name="primer_nombre"
                  value="{{ old('primer_nombre') }}"
                  placeholder="Primer nombre"
                  class="{{ $errors->has('primer_nombre') ? 'is-error' : '' }}"/>
                @error('primer_nombre')
                  <span class="error-msg">{{ $message }}</span>
                @enderror
              </div>
              <div class="field">
                <label>SEGUNDO NOMBRE</label>
                <input type="text" name="segundo_nombre"
                  value="{{ old('segundo_nombre') }}"
                  placeholder="Segundo nombre"/>
              </div>
            </div>
          </div>

          {{-- Email --}}
          <div class="form-grid form-grid-2">
            <div class="field field-required">
              <label>CORREO ELECTRÓNICO</label>
              <input type="email" name="email"
                value="{{ old('email') }}"
                placeholder="correo@empresa.com"
                class="{{ $errors->has('email') ? 'is-error' : '' }}"/>
              @error('email')
                <span class="error-msg">{{ $message }}</span>
              @enderror
            </div>
          </div>

        </div>
      </div>

      {{-- PESTAÑA A — DIRECCIÓN --}}
      <div class="form-card">
        <div class="form-card-header">
          <span class="form-card-title">A. Dirección</span>
        </div>
        <div class="form-card-body">
          <div class="form-grid form-grid-2" style="margin-bottom:16px;">
            <div class="field field-required">
              <label>DIRECCIÓN</label>
              <input type="text" name="direccion"
                value="{{ old('direccion') }}"
                placeholder="Dirección completa"
                class="{{ $errors->has('direccion') ? 'is-error' : '' }}"/>
              @error('direccion')
                <span class="error-msg">{{ $message }}</span>
              @enderror
            </div>
            <div class="field">
              <label>CIUDAD</label>
              <select name="ciudad_id">
                <option value="">Sin ciudad</option>
                @foreach($ciudades as $ciudad)
                  <option value="{{ $ciudad->id }}"
                    {{ old('ciudad_id', $ciudadDefault?->id) == $ciudad->id ? 'selected' : '' }}>
                    {{ $ciudad->nombre }}
                    @if($ciudad->departamento) — {{ $ciudad->departamento }} @endif
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-grid form-grid-2">
            <div class="field">
              <label>TELÉFONO FIJO</label>
              <input type="text" name="telefono"
                value="{{ old('telefono') }}"
                placeholder="Sin espacios ni comas"/>
            </div>
            <div class="field">
              <label>CELULAR</label>
              <input type="text" name="celular"
                value="{{ old('celular') }}"
                placeholder="Sin espacios ni comas"/>
            </div>
          </div>
        </div>
      </div>

      {{-- ACCIONES --}}
      <div class="form-card">
        <div class="form-actions">
          <a href="{{ route('clientes.index') }}" class="btn-salir">Salir</a>
          <button type="submit" class="btn-grabar">Grabar cliente</button>
        </div>
      </div>

    </form>
  </div>

<script>
  const dvInput        = document.getElementById('digito_verificacion');
  const codigoInput    = document.getElementById('codigo');
  const numDocInput    = document.getElementById('numero_documento');
  const tipoDocSelect  = document.getElementById('tipo_documento');
  const bloqueJuridico = document.getElementById('bloque-juridico');
  const bloqueNatural  = document.getElementById('bloque-natural');

  // Autorelleno: código → número de documento
  codigoInput.addEventListener('blur', function () {
    if (numDocInput.value === '') {
      numDocInput.value = this.value;
    }
  });

  // Cambio dinámico según dígito de verificación
  function actualizarModo() {
    const tieneDV = dvInput.value.trim() !== '';

    if (tieneDV) {
      bloqueJuridico.classList.remove('field-hidden');
      bloqueNatural.classList.add('field-hidden');
      tipoDocSelect.value = 'NIT';
    } else {
      bloqueJuridico.classList.add('field-hidden');
      bloqueNatural.classList.remove('field-hidden');
      // Solo cambia a CC si estaba en NIT
      if (tipoDocSelect.value === 'NIT') {
        tipoDocSelect.value = 'CC';
      }
    }
  }

  dvInput.addEventListener('input', actualizarModo);

  // Estado inicial al cargar la página
  actualizarModo();
</script>

</body>
</html>