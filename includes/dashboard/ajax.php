<?php
// Archivo: includes/dashboard/ajax.php

if (!defined('ABSPATH')) exit;

// Hook para usuarios logueados
add_action('wp_ajax_dashboard_cargar_seccion', 'dashboard_ajax_cargar_seccion');

// Hook opcional si algún día habilitás para visitantes (no obligatorio ahora)
// add_action('wp_ajax_nopriv_dashboard_cargar_seccion', 'dashboard_ajax_cargar_seccion');

function dashboard_ajax_cargar_seccion() {
    if (!is_user_logged_in()) wp_die();

    $user_id = get_current_user_id();
    $seccion = sanitize_text_field($_POST['seccion'] ?? '');

    // Validación de secciones permitidas (opcional y recomendable)
    $secciones_validas = [
        'resumen-general',
        'progreso-categorias',
        'publicaciones-ia',
        'progreso-competencias',
        'medallas',
        'interacciones-sociales',
        'evolucion-temporal',
        'actividad-por-tipo',
    ];

    if (!in_array($seccion, $secciones_validas)) {
        echo "<p class='text-danger'>❌ Sección inválida</p>";
        wp_die();
    }

    // Clave del transient
    $transient_key = "dashboard_{$seccion}_{$user_id}";

    // Verificar si ya está cacheado
    $html = get_transient($transient_key);

    if ($html === false) {
        require_once __DIR__ . '/functions.php';
        require_once __DIR__ . '../../utils/estadisticas-dashboard.php';

        // Construir nombre de la función a ejecutar
        $func = "renderizar_" . str_replace('-', '_', $seccion);

        if (function_exists($func)) {
            $html = $func($user_id);
            // Guardar en caché por 10 minutos
            set_transient($transient_key, $html, 10 * MINUTE_IN_SECONDS);
        } else {
            $html = "<p class='text-danger'>❌ Función no encontrada para la sección</p>";
        }
    }

    echo $html;
    wp_die();
}
