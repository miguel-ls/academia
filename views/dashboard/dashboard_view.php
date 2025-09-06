<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Panel Principal</h1>
</div>

<div class="card">
    <form action="index.php" method="GET" class="form-row">
        <input type="hidden" name="view" value="dashboard">
        <div class="form-group">
            <label for="anio">Año:</label>
            <select name="anio" id="anio" class="form-control">
                <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($i == $anio_seleccionado) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="mes">Mes:</label>
            <select name="mes" id="mes" class="form-control">
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

<div class="dashboard-grid">
    <div class="card">
        <h3>Ventas Mensuales (<?php echo $anio_seleccionado; ?>)</h3>
        <canvas id="barChartVentas"></canvas>
    </div>
    <div class="card">
        <h3>Ventas por Curso (<?php echo $meses[$mes_seleccionado - 1] . ' ' . $anio_seleccionado; ?>)</h3>
        <canvas id="pieChartVentas"></canvas>
    </div>
    <div class="card">
        <h3>Ventas por Curso-Área (<?php echo $meses[$mes_seleccionado - 1] . ' ' . $anio_seleccionado; ?>)</h3>
        <canvas id="pieChartVentasArea"></canvas>
    </div>
</div>

<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}
</style>

<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Gráfico de Barras: Ventas Mensuales ---
    const barCtx = document.getElementById('barChartVentas').getContext('2d');
    const barData = <?php echo $json_data_bar; ?>;
    new Chart(barCtx, { type: 'bar', data: { labels: barData.labels, datasets: [{
        label: 'Ventas Totales', data: barData.data,
        backgroundColor: 'rgba(0, 123, 255, 0.7)',
        borderColor: 'rgba(0, 123, 255, 1)',
        borderWidth: 1
    }]}, options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }});

    // --- Gráfico Circular 1: Ventas por Curso ---
    const pieCtx = document.getElementById('pieChartVentas').getContext('2d');
    const pieData = <?php echo $json_data_pie; ?>;
    if (pieData.labels.length > 0) {
        new Chart(pieCtx, { type: 'pie', data: { labels: pieData.labels, datasets: [{
            label: 'Ventas', data: pieData.data,
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#6610f2'],
            borderColor: '#fff', borderWidth: 2
        }]}});
    } else {
        pieCtx.font = "16px Arial";
        pieCtx.textAlign = "center";
        pieCtx.fillText("No hay datos de ventas por curso para este mes.", pieCtx.canvas.width / 2, 100);
    }

    // --- Gráfico Circular 2: Ventas por Curso-Área ---
    const pieAreaCtx = document.getElementById('pieChartVentasArea').getContext('2d');
    const pieAreaData = <?php echo $json_data_pie_area; ?>;
    if (pieAreaData.labels.length > 0) {
        new Chart(pieAreaCtx, { type: 'pie', data: { labels: pieAreaData.labels, datasets: [{
            label: 'Ventas', data: pieAreaData.data,
            backgroundColor: ['#17a2b8', '#fd7e14', '#6610f2', '#e83e8c', '#20c997', '#ffc107', '#28a745', '#dc3545'],
            borderColor: '#fff', borderWidth: 2
        }]}});
    } else {
        pieAreaCtx.font = "16px Arial";
        pieAreaCtx.textAlign = "center";
        pieAreaCtx.fillText("No hay datos de ventas por curso-área para este mes.", pieAreaCtx.canvas.width / 2, 100);
    }
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
