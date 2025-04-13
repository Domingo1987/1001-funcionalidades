<?php

// 🚀 Shortcode principal del dashboard
require_once __DIR__ . '/shortcode.php';

// 🧠 Funciones que renderizan las secciones
require_once __DIR__ . '/functions.php';

// 📊 Funciones de estadísticas (consultas a la base de datos)
require_once plugin_dir_path(__FILE__) . '/../utils/estadisticas-dashboard.php';

// 🎨 Enqueue de scripts solo en la página del dashboard
add_action('wp_enqueue_scripts', function () {
    if (is_page('dashboard')) {

        // Scripts JS
        wp_enqueue_script(
            'apexcharts',
            'https://cdn.jsdelivr.net/npm/apexcharts',
            [],
            null,
            true
        );

        wp_enqueue_script(
            'dashboard-js',
            FUNC_URL . 'assets/js/dashboard.js',
            ['apexcharts'],
            '1.0',
            true
        );

        // Obtener todos los datos del usuario logueado
        $user_id = get_current_user_id();

        wp_localize_script('dashboard-js', 'dashboardData', [
            // 🟢 PROGRESO POR CATEGORÍA
            'heatmapData' => get_participacion_mensual($user_id),
            'coloresCategorias' => get_colores_por_categoria(),
            'progresoPorCategoria' => get_progreso_por_categoria($user_id),
            'interaccionesIA' => get_comentarios_por_publicacion_ia($user_id),
            'radarCompetencias' => get_radar_series_por_usuario($user_id)
        ]);
    }
});