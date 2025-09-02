<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Panel Principal</h1>
</div>

<div class="filters-container form-container" style="max-width: none;">
    <form action="index.php" method="GET" class="form-row">
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
                <?php
                $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                foreach ($meses as $num => $nombre) {
                    $month_num = $num + 1;
                    echo '<option value="' . $month_num . '" ' . ($month_num == $mes_seleccionado ? 'selected' : '') . '>' . $nombre . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group" style="align-self: flex-end;">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>
</div>

<div class="charts-container">
    <div class="chart-card">
        <h3>Ventas por Curso (<?php echo $meses[$mes_seleccionado - 1] . ' ' . $anio_seleccionado; ?>)</h3>
        <canvas id="pieChartVentas"></canvas>
    </div>
    <div class="chart-card">
        <h3>Ventas Mensuales (<?php echo $anio_seleccionado; ?>)</h3>
        <canvas id="barChartVentas"></canvas>
    </div>
</div>

<!-- Chart.js se carga en el footer -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Gráfico Circular: Ventas por Curso ---
    const pieCtx = document.getElementById('pieChartVentas').getContext('2d');
    const pieData = <?php echo $json_data_pie; ?>;

    if (pieData.labels.length > 0) {
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: pieData.labels,
                datasets: [{
                    label: 'Ventas',
                    data: pieData.data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)', 'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)', 'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            }
        });
    } else {
        pieCtx.font = "16px Arial";
        pieCtx.textAlign = "center";
        pieCtx.fillText("No hay datos de ventas para este mes.", pieCtx.canvas.width / 2, 100);
    }

    // --- Gráfico de Barras: Ventas Mensuales ---
    const barCtx = document.getElementById('barChartVentas').getContext('2d');
    const barData = <?php echo $json_data_bar; ?>;

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: barData.labels,
            datasets: [{
                label: 'Ventas Totales',
                data: barData.data,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
