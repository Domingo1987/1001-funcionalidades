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

        $val_ia = get_valoraciones_ia($user_id);
        $prom_ia = $val_ia['cantidad_valoraciones'] > 0
            ? round($val_ia['estrellas_totales'] / $val_ia['cantidad_valoraciones'], 1)
            : 0;

        wp_localize_script('dashboard-js', 'dashboardData', [
            // 🟢 RESUMEN GENERAL
            'problemas' => get_problemas_resueltos($user_id),
            'puntaje' => get_puntaje_promedio($user_id),
            'tendencia' => get_tendencia_porcentual($user_id),
            'comentarios' => get_cantidad_comentarios($user_id),
            'ia_posts' => get_ia_publicadas($user_id),
            'medallas' => get_cantidad_medallas($user_id),
            'preguntas' => get_preguntas_creadas($user_id),
            'actividadSemanal' => get_actividad_semanal($user_id),

            // 🟢 PUBLICACIONES IA
            'publicacionesIA' => get_publicaciones_ia_por_tipo($user_id),
            'valoracionesIA' => $val_ia,
            'promedioIA' => $prom_ia,

            // 🟢 MEDALLAS
            'medallasLogradas' => get_medallas_logradas($user_id),
            'medallasPendientes' => get_medallas_pendientes($user_id),

            // 🟢 INTERACCIONES
            'likesDados' => get_likes_dados($user_id),
            'likesRecibidos' => get_likes_recibidos($user_id),
            'comentariosHechos' => get_comentarios_hechos($user_id),
            'comentariosRecibidos' => get_comentarios_recibidos($user_id),

            // 🟢 EVOLUCIÓN TEMPORAL
            'progresoMensual' => get_progreso_mensual($user_id),

            // 🟢 PROGRESO POR CATEGORÍA
            'progresoPorCategoria' => get_progreso_por_categoria($user_id)
        ]);
    }
});