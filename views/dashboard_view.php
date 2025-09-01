<?php
// =================================================================
// Vista del Panel Principal (Dashboard)
// =================================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - <?php echo SITE_NAME; ?></title>
    <!-- Aquí iría un CSS común para el panel de administración -->
</head>
<body>

    <!-- Aquí iría una barra de navegación/menú lateral común -->
    <nav>
        <a href="index.php?view=dashboard">Panel</a> |
        <a href="index.php?view=clientes">Clientes</a> |
        <a href="index.php?view=cursos">Cursos</a> |
        <a href="index.php?view=matriculas">Matrículas</a> |
        <a href="index.php?view=usuarios">Usuarios (Admin)</a> |
        <a href="index.php?view=logout">Cerrar Sesión</a>
    </nav>

    <hr>

    <h1>Panel Principal</h1>

    <div class="filters-container">
        <form action="index.php" method="GET">
            <input type="hidden" name="view" value="dashboard">
            <div class="form-group">
                <label for="anio">Año:</label>
                <select name="anio" id="anio">
                    <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == $anio_seleccionado) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="mes">Mes (para Gráfico Circular):</label>
                <select name="mes" id="mes">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == $mes_seleccionado) ? 'selected' : ''; ?>><?php echo DateTime::createFromFormat('!m', $i)->format('F'); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit">Filtrar</button>
        </form>
    </div>

    <div class="charts-container">
        <div class="chart-card">
            <h3>Ventas Mensuales por Curso (<?php echo DateTime::createFromFormat('!m', $mes_seleccionado)->format('F'); ?> <?php echo $anio_seleccionado; ?>)</h3>
            <canvas id="pieChartVentas"></canvas>
        </div>
        <div class="chart-card">
            <h3>Ventas Mensuales (<?php echo $anio_seleccionado; ?>)</h3>
            <canvas id="barChartVentas"></canvas>
        </div>
    </div>

    <!-- Incluir Chart.js desde un CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Gráfico Circular: Ventas por Curso ---
            const pieCtx = document.getElementById('pieChartVentas').getContext('2d');
            const pieData = <?php echo $json_data_pie; ?>;

            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: pieData.labels,
                    datasets: [{
                        label: 'Ventas por Curso',
                        data: pieData.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // --- Gráfico de Barras: Ventas Mensuales ---
            const barCtx = document.getElementById('barChartVentas').getContext('2d');
            const barData = <?php echo $json_data_bar; ?>;

            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: barData.labels,
                    datasets: [{
                        label: 'Ventas Totales del Mes',
                        data: barData.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <style>
        .filters-container { display: flex; gap: 20px; padding: 20px; background: #f4f4f4; border-radius: 8px; margin-bottom: 20px; }
        .filters-container form { display: flex; gap: 15px; align-items: center; }
        .charts-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .chart-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    </style>
</body>
</html>
