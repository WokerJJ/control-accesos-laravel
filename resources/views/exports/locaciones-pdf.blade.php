<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size:10px; color:#333; }

        .header { background:#17a2b8; color:#fff; padding:12px 16px; margin-bottom:16px; }
        .header h1 { font-size:16px; margin-bottom:2px; }
        .header p  { font-size:9px; opacity:.85; }

        .meta { display:flex; gap:24px; margin-bottom:14px; padding:0 4px; }
        .meta-item label { font-size:8px; text-transform:uppercase; color:#888; display:block; }
        .meta-item span  { font-weight:bold; font-size:11px; }

        .kpis { display:flex; gap:10px; margin-bottom:16px; }
        .kpi  { flex:1; border:1px solid #dee2e6; border-radius:4px; padding:8px; text-align:center; }
        .kpi .val   { font-size:18px; font-weight:bold; color:#17a2b8; }
        .kpi .label { font-size:8px; color:#666; margin-top:2px; }

        table { width:100%; border-collapse:collapse; }
        thead th {
            background:#17a2b8; color:#fff;
            padding:6px 8px; text-align:left;
            font-size:9px; text-transform:uppercase;
        }
        tbody tr:nth-child(even) { background:#f8f9fa; }
        tbody td { padding:5px 8px; border-bottom:1px solid #e9ecef; font-size:9px; }

        .bar-wrap { width:80px; background:#e9ecef; border-radius:2px; height:6px; display:inline-block; vertical-align:middle; }
        .bar-fill  { background:#17a2b8; height:6px; border-radius:2px; display:block; }

        .footer { margin-top:16px; text-align:right; font-size:8px; color:#aaa; }
    </style>
</head>
<body>

<div class="header">
    <h1>Ocupación de Locaciones</h1>
    <p>Control de Accesos — Generado el {{ now('America/Bogota')->isoFormat('D [de] MMMM [de] YYYY, H:mm') }}</p>
</div>

<div class="meta">
    <div class="meta-item">
        <label>Período</label>
        <span>
                {{ \Carbon\Carbon::parse($desde)->isoFormat('D MMM YYYY') }}
                —
                {{ \Carbon\Carbon::parse($hasta)->isoFormat('D MMM YYYY') }}
            </span>
    </div>
    <div class="meta-item">
        <label>Locaciones analizadas</label>
        <span>{{ count($ocupacion) }}</span>
    </div>
</div>

<div class="kpis">
    <div class="kpi">
        <div class="val">{{ $kpis['total_accesos'] }}</div>
        <div class="label">Total accesos</div>
    </div>
    <div class="kpi">
        <div class="val">{{ $kpis['locacion_top'] }}</div>
        <div class="label">Locación más usada</div>
    </div>
    <div class="kpi">
        <div class="val">{{ $kpis['locacion_top_usos'] }}</div>
        <div class="label">Usos locación top</div>
    </div>
    <div class="kpi">
        <div class="val">{{ $kpis['hora_pico_global'] }}</div>
        <div class="label">Hora pico</div>
    </div>
</div>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Locación</th>
        <th>Accesos</th>
        <th>En curso</th>
        <th>Participación</th>
        <th>Días activa</th>
        <th>Duración prom.</th>
        <th>Último acceso</th>
    </tr>
    </thead>
    <tbody>
    @forelse($ocupacion as $i => $loc)
    <tr>
        <td>{{ $i + 1 }}</td>
        <td><strong>{{ $loc->nombre }}</strong></td>
        <td>{{ $loc->total_accesos }}</td>
        <td>{{ $loc->en_curso ?: '—' }}</td>
        <td>
                    <span class="bar-wrap">
                        <span class="bar-fill" style="width:{{ $loc->porcentaje }}%;"></span>
                    </span>
            {{ $loc->porcentaje }}%
        </td>
        <td>{{ $loc->dias_activa }} días</td>
        <td>{{ $loc->duracion_promedio }}</td>
        <td>{{ $loc->ultimo_acceso }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="8" style="text-align:center; padding:16px; color:#888;">Sin registros</td>
    </tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    Total: {{ count($ocupacion) }} locaciones · Sistema de Control de Accesos
</div>

</body>
</html>
