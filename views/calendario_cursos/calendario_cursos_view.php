<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Calendario de Cursos Programados</h1>
</div>

<!-- Filtros del Calendario -->
<div class="card" style="padding: 20px; margin-bottom: 20px;">
    <div class="filter-container">
        <div class="form-group">
            <label for="filtro_curso">Curso:</label>
            <select id="filtro_curso" class="form-control">
                <option value="">Todos</option>
                <?php foreach ($filter_data['cursos'] as $id => $nombre): ?>
                    <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($nombre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="filtro_profesor">Profesor:</label>
            <select id="filtro_profesor" class="form-control">
                <option value="">Todos</option>
                <?php foreach ($filter_data['profesores'] as $nombre): ?>
                    <option value="<?php echo htmlspecialchars($nombre); ?>"><?php echo htmlspecialchars($nombre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="filtro_ubicacion">Ubicación:</label>
            <select id="filtro_ubicacion" class="form-control">
                <option value="">Todas</option>
                <?php foreach ($filter_data['ubicaciones'] as $nombre): ?>
                    <option value="<?php echo htmlspecialchars($nombre); ?>"><?php echo htmlspecialchars($nombre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group filter-buttons">
            <button id="btn_filtrar" type="button" class="btn btn-primary">Filtrar</button>
            <button id="btn_limpiar" type="button" class="btn btn-secondary">Limpiar</button>
        </div>
    </div>
</div>


<div class="card" style="padding: 20px;">
    <div id="calendar"></div>
</div>

<style>
    .filter-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        align-items: flex-end;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    .form-group label {
        margin-bottom: 5px;
        font-weight: bold;
    }
    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .filter-buttons {
        flex-direction: row;
        gap: 10px;
    }

    /* Estilos para los eventos del calendario */
    .fc-event-main-frame {
        padding: 5px;
        font-size: 12px;
        line-height: 1.3;
        cursor: pointer;
        overflow: hidden;
    }
    .fc-event-title, .event-details p, .event-time {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .fc-event-title {
        font-weight: bold;
    }
    .event-details {
        margin-top: 5px;
    }
    .event-details p {
        margin: 0;
    }
    .event-time {
        font-weight: bold;
    }
</style>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- Variables y referencias ---
    const allEvents = <?php echo $calendar_events_json; ?>;
    const calendarEl = document.getElementById('calendar');
    const filtroCurso = document.getElementById('filtro_curso');
    const filtroProfesor = document.getElementById('filtro_profesor');
    const filtroUbicacion = document.getElementById('filtro_ubicacion');
    const btnFiltrar = document.getElementById('btn_filtrar');
    const btnLimpiar = document.getElementById('btn_limpiar');

    // --- Función para generar colores pastel ---
    function generatePastelColor(str) {
        let hash = 0x811c9dc5;
        for (let i = 0; i < str.length; i++) {
            hash ^= str.charCodeAt(i);
            hash += (hash << 1) + (hash << 4) + (hash << 7) + (hash << 8) + (hash << 24);
        }
        const h = (hash >>> 0) % 360;
        return `hsl(${h}, 70%, 85%)`;
    }

    // --- Formateador de hora ---
    const timeFormatter = new Intl.DateTimeFormat('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });

    // --- Inicialización del Calendario ---
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: allEvents,

        eventContent: function(arg) {
            const props = arg.event.extendedProps;
            // La clave de color no incluye al cliente
            const key = `${props.id_curso}-${props.id_area}-${props.id_sub_area}`;
            const color = generatePastelColor(key);

            const startTime = timeFormatter.format(arg.event.start);
            const endTime = timeFormatter.format(arg.event.end);
            const timeText = `${startTime} - ${endTime}`;

            let eventEl = document.createElement('div');
            eventEl.style.backgroundColor = color;
            eventEl.style.borderColor = color;
            eventEl.classList.add('fc-event-main-frame');

            eventEl.innerHTML = `
                <div class="event-time">${timeText}</div>
                <div class="fc-event-title-container">
                    <div class="fc-event-title">${arg.event.title}</div>
                </div>
                <div class="event-details">
                    <p><strong>Prof:</strong> ${props.nombre_profesor}</p>
                    <p><strong>Ubic:</strong> ${props.ubicacion}</p>
                    <p><strong>Vacantes:</strong> ${props.vacantes}</p>
                </div>
            `;

            return { domNodes: [eventEl] };
        }
    });
    calendar.render();

    // --- Lógica de los Filtros ---
    btnFiltrar.addEventListener('click', function() {
        const cursoId = filtroCurso.value;
        const profesorNombre = filtroProfesor.value;
        const ubicacionNombre = filtroUbicacion.value;

        const filteredEvents = allEvents.filter(function(event) {
            const props = event.extendedProps;
            const matchCurso = !cursoId || props.id_curso == cursoId;
            const matchProfesor = !profesorNombre || props.nombre_profesor == profesorNombre;
            const matchUbicacion = !ubicacionNombre || props.ubicacion == ubicacionNombre;

            return matchCurso && matchProfesor && matchUbicacion;
        });

        calendar.removeAllEvents();
        calendar.addEventSource(filteredEvents);
    });

    btnLimpiar.addEventListener('click', function() {
        filtroCurso.value = "";
        filtroProfesor.value = "";
        filtroUbicacion.value = "";

        calendar.removeAllEvents();
        calendar.addEventSource(allEvents);
    });
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
