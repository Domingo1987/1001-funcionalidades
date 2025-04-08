<?php
if (!defined('ABSPATH')) exit;

/**
 * Devuelve la cantidad total de problemas resueltos por el usuario.
 */
function get_problemas_resueltos($user_id) {
    return 145; // Simulado
}

/**
 * Devuelve el puntaje promedio del usuario.
 */
function get_puntaje_promedio($user_id) {
    return 17.8; // Simulado
}

/**
 * Devuelve la mejora o retroceso porcentual del usuario (últimas vs anteriores).
 */
function get_tendencia_porcentual($user_id) {
    return 12.5; // Simulado (positivo)
}

/**
 * Devuelve la cantidad total de comentarios hechos por el usuario.
 */
function get_cantidad_comentarios($user_id) {
    return 62; // Simulado
}

/**
 * Devuelve la cantidad de publicaciones de IA realizadas por el usuario.
 */
function get_ia_publicadas($user_id) {
    return 7; // Simulado
}

/**
 * Devuelve la cantidad de medallas logradas por el usuario.
 */
function get_cantidad_medallas($user_id) {
    return 3; // Simulado
}

/**
 * Devuelve la cantidad de horas que el usuario ha estado activo en la plataforma.
 */
function get_tiempo_total_plataforma($user_id) {
    return 29; // Simulado (en horas)
}

/**
 * Devuelve la cantidad de interacciones/acciones del usuario en la última semana.
 */
function get_actividad_semanal($user_id) {
    return 18; // Simulado
}


/**
 * ********************************************************************************
 */

/**
 * Devuelve el desglose de problemas resueltos por categoría.
 * 
 * @param int $user_id ID del usuario
 * @return array Arreglo de categorías con nombre, icono, color, total y resueltos
 */
function get_problemas_por_categoria($user_id) {
    // 🔧 Datos simulados — reemplazar con consulta a BD en el futuro
    return [
        [
            'nombre'    => 'Secuenciales',
            'icono'     => '📘',
            'color'     => '#00bcd4',
            'total'     => 40,
            'resueltos' => 28
        ],
        [
            'nombre'    => 'Condicionales',
            'icono'     => '📙',
            'color'     => '#ff9800',
            'total'     => 30,
            'resueltos' => 22
        ],
        [
            'nombre'    => 'Bucles',
            'icono'     => '📗',
            'color'     => '#4caf50',
            'total'     => 20,
            'resueltos' => 15
        ]
    ];
}

/**
 * Devuelve el porcentaje de problemas resueltos por lenguaje.
 * 
 * @param int $user_id ID del usuario
 * @return array Arreglo de lenguajes con nombre, icono, color y porcentaje
 */
function get_porcentaje_lenguajes($user_id) {
    // 🔧 Datos simulados — reemplazar con consulta a BD en el futuro
    return [
        [
            'nombre'     => 'Python',
            'icono'      => '🐍',
            'color'      => '#4caf50',
            'porcentaje' => 65
        ],
        [
            'nombre'     => 'Java',
            'icono'      => '☕',
            'color'      => '#f44336',
            'porcentaje' => 25
        ],
        [
            'nombre'     => 'C',
            'icono'      => '🔧',
            'color'      => '#2196f3',
            'porcentaje' => 10
        ]
    ];
}

/**
 * ********************************************************************************
 */

 /**
 * Devuelve los tipos de publicaciones de IA del usuario, con ícono, color y cantidad.
 * @param int $user_id
 * @return array
 */
function get_publicaciones_ia_por_tipo($user_id) {
    // Datos simulados
    return [
        ['tipo' => 'Imagen', 'icono' => '🖼️', 'color' => '#3f51b5', 'cantidad' => 4],
        ['tipo' => 'Chatbot', 'icono' => '💬', 'color' => '#009688', 'cantidad' => 2],
        ['tipo' => 'Música', 'icono' => '🎵', 'color' => '#e91e63', 'cantidad' => 1],
    ];

    // En el futuro podrías hacer una consulta a la base de datos agrupando por tipo.
}

/**
 * Devuelve un array con la cantidad de likes recibidos y dados por el usuario.
 * @param int $user_id
 * @return array
 */
function get_likes_ia($user_id) {
    // Datos simulados
    return [
        'recibidos' => 28,
        'dados' => 15,
    ];

    // Luego podrías usar $wpdb para consultar en una tabla de likes o metadatos de posts.
}


/**
 * ********************************************************************************
 */

/**
 * Devuelve un array de medallas logradas por el usuario.
 * 
 * @param int $user_id
 * @return array
 */
function get_medallas_logradas($user_id) {
    // TODO: Reemplazar con consulta a la base de datos
    return [
        ['nombre' => 'Primer Problema', 'icono' => '🥇', 'color' => '#ffd700'],
        ['nombre' => '5 Comentarios', 'icono' => '💬', 'color' => '#4caf50'],
        ['nombre' => 'IA Inicial', 'icono' => '🤖', 'color' => '#9c27b0'],
    ];
}

/**
 * Devuelve un array de medallas pendientes por el usuario.
 * 
 * @param int $user_id
 * @return array
 */
function get_medallas_pendientes($user_id) {
    // TODO: Reemplazar con consulta a la base de datos
    return [
        ['nombre' => '50 Problemas', 'icono' => '🎯'],
        ['nombre' => '10 IA Publicadas', 'icono' => '🤖'],
        ['nombre' => '100 Comentarios', 'icono' => '💬'],
    ];
}

 /**
 * ********************************************************************************
 */

/**
 * Cantidad de likes que dio el usuario en publicaciones de IA
 */
function get_likes_dados($user_id) {
    // Simulado. Reemplazar con consulta real si tenés tabla de likes.
    return 19;
}

/**
 * Cantidad de likes que recibió el usuario en sus publicaciones de IA
 */
function get_likes_recibidos($user_id) {
    // Simulado. Reemplazar con consulta real si tenés tabla de likes.
    return 11;
}

/**
 * Comentarios hechos por el usuario en problemas, entradas IA u otros
 */
function get_comentarios_hechos($user_id) {
    global $wpdb;
    return (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->comments} WHERE user_id = %d AND comment_approved = 1",
        $user_id
    ));
}

/**
 * Comentarios recibidos en publicaciones creadas por el usuario
 */
function get_comentarios_recibidos($user_id) {
    global $wpdb;

    // Contar comentarios en posts donde el usuario es autor
    return (int) $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(c.comment_ID)
        FROM {$wpdb->comments} c
        INNER JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
        WHERE p.post_author = %d AND c.comment_approved = 1
    ", $user_id));
}

  /**
 * ********************************************************************************
 */



 
 /**
 * ********************************************************************************
 */

/**
 * Simula el progreso mensual por capítulo en niveles de 0 a 5
 * Cada fila es un capítulo (Cap. 1 a Cap. 9, IA)
 * Cada columna un mes (enero a diciembre)
 * 
 * @param int $user_id
 * @return array matriz[capitulo][mes] con valores 0-5
 */
function get_progreso_mensual($user_id) {
    $matriz = [];

    for ($capitulo = 0; $capitulo < 10; $capitulo++) {
        $fila = [];
        for ($mes = 0; $mes < 12; $mes++) {
            $fila[] = rand(0, 5); // Nivel aleatorio de progreso
        }
        $matriz[] = $fila;
    }

    return $matriz;
}

  /**
 * ********************************************************************************
 */