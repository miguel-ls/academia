<?php

// =================================================================
// Archivo de Funciones de Ayuda (Helpers)
// =================================================================

if (!function_exists('get_day_name_es')) {
    /**
     * Devuelve el nombre en español para un día de la semana numérico.
     *
     * @param int $day_of_week_numeric Un número del 1 (Lunes) al 7 (Domingo).
     * @return string El nombre del día en español.
     */
    function get_day_name_es($day_of_week_numeric) {
        $dias = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo'
        ];

        return $dias[$day_of_week_numeric] ?? 'Día no válido';
    }
}
