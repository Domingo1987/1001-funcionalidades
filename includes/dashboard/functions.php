<?php

if (!defined('ABSPATH')) exit;

// Incluir archivo central de estadísticas (consulta a BD)
require_once plugin_dir_path(__FILE__) . '/../utils/estadisticas-dashboard.php';

// Módulos (secciones del dashboard)
function render_resumen_general() {
    error_log('➡️ Sección render_x cargada');
    include plugin_dir_path(__FILE__) . '/partes/resumen-general.php';
}

function render_progreso_por_categorias() {
    include plugin_dir_path(__FILE__) . '/partes/progreso-categorias.php';
}

function render_publicaciones_ia() {
    include plugin_dir_path(__FILE__) . '/partes/publicaciones-ia.php';
}

function render_medallas() {
    include plugin_dir_path(__FILE__) . '/partes/medallas.php';
}

function render_interacciones_sociales() {
    include plugin_dir_path(__FILE__) . '/partes/interacciones-sociales.php';
}

function render_progreso_por_competencias() {
    include plugin_dir_path(__FILE__) . '/partes/progreso-competencias.php';
}

function render_evolucion_temporal() {
    include plugin_dir_path(__FILE__) . '/partes/evolucion-temporal.php';
}
