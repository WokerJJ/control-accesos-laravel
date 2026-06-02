<div class="card card-primary card-outline h-100">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-area me-2"></i>
            Accesos últimos 7 días
        </h3>
    </div>

    <div class="card-body">
        <canvas id="accesosChart"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        var rawData = @json($data ?? []);
        const labels = Object.keys(rawData).map(fecha => {
            const d = new Date(fecha);
            return d.toLocaleDateString('es-CO', {
                day: '2-digit',
                month: 'short'
            });
        });
        const values = Object.values(rawData);

        const ctx = document.getElementById('accesosChart');

        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Accesos',
                    data: values,
                    fill: true,
                    tension: 0.4,
                    borderColor: 'rgba(60, 141, 188, 1)',
                    backgroundColor: 'rgba(60, 141, 188, 0.15)',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

    });
</script>
@endpush
