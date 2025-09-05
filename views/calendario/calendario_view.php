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
        /* Corrección de desbordamiento */
        overflow: hidden;
    }
    .fc-event-title, .event-details p, .event-time {
        /* Evitar que el texto se divida y se desborde */
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

    // --- Función para generar colores pastel basados en un string ---
    function generatePastelColor(key) {
        let hash = 0;
        for (let i = 0; i < key.length; i++) {
            hash = key.charCodeAt(i) + ((hash << 5) - hash);
        }
        const h = hash % 360;
        return `hsl(${h}, 70%, 85%)`;
    }

    // --- Formateador de hora ---
    const timeFormatter = new Intl.DateTimeFormat('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?php echo $calendar_events_json; ?>,

        eventContent: function(arg) {
            const props = arg.event.extendedProps;
            const key = `${props.id_curso}-${props.id_area}-${props.id_sub_area}-${props.id_cliente}`;
            const color = generatePastelColor(key);

            // Formatear la hora
            const startTime = timeFormatter.format(arg.event.start);
            const endTime = timeFormatter.format(arg.event.end);
            const timeText = `${startTime} - ${endTime}`;

            let eventEl = document.createElement('div');
            eventEl.style.backgroundColor = color;
            eventEl.style.borderColor = color;
            eventEl.classList.add('fc-event-main-frame');

            // Añadir la hora antes del título
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
});
</script>

<?php require_once 'views/partials/footer.php'; ?>
