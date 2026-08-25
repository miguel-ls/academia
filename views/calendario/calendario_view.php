<?php
$filter_data = $filter_data ?? [
    'clientes' => [],
    'cursos' => [],
    'profesores' => [],
    'ubicaciones' => []
];
$calendar_events_json = $calendar_events_json ?? '[]';
require_once 'views/partials/header.php';
?>

<div class="page-header">
    <h1>Calendario de Clases por Alumnos</h1>
</div>

<!-- Filtros del Calendario -->
<div class="card" style="padding: 20px; margin-bottom: 20px;">
    <div class="filter-container">
        <div class="form-group">
            <label for="filtro_curso">Curso:</label>
            <div class="client-filter" id="course-filter">
                <input type="text" id="filtro_curso" class="form-control" autocomplete="off" placeholder="Buscar curso..." aria-autocomplete="list" aria-controls="cursos-resultados">
                <input type="hidden" id="filtro_curso_id" value="">
                <div id="cursos-resultados" class="client-results" role="listbox"></div>
            </div>
        </div>
        <div class="form-group">
            <label for="filtro_cliente">Alumno:</label>
            <div class="client-filter" id="client-filter">
                <input type="text" id="filtro_cliente" class="form-control" autocomplete="off" placeholder="Buscar alumno..." aria-autocomplete="list" aria-controls="clientes-resultados">
                <input type="hidden" id="filtro_cliente_id" value="">
                <div id="clientes-resultados" class="client-results" role="listbox"></div>
            </div>
        </div>
        <div class="form-group">
            <label for="filtro_profesor">Profesor:</label>
            <div class="client-filter" id="teacher-filter">
                <input type="text" id="filtro_profesor" class="form-control" autocomplete="off" placeholder="Buscar profesor..." aria-autocomplete="list" aria-controls="profesores-resultados">
                <input type="hidden" id="filtro_profesor_valor" value="">
                <div id="profesores-resultados" class="client-results" role="listbox"></div>
            </div>
        </div>
        <div class="form-group">
            <label for="filtro_ubicacion">Ubicación:</label>
            <div class="client-filter" id="location-filter">
                <input type="text" id="filtro_ubicacion" class="form-control" autocomplete="off" placeholder="Buscar ubicación..." aria-autocomplete="list" aria-controls="ubicaciones-resultados">
                <input type="hidden" id="filtro_ubicacion_valor" value="">
                <div id="ubicaciones-resultados" class="client-results" role="listbox"></div>
            </div>
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
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #fff;
        color: #1e293b;
        box-sizing: border-box;
    }
    .form-control:focus {
        outline: 3px solid rgba(37, 99, 235, .14);
        border-color: #2563eb;
    }
    .client-filter {
        position: relative;
    }
    .client-results {
        position: absolute;
        z-index: 20;
        top: calc(100% + 5px);
        left: 0;
        right: 0;
        display: none;
        max-height: 230px;
        overflow-y: auto;
        padding: 5px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #fff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .14);
    }
    .client-results.is-open {
        display: block;
    }
    .client-result {
        display: block;
        width: 100%;
        padding: 10px 11px;
        border: 0;
        border-radius: 5px;
        background: transparent;
        color: #334155;
        text-align: left;
        cursor: pointer;
    }
    .client-result:hover,
    .client-result:focus {
        outline: none;
        background: #eff6ff;
        color: #1d4ed8;
    }
    .client-empty {
        padding: 10px 11px;
        color: #64748b;
        font-size: .9rem;
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
    const clients = <?php echo json_encode($filter_data['clientes'], JSON_UNESCAPED_UNICODE); ?>;
    const calendarEl = document.getElementById('calendar');
    const filtroCurso = document.getElementById('filtro_curso');
    const filtroCliente = document.getElementById('filtro_cliente');
    const filtroProfesor = document.getElementById('filtro_profesor');
    const filtroUbicacion = document.getElementById('filtro_ubicacion');
    const btnFiltrar = document.getElementById('btn_filtrar');
    const btnLimpiar = document.getElementById('btn_limpiar');
    const filterConfigs = [
        { input: filtroCurso, value: document.getElementById('filtro_curso_id'), results: document.getElementById('cursos-resultados'), wrapper: document.getElementById('course-filter'), items: Object.entries(<?php echo json_encode($filter_data['cursos'], JSON_UNESCAPED_UNICODE); ?>), useId: true },
        { input: filtroCliente, value: document.getElementById('filtro_cliente_id'), results: document.getElementById('clientes-resultados'), wrapper: document.getElementById('client-filter'), items: Object.entries(clients), useId: true },
        { input: filtroProfesor, value: document.getElementById('filtro_profesor_valor'), results: document.getElementById('profesores-resultados'), wrapper: document.getElementById('teacher-filter'), items: Object.entries(<?php echo json_encode($filter_data['profesores'], JSON_UNESCAPED_UNICODE); ?>) },
        { input: filtroUbicacion, value: document.getElementById('filtro_ubicacion_valor'), results: document.getElementById('ubicaciones-resultados'), wrapper: document.getElementById('location-filter'), items: Object.entries(<?php echo json_encode($filter_data['ubicaciones'], JSON_UNESCAPED_UNICODE); ?>) }
    ];

    function closeResults(results) {
        results.classList.remove('is-open');
        results.innerHTML = '';
    }

    function renderResults(config, query) {
        const normalizedQuery = query.trim().toLocaleLowerCase('es');
        const matches = config.items.filter(([id, name]) =>
            String(name).toLocaleLowerCase('es').includes(normalizedQuery)
        ).slice(0, 30);

        config.results.innerHTML = '';
        if (matches.length === 0) {
            config.results.innerHTML = '<div class="client-empty">No se encontraron resultados.</div>';
        } else {
            matches.forEach(([id, name]) => {
                const result = document.createElement('button');
                result.type = 'button';
                result.className = 'client-result';
                result.dataset.id = id;
                result.textContent = name;
                result.setAttribute('role', 'option');
                config.results.appendChild(result);
            });
        }
        config.results.classList.add('is-open');
    }

    filterConfigs.forEach(config => {
        config.input.addEventListener('input', function() {
            config.value.value = '';
            renderResults(config, this.value);
        });
        config.input.addEventListener('focus', function() {
            renderResults(config, this.value);
        });
        config.results.addEventListener('click', function(event) {
            const result = event.target.closest('.client-result');
            if (!result) return;
            config.input.value = result.textContent;
            config.value.value = config.useId ? result.dataset.id : result.textContent;
            closeResults(config.results);
        });
    });
    document.addEventListener('click', function(event) {
        filterConfigs.forEach(config => {
            if (!config.wrapper.contains(event.target)) closeResults(config.results);
        });
    });

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
            const key = `${props.id_curso}-${props.id_area}-${props.id_sub_area}-${props.id_cliente}`;
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
                    <p><strong>Est:</strong> ${props.nombre_cliente}</p>
                    <p><strong>Prof:</strong> ${props.nombre_profesor}</p>
                    <p><strong>Ubic:</strong> ${props.ubicacion}</p>
                </div>
            `;

            return { domNodes: [eventEl] };
        }
    });
    calendar.render();

    // --- Lógica de los Filtros ---
    btnFiltrar.addEventListener('click', function() {
        const cursoId = document.getElementById('filtro_curso_id').value;
        const clienteId = document.getElementById('filtro_cliente_id').value;
        const profesorNombre = document.getElementById('filtro_profesor_valor').value;
        const ubicacionNombre = document.getElementById('filtro_ubicacion_valor').value;

        const filteredEvents = allEvents.filter(function(event) {
            const props = event.extendedProps;
            const matchCurso = !cursoId || props.id_curso == cursoId;
            const matchCliente = !clienteId || props.id_cliente == clienteId;
            const matchProfesor = !profesorNombre || props.nombre_profesor == profesorNombre;
            const matchUbicacion = !ubicacionNombre || props.ubicacion == ubicacionNombre;

            return matchCurso && matchCliente && matchProfesor && matchUbicacion;
        });

        calendar.removeAllEvents();
        calendar.addEventSource(filteredEvents);
    });

    btnLimpiar.addEventListener('click', function() {
        filtroCurso.value = "";
        filtroCliente.value = "";
        clientIdInput.value = "";
        document.getElementById('filtro_curso_id').value = "";
        document.getElementById('filtro_profesor_valor').value = "";
        document.getElementById('filtro_ubicacion_valor').value = "";
        filterConfigs.forEach(config => closeResults(config.results));
        filtroProfesor.value = "";
        filtroUbicacion.value = "";

        calendar.removeAllEvents();
        calendar.addEventSource(allEvents);
    });
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
