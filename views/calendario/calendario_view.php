<?php require_once 'views/partials/header.php'; ?>

<div class="page-header">
    <h1>Calendario de Clases</h1>
</div>

<div class="card" style="padding: 20px;">
    <div id="calendar"></div>
</div>

<style>
    /* Estilos para los eventos del calendario */
    .fc-event-main-frame {
        padding: 5px;
        font-size: 12px;
        line-height: 1.3;
        cursor: pointer;
    }
    .fc-event-title {
        font-weight: bold;
        white-space: normal; /* Permitir que el título del curso se divida en varias líneas */
    }
    .event-details {
        margin-top: 5px;
    }
    .event-details p {
        margin: 0;
    }
</style>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- Función para generar colores pastel basados en un string ---
    function generatePastelColor(key) {
        let hash = 0;
        for (let i = 0; i < key.length; i++) {
            hash = key.charCodeAt(i) + ((hash << 5) - hash);
        }
        const h = hash % 360;
        // Usar HSL para colores pastel: alta luminosidad (l), saturación media (s)
        return `hsl(${h}, 70%, 85%)`;
    }

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        // Cargar los eventos desde la variable PHP
        events: <?php echo $calendar_events_json; ?>,

        eventContent: function(arg) {
            const props = arg.event.extendedProps;
            const key = `${props.id_curso}-${props.id_area}-${props.id_sub_area}-${props.id_cliente}`;
            const color = generatePastelColor(key);

            let eventEl = document.createElement('div');
            eventEl.style.backgroundColor = color;
            eventEl.style.borderColor = color;
            eventEl.classList.add('fc-event-main-frame');

            eventEl.innerHTML = `
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
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
