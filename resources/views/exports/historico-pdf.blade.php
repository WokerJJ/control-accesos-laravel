<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }

        .header { background: #007bff; color: #fff; padding: 12px 16px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; margin-bottom: 2px; }
        .header p  { font-size: 9px; opacity: .85; }

        .meta { display: flex; gap: 24px; margin-bottom: 14px; padding: 0 4px; }
        .meta-item label { font-size: 8px; text-transform: uppercase; color: #888; display: block; }
        .meta-item span  { font-weight: bold; font-size: 11px; }

        .kpis { display: flex; gap: 10px; margin-bottom: 16px; }
        .kpi  { flex: 1; border: 1px solid #dee2e6; border-radius: 4px; padding: 8px; text-align: center; }
        .kpi .val   { font-size: 18px; font-weight: bold; color: #007bff; }
        .kpi .label { font-size: 8px; color: #666; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #007bff; color: #fff;
            padding: 6px 8px; text-align: left;
            font-size: 9px; text-transform: uppercase;
        }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #e9ecef; font-size: 9px; }

        .badge {
            display: inline-block; padding: 2px 6px;
            border-radius: 3px; font-size: 8px; font-weight: bold;
        }
        .badge-success    { background: #d4edda; color: #155724; }
        .badge-secondary  { background: #e2e3e5; color: #383d41; }

        .footer { margin-top: 16px; text-align: right; font-size: 8px; color: #aaa; }
    </style>
</head>
<body>

<div class="header">
    <h1>Histórico de Accesos</h1>
    <p>Control de Accesos — Generado el {{ now('America/Bogota')->isoFormat('D [de] MMMM [de] YYYY, H:mm') }}</p>
</div>

<div class="meta">
    <div class="meta-item">
        <label>Período</label>
        <span>{{ \Carbon\Carbon::parse($desde)->isoFormat('D MMM YYYY') }} — {{ \Carbon\Carbon::parse($hasta)->isoFormat('D MMM YYYY') }}</span>
    </div>
    @if($locacion)
    <div class="meta-item">
        <label>Locación</label>
        <span>{{ $locacion }}</span>
    </div>
    @endif
</div>

<div class="kpis">
    <div class="kpi">
        <div class="val">{{ $kpis['total'] }}</div>
        <div class="label">Total accesos</div>
    </div>
    <div class="kpi">
        <div class="val">{{ $kpis['completados'] }}</div>
        <div class="label">Completados</div>
    </div>
    <div class="kpi">
        <div class="val">{{ $kpis['en_curso'] }}</div>
        <div class="label">En curso</div>
    </div>
    <div class="kpi">
        <div class="val">{{ $kpis['duracion_promedio'] }}</div>
        <div class="label">Duración prom.</div>
    </div>
</div>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Persona</th>
        <th>Documento</th>
        <th>Actividad</th>
        <th>Locación</th>
        <th>Ingreso</th>
        <th>Salida</th>
        <th>Duración</th>
        <th>Estado</th>
    </tr>
    </thead>
    <tbody>
    @forelse($accesos as $i => $acceso)
    <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $acceso->persona->primer_nombre }} {{ $acceso->persona->primer_apellido }}</td>
        <td>{{ $acceso->persona->doc_identidad }}</td>
        <td>{{ $acceso->actividad->nombre }}</td>
        <td>{{ $acceso->locacion->nombre }}</td>
        <td>{{ $acceso->hora_ingreso?->format('d/m/Y H:i') }}</td>
        <td>{{ $acceso->hora_salida?->format('H:i') ?? '—' }}</td>
        <td>{{ $acceso->duracion ? $acceso->duracion . ' min' : '—' }}</td>
        <td>
                    <span class="badge {{ $acceso->estado === 'en_curso' ? 'badge-success' : 'badge-secondary' }}">
                        {{ $acceso->estado === 'en_curso' ? 'En curso' : 'Completado' }}
                    </span>
        </td>
    </tr>
    @empty
    <tr><td colspan="9" style="text-align:center; padding:16px; color:#888;">Sin registros</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    Total: {{ count($accesos) }} registros · Sistema de Control de Accesos
</div>

</body>
</html>
