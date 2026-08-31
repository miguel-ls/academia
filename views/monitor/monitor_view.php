<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Monitor de Cursos con Vacantes Disponibles</h1>
    <div class="page-header-right">
            <span id="monitor-countdown" class="monitor-countdown" aria-live="polite">Actualiza en 5 s</span>
            <a id="btn-actualizar-monitor" href="index.php?view=monitor" class="btn btn-secondary">Actualizar Lista</a>
    </div>
</div>

<div id="monitor-cards" class="cards-container">
    <?php if (empty($cursos_disponibles)): ?>
        <div class="no-courses" style="width: 100%; text-align: center; padding: 40px; background-color: #fff; border: 1px solid #ddd; border-radius: 5px;">
            <p>No hay cursos con vacantes disponibles en este momento.</p>
        </div>
    <?php else: ?>
        <?php $cursos_por_horario = []; ?>
        <?php foreach ($cursos_disponibles as $curso): ?>
            <?php $cursos_por_horario[$curso['horario_dias'] ?: 'Sin horario'] [] = $curso; ?>
        <?php endforeach; ?>
        <?php foreach ($cursos_por_horario as $horario => $cursos): ?>
            <section class="monitor-schedule-group">
                <h2><?php echo htmlspecialchars($horario); ?></h2>
                <div class="monitor-schedule-cards">
                    <?php foreach ($cursos as $curso): ?>
                        <div class="card monitor-course-card">
                            <h3><?php echo htmlspecialchars($curso['nombre_curso']); ?></h3>
                            <p><strong>Profesor:</strong> <?php echo htmlspecialchars($curso['nombre_profesor']); ?></p>
                            <p><strong>Periodo:</strong> <?php echo date('d/m/Y', strtotime($curso['fecha_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($curso['fecha_fin'])); ?></p>
                            <p><strong>Horario:</strong> <?php echo htmlspecialchars($curso['horario_dias']); ?> (<?php echo date('H:i', strtotime($curso['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($curso['hora_fin'])); ?>)</p>
                            <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($curso['area'] . ' - ' . $curso['sub_area'] . ' ' . $curso['numero_sub_area']); ?></p>
                            <p><strong>Vacantes:</strong> <?php echo htmlspecialchars($curso['vacantes_disponibles']); ?></p>
                            <p><strong>Precio:</strong> S/ <?php echo number_format($curso['precio_actual'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.cards-container {
    display: grid;
    gap: 24px;
}
.monitor-schedule-group {
    width: 100%;
}
.monitor-schedule-group h2 {
    margin: 0 0 12px;
    color: #253858;
    font-size: 1.05rem;
}
.monitor-schedule-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.monitor-course-card,
.no-courses {
    width: 320px;
    box-sizing: border-box;
    padding: 15px;
    margin-bottom: 20px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, .1);
}
.monitor-course-card h3 {
    margin-top: 0;
    color: #0056b3;
}
.no-courses {
    width: 100%;
    padding: 40px;
    text-align: center;
}
.monitor-countdown {
    display: inline-flex;
    align-items: center;
    min-height: 38px;
    margin-right: 10px;
    color: #64748b;
    font-size: .9rem;
    white-space: nowrap;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const actualizarMonitor = document.getElementById('btn-actualizar-monitor');
    const monitorCountdown = document.getElementById('monitor-countdown');
    const monitorCards = document.getElementById('monitor-cards');
    const intervaloActualizacion = 6000;
    let monitorTimer = null;
    let countdownTimer = null;
    let segundosRestantes = 6;
    let actualizacionEnCurso = false;

    function actualizarCountdown() {
        monitorCountdown.textContent = `Actualiza en ${segundosRestantes} s`;
    }

    function reiniciarCountdown() {
        segundosRestantes = 6;
        actualizarCountdown();
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(character) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[character];
        });
    }

    function formatDate(value) {
        const parts = String(value ?? '').split('-');
        return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : '';
    }

    function formatTime(value) {
        return String(value ?? '').slice(0, 5);
    }

    function renderCards(cursos) {
        if (!cursos.length) {
            monitorCards.innerHTML = '<div class="no-courses"><p>No hay cursos con vacantes disponibles en este momento.</p></div>';
            return;
        }

        const cursosPorHorario = cursos.reduce(function(grupos, curso) {
            const horario = curso.horario_dias || 'Sin horario';
            if (!grupos[horario]) grupos[horario] = [];
            grupos[horario].push(curso);
            return grupos;
        }, {});

        monitorCards.innerHTML = Object.entries(cursosPorHorario).map(function([horario, cursosHorario]) {
            const cards = cursosHorario.map(function(curso) {
            const ubicacion = `${curso.area} - ${curso.sub_area} ${curso.numero_sub_area}`;
            return `<div class="card monitor-course-card">
                <h3>${escapeHtml(curso.nombre_curso)}</h3>
                <p><strong>Profesor:</strong> ${escapeHtml(curso.nombre_profesor)}</p>
                <p><strong>Periodo:</strong> ${formatDate(curso.fecha_inicio)} - ${formatDate(curso.fecha_fin)}</p>
                <p><strong>Horario:</strong> ${escapeHtml(curso.horario_dias)} (${formatTime(curso.hora_inicio)} - ${formatTime(curso.hora_fin)})</p>
                <p><strong>Ubicación:</strong> ${escapeHtml(ubicacion)}</p>
                <p><strong>Vacantes:</strong> ${escapeHtml(curso.vacantes_disponibles)}</p>
                <p><strong>Precio:</strong> S/ ${Number(curso.precio_actual || 0).toFixed(2)}</p>
            </div>`;
            }).join('');
            return `<section class="monitor-schedule-group">
                <h2>${escapeHtml(horario)}</h2>
                <div class="monitor-schedule-cards">${cards}</div>
            </section>`;
        }).join('');
    }

    function actualizarCards() {
        if (actualizacionEnCurso || document.hidden) return;
        actualizacionEnCurso = true;
        reiniciarCountdown();
        fetch('index.php?view=monitor&action=datos', {cache: 'no-store'})
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(renderCards)
            .catch(error => console.error('Error al actualizar el monitor:', error))
            .finally(() => { actualizacionEnCurso = false; });
    }

    actualizarMonitor.addEventListener('click', function(event) {
        event.preventDefault();
        actualizarCards();
    });

    function iniciarMonitor() {
        if (monitorTimer !== null || document.hidden) return;
        monitorTimer = window.setInterval(function() {
            actualizarCards();
        }, intervaloActualizacion);
    }

    function detenerMonitor() {
        if (monitorTimer === null) return;
        window.clearInterval(monitorTimer);
        monitorTimer = null;
    }

    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            detenerMonitor();
            if (countdownTimer !== null) {
                window.clearInterval(countdownTimer);
                countdownTimer = null;
            }
        } else {
            actualizarCards();
            iniciarCuentaRegresiva();
            iniciarMonitor();
        }
    });

    function iniciarCuentaRegresiva() {
        if (countdownTimer !== null || document.hidden) return;
        countdownTimer = window.setInterval(function() {
            segundosRestantes = Math.max(0, segundosRestantes - 1);
            actualizarCountdown();
        }, 1000);
    }

    iniciarMonitor();
    iniciarCuentaRegresiva();
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
