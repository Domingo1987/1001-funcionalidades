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

// Versión v2 con tipo_comparacion y problema_id
function guardar_evaluacion_en_bd_v2($problema, $solucion, $total_puntos, $user_id, $criterios, $problema_id = 0) {
    global $wpdb;

    $wpdb->insert(
        $wpdb->prefix . 'evaluaciones',
        [
            'problema' => $problema,
            'solucion' => $solucion,
            'total_puntos' => $total_puntos,
            'user_id' => $user_id,
            'problema_id' => $problema_id,
            'fecha_evaluacion' => current_time('mysql')
        ]
    );

    $evaluacion_id = $wpdb->insert_id;

    foreach ($criterios as $criterio) {
        $wpdb->insert(
            $wpdb->prefix . 'evaluaciones_criterios',
            [
                'criterio_id' => $criterio['criterio_id'],
                'criterio_just' => $criterio['justificacion'],
                'criterio_puntos' => $criterio['puntaje_asignado'],
                'criterio_retro' => $criterio['retroalimentacion'],
                'tipo_comparacion' => $criterio['tipo_comparacion'] ?? null,
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

    // Tomar el primer curso registrado en ese año
    $curso_data = $historico[$anio][0] ?? null;

    $curso = $curso_data['curso'] ?? '';
    $centro = $curso_data['centro'] ?? '';

    error_log("🔎 Año: $anio | Curso: $curso | Centro: $centro");

    if (!$curso || !$centro || !$anio) {
        error_log("❌ Faltan datos clave para filtrar (curso/centro/año)");
        return [];
    }

    // Consulta a la base
    $sql = "
        SELECT 
            pp.id,
            CONCAT('Práctico: ', p.nombre, ' → ', pp.titulo) AS nombre,
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


// Construir mensaje para IA (v2)
function construir_mensaje_v2($problema_texto, $solucion_actual, $user_id, $problema_id) {
    $historial = get_historial_entregas_v2($user_id, $problema_id);
    $criterios_def = get_criterios_rubrica_v2();

    $historial_texto = '';
    if (count($historial) === 0) {
        $historial_texto = "Esta es la primera entrega del estudiante.";
    } else {
        $historial_texto = "Historial de entregas previas:\n";
        foreach ($historial as $i => $entrega) {
            $historial_texto .= "- Versión " . ($i + 1) . " ({$entrega['fecha']}): {$entrega['total_puntos']} puntos\n";
            foreach ($entrega['criterios'] as $c) {
                $historial_texto .= "    • {$c['criterio']}: {$c['puntos']} pts ({$c['tipo_comparacion']})\n";
            }
        }
    }

    $rubrica_texto = "Rúbrica de evaluación:\n";
    foreach ($criterios_def as $c) {
        $rubrica_texto .= "- {$c['criterio_nombre']} (máx {$c['puntaje_maximo']}): {$c['descripcion']}\n";
    }

    $mensaje = <<<TXT
Estudiante: ID $user_id
Problema: $problema_texto

$rubrica_texto

$historial_texto

Nueva solución enviada:
$solucion_actual

Evalúa esta entrega según la rúbrica y el historial anterior. Indica si hay mejoras o retrocesos por criterio y genera retroalimentación detallada.
Devuelve la evaluación en JSON con total_puntos y criterios.
TXT;

    return $mensaje;
}

// Obtener historial de entregas del usuario por problema (v2)
function get_historial_entregas_v2($user_id, $problema_id) {
    global $wpdb;

    // Traer todas las evaluaciones para este usuario y problema
    $evaluaciones = $wpdb->get_results($wpdb->prepare("
        SELECT e.id AS evaluacion_id, e.total_puntos, e.fecha_evaluacion
        FROM {$wpdb->prefix}evaluaciones e
        WHERE e.user_id = %d
        AND e.problema_id = %d
        ORDER BY e.fecha_evaluacion ASC
    ", $user_id, $problema_id), ARRAY_A);

    $historial = [];

    foreach ($evaluaciones as $eval) {
        $criterios = $wpdb->get_results($wpdb->prepare("
            SELECT c.criterio_id, r.criterio_nombre, c.criterio_puntos, c.tipo_comparacion
            FROM {$wpdb->prefix}evaluaciones_criterios c
            LEFT JOIN {$wpdb->prefix}rubrica_problemas r ON c.criterio_id = r.id
            WHERE c.evaluacion_id = %d
        ", $eval['evaluacion_id']), ARRAY_A);

        $criterios_formateados = array_map(function ($c) {
            return [
                'criterio' => $c['criterio_nombre'],
                'puntos' => $c['criterio_puntos'],
                'tipo_comparacion' => $c['tipo_comparacion'] ?? 'sin datos',
            ];
        }, $criterios);

        $historial[] = [
            'fecha' => $eval['fecha_evaluacion'],
            'total_puntos' => $eval['total_puntos'],
            'criterios' => $criterios_formateados,
        ];
    }

    return $historial;
}


// Obtener rúbrica (v2)
function get_criterios_rubrica_v2() {
    global $wpdb;

    $resultados = $wpdb->get_results("SELECT criterio_nombre, puntaje_maximo, descripcion FROM {$wpdb->prefix}rubrica_problemas", ARRAY_A);
    return $resultados ?: [];
}
