<?php
if (!defined('ABSPATH')) {
    exit;
}

// Incluir archivo central de estadísticas (consulta a BD)
require_once plugin_dir_path(__FILE__) . '/../utils/estadisticas-dashboard.php';

// Módulos (secciones del dashboard)
function renderizar_resumen_general($user_id) {
    error_log("✅ Ejecutando renderizar_resumen_general para user $user_id");

    ob_start();
    include plugin_dir_path(__FILE__) . '/partes/resumen-general.php';
    return ob_get_clean();
}


function renderizar_actividad_por_tipo($user_id) {
    ob_start();
    include plugin_dir_path(__FILE__) . '/partes/actividad_por_tipo.php';
    return ob_get_clean();
}

function renderizar_progreso_categorias($user_id) {
    error_log("✅ Ejecutando renderizar_progreso_categoria para user $user_id");

    ob_start();
    include plugin_dir_path(__FILE__) . '/partes/progreso-categorias.php';
    return ob_get_clean();

}
function renderizar_publicaciones_ia($user_id) {
    ob_start();
    include plugin_dir_path(__FILE__) . '/partes/publicaciones-ia.php';
    return ob_get_clean();
}

function renderizar_progreso_competencias($user_id) {
    ob_start();
    include plugin_dir_path(__FILE__) . '/partes/progreso-competencias.php';
    return ob_get_clean();
}

function renderizar_medallas($user_id) {
    ob_start();
    include plugin_dir_path(__FILE__) . '/partes/medallas.php';
    return ob_get_clean();
}

function renderizar_interacciones_sociales($user_id) {
    ob_start();
    include plugin_dir_path(__FILE__) . '/partes/interacciones-sociales.php';
    return ob_get_clean();
}


function renderizar_evolucion_temporal($user_id) {
    ob_start();
    include plugin_dir_path(__FILE__) . '/partes/evolucion-temporal.php';
    return ob_get_clean();
}

function calcular_nivel_explorador($user_id) {
    $resueltos = get_total_problemas_resueltos($user_id); // Debe existir o crearse
    
    // 🐛 Log para debug
    error_log("👨‍💻 Usuario $user_id resolvió $resueltos problemas.");
    
    if ($resueltos >= 50) $nivel = 5;
    elseif ($resueltos >= 25) $nivel = 4;
    elseif ($resueltos >= 10) $nivel = 3;
    elseif ($resueltos >= 5) $nivel = 2;
    elseif ($resueltos >= 1) $nivel = 1;
    else $nivel = 0;

    return [
        'nivel' => $nivel,
        'valor' => $resueltos
    ];
}

function calcular_nivel_colaborador($user_id) {
    $comentarios_ia = get_comentarios_en_ia($user_id);
    $respuestas_problemas = get_respuestas_en_problemas($user_id);
    $total = $comentarios_ia + $respuestas_problemas;

    // 🐛 Debug
    error_log("💬 Usuario $user_id colaboró con $comentarios_ia en IA y $respuestas_problemas en problemas. Total: $total");

    if ($total >= 50) $nivel = 5;
    elseif ($total >= 25) $nivel = 4;
    elseif ($total >= 10) $nivel = 3;
    elseif ($total >= 5) $nivel = 2;
    elseif ($total >= 1) $nivel = 1;
    else $nivel = 0;

    return [
        'nivel' => $nivel,
        'valor' => $total
    ];
}

function calcular_nivel_valorado($user_id) {
    $total = get_likes_recibidos($user_id);

    // 🐛 Debug
    error_log("⭐ Usuario $user_id recibió $total likes en total (posts + comentarios)");

    if ($total >= 50) $nivel = 5;
    elseif ($total >= 25) $nivel = 4;
    elseif ($total >= 10) $nivel = 3;
    elseif ($total >= 5) $nivel = 2;
    elseif ($total >= 1) $nivel = 1;
    else $nivel = 0;

    return [
        'nivel' => $nivel,
        'valor' => $total
    ];
}

function calcular_nivel_multilenguaje($user_id) {
    $lenguajes = get_lenguajes_por_tipo($user_id); // ya devuelve clave => datos con 'cantidad'

    $lenguajes_usados = array_keys($lenguajes);
    $cantidad_lenguajes = count($lenguajes_usados);

    // Si no usó ningún lenguaje, nivel 0
    if ($cantidad_lenguajes === 0) {
        return [
            'nivel' => 0,
            'valor' => []
        ];
    }

    // Obtener cantidades por lenguaje
    $cantidades = array_map(fn($data) => $data['cantidad'], $lenguajes);

    // Evaluar nivel
    if ($cantidad_lenguajes >= 3 && min($cantidades) >= 10) $nivel = 5;
    elseif ($cantidad_lenguajes >= 3 && min($cantidades) >= 5) $nivel = 4;
    elseif ($cantidad_lenguajes >= 3) $nivel = 3;
    elseif ($cantidad_lenguajes == 2) $nivel = 2;
    else $nivel = 1;

    return [
        'nivel' => $nivel,
        'valor' => $lenguajes // Devolvemos el detalle para visualizaciones
    ];
}

function calcular_nivel_creador_ia($user_id) {
    $por_tipo = get_publicaciones_ia_por_tipo($user_id);
    $total = array_sum($por_tipo);

    // 🐛 Debug
    error_log("📘 Usuario $user_id creó $total publicaciones IA");

    if ($total >= 20) $nivel = 5;
    elseif ($total >= 10) $nivel = 4;
    elseif ($total >= 5) $nivel = 3;
    elseif ($total >= 3) $nivel = 2;
    elseif ($total >= 1) $nivel = 1;
    else $nivel = 0;

    return [
        'nivel' => $nivel,
        'valor' => $por_tipo // Se devuelve el detalle por tipo
    ];
}
