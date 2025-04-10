<?php
// Función de logging (solo si WP_DEBUG está activado)
function write_log($data) {
    if (defined('WP_DEBUG') && true === WP_DEBUG) {
        if (is_array($data) || is_object($data)) {
            error_log(print_r($data, true));
        } else {
            error_log($data);
        }
    }
}

// Guardar la evaluación general y sus criterios en la base de datos
function guardar_evaluacion_en_bd($problema, $solucion, $total_puntos, $user_id, $criterios) {
    global $wpdb;

    // Guardar evaluación principal
    $wpdb->insert(
        $wpdb->prefix . 'evaluaciones',
        [
            'problema' => $problema,
            'solucion' => $solucion,
            'total_puntos' => $total_puntos,
            'user_id' => $user_id,
            'fecha_evaluacion' => current_time('mysql')
        ]
    );

    $evaluacion_id = $wpdb->insert_id;

    // Guardar criterios
    foreach ($criterios as $criterio) {
        $wpdb->insert(
            $wpdb->prefix . 'evaluaciones_criterios',
            [
                'criterio_id' => $criterio['criterio_id'],
                'criterio_just' => $criterio['justificacion'],
                'criterio_puntos' => $criterio['puntaje_asignado'],
                'criterio_retro' => $criterio['retroalimentacion'],
                'evaluacion_id' => $evaluacion_id
            ]
        );
    }

    return $evaluacion_id;
}
