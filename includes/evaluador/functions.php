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

function obtener_problemas_practicos_usuario($user_id = null) {
    global $wpdb;
    $user_id = $user_id ?: get_current_user_id();

    error_log("📥 Buscando problemas del usuario $user_id");

    $historico_json = get_user_meta($user_id, 'historico_academico', true);

    if (!$historico_json) {
        error_log("❌ No se encontró el campo historico_academico");
        return [];
    }

    $historico = json_decode($historico_json, true);
    if (!$historico) {
        error_log("❌ Error al decodificar JSON del historial académico");
        return [];
    }

    // Tomar el año más reciente
    $años = array_keys($historico);
    rsort($años);
    $anio = $años[0];

    $curso = $historico[$anio]['curso'] ?? '';
    $centro = $historico[$anio]['centro'] ?? '';

    error_log("🔎 Año: $anio | Curso: $curso | Centro: $centro");

    if (!$curso || !$centro || !$anio) {
        error_log("❌ Faltan datos clave para filtrar (curso/centro/año)");
        return [];
    }

    // Consulta a la base
    $sql = "
        SELECT 
            pp.id,
            CONCAT('Práctico ', p.id, ' - ', p.nombre, ' → ', pp.titulo) AS nombre,
            pp.descripcion
        FROM {$wpdb->prefix}practicos_problemas pp
        JOIN {$wpdb->prefix}practicos p ON pp.practico_id = p.id
        WHERE p.activo = 1 AND pp.activo = 1
          AND p.curso = %s
          AND p.centro = %s
          AND p.anio = %d
        ORDER BY p.id DESC, pp.id ASC
    ";

    $resultados = $wpdb->get_results($wpdb->prepare($sql, $curso, $centro, (int)$anio));
    error_log("✅ Se encontraron " . count($resultados) . " problemas para mostrar");
    return $resultados;
}
