<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Listado de Clientes</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: sans-serif;
    font-size: 11px;
    color: #111111;
    margin: 1.5cm 1.5cm 2cm 1.5cm;
  }

  /* ── PIE DE PÁGINA FIJO ── */
  .footer {
    position: fixed;
    bottom: -1cm;
    left: 0; right: 0;
    border-top: 1px solid #E8E8E8;
    padding: 5px 0;
    font-size: 8.5px;
    color: #AAAAAA;
    background: #FFFFFF;
  }
  .footer table { width: 100%; }
  .footer td { padding: 0; }
  .footer .f-left   { text-align: left;   width: 33%; }
  .footer .f-center { text-align: center; width: 34%; }
  .footer .f-right  { text-align: right;  width: 33%; }

  /* ── CABECERA ── */
  .header-table { width: 100%; margin-bottom: 10px; }
  .header-table td { vertical-align: middle; }
  .header-logo img {
    width: 130px;
    height: auto;
  }
  .header-info { text-align: right; }
  .header-title {
    font-size: 20px;
    font-weight: bold;
    color: #1E3A5F;
    line-height: 1.3;
  }
  .header-sub {
    font-size: 10px;
    color: #666666;
    margin-top: 3px;
  }

  /* ── LÍNEA DIVISORA ── */
  .divider {
    border: none;
    border-top: 3px solid #E8960C;
    margin: 8px 0;
  }

  /* ── INFO DEL REPORTE ── */
  .report-info {
    width: 100%;
    background: #F8FAFC;
    border: 1px solid #E2E0D8;
    padding: 7px 12px;
    margin-bottom: 12px;
  }
  .report-info table { width: 100%; table-layout: fixed; }
  .report-info td {
    font-size: 10px;
    color: #334155;
    padding: 3px 4px;
    word-wrap: break-word;
  }
  .ri-label { font-weight: bold; color: #1E3A5F; width: 140px; }
  .ri-right { text-align: right; color: #666666; width: 300px; }
  .ri-right strong { color: #1E3A5F; }

  /* ── TABLA ── */
  .tabla-clientes {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
    table-layout: fixed;
  }
  .tabla-clientes thead tr { background-color: #1E3A5F; }
  .tabla-clientes thead th {
    color: #FFFFFF;
    font-size: 10px;
    font-weight: bold;
    padding: 8px 7px;
    text-align: left;
    letter-spacing: 0.3px;
  }
  .tabla-clientes tbody tr:nth-child(even) { background-color: #EFF6FF; }
  .tabla-clientes tbody tr:nth-child(odd)  { background-color: #FFFFFF; }
  .tabla-clientes tbody td {
    font-size: 10px;
    padding: 7px 7px;
    color: #334155;
    border-bottom: 0.5px solid #EEEEEE;
    word-wrap: break-word;
  }

  /* ── ANCHOS DE COLUMNAS — letter landscape ── */
  .col-codigo    { width: 9%;  }
  .col-nombre    { width: 24%; }
  .col-tipodoc   { width: 8%;  }
  .col-documento { width: 12%; }
  .col-ciudad    { width: 11%; }
  .col-email     { width: 36%; }

  /* ── ESTILOS DE CELDA ── */
  .td-codigo {
    font-family: monospace;
    font-weight: bold;
    color: #1E3A5F;
    font-size: 9.5px;
  }
  .td-nombre {
    font-weight: bold;
    color: #111111;
  }
  .badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 3px;
    font-size: 9px;
    font-weight: bold;
  }
  .badge-nit  { background: #DBEAFE; color: #1D4ED8; }
  .badge-cc   { background: #DCFCE7; color: #15803D; }
  .badge-otro { background: #F1F5F9; color: #475569; }

  /* ── TOTALES ── */
  .totales {
    text-align: right;
    font-size: 10px;
    color: #334155;
    margin-top: 6px;
  }
  .totales strong { color: #1E3A5F; font-size: 11px; }

  /* ── EMPTY STATE ── */
  .empty {
    text-align: center;
    padding: 40px;
    color: #AAAAAA;
    font-size: 12px;
    border: 1px dashed #CCCCCC;
  }
</style>
</head>
<body>

  {{-- PIE FIJO --}}
  <div class="footer">
    <table>
      <tr>
        <td class="f-left">{{ $empresa }}</td>
        <td class="f-center">IMS Global SAS — Sistema de Gestión Administrativa</td>
        <td class="f-right">Generado: {{ $fechaGeneracion }}</td>
      </tr>
    </table>
  </div>

  {{-- CABECERA --}}
  <table class="header-table">
    <tr>
      <td width="220">
        <img src="{{ public_path('img/logo_ims_color.png') }}" alt="IMS Global"
        style="width:130px; height:auto;"/>
      </td>
      <td class="header-info">
        <div class="header-title">Listado de Clientes</div>
        <div class="header-sub">{{ $empresa }}</div>
        <div class="header-sub">Fecha de generación: {{ $fechaGeneracion }}</div>
      </td>
    </tr>
  </table>

  <hr class="divider"/>

  {{-- INFO DEL REPORTE --}}
  <div class="report-info">
    <table>
      <tr>
        <td class="ri-label">Empresa:</td>
        <td>{{ $empresa }}</td>
        <td class="ri-right">
          @if($busqueda)
            Filtro aplicado: <strong>"{{ $busqueda }}"</strong>
          @else
            Todos los clientes activos
          @endif
        </td>
      </tr>
      <tr>
        <td class="ri-label">Total de clientes:</td>
        <td><strong>{{ $totalClientes }}</strong></td>
        <td class="ri-right">gestion-admin — Laravel 11</td>
      </tr>
    </table>
  </div>

  {{-- TABLA --}}
  @if($clientes->isEmpty())
    <div class="empty">No se encontraron clientes con los criterios aplicados.</div>
  @else
    <table class="tabla-clientes">
      <thead>
        <tr>
          <th class="col-codigo">CÓDIGO</th>
          <th class="col-nombre">NOMBRE / RAZÓN SOCIAL</th>
          <th class="col-tipodoc">TIPO DOC</th>
          <th class="col-documento">DOCUMENTO</th>
          <th class="col-ciudad">CIUDAD</th>
          <th class="col-email">CORREO ELECTRÓNICO</th>
        </tr>
      </thead>
      <tbody>
        @foreach($clientes as $cliente)
        <tr>
          <td class="td-codigo">{{ $cliente->codigo }}</td>
          <td class="td-nombre">{{ $cliente->nombre_completo }}</td>
          <td>
            @php
              $badgeClass = match($cliente->tipo_documento) {
                'NIT'   => 'badge-nit',
                'CC'    => 'badge-cc',
                default => 'badge-otro',
              };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ $cliente->tipo_documento }}</span>
          </td>
          <td>{{ $cliente->documento_formateado }}</td>
          <td>{{ $cliente->ciudad?->nombre ?? '—' }}</td>
          <td>{{ $cliente->email }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="totales">
      Total de registros: <strong>{{ $totalClientes }}</strong>
    </div>
  @endif

</body>
</html>