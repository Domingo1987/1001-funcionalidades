<?php
if (!defined('ABSPATH')) exit;

/**
 * Devuelve la cantidad total de problemas resueltos por el usuario.
 */
function get_problemas_resueltos($user_id) {
    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT COUNT(DISTINCT p.ID)
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->comments} c ON p.ID = c.comment_post_ID
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE c.user_id = %d
        AND pm.meta_key = 'num_problema'
        AND p.post_status = 'publish'
    ", $user_id);

    return (int) $wpdb->get_var($sql);
}

/**
 * Devuelve el puntaje promedio del usuario.
 */
function get_puntaje_promedio($user_id) {
    global $wpdb;

    $tabla = "{$wpdb->prefix}evaluaciones";

    $sql = $wpdb->prepare("
        SELECT AVG(total_puntos)
        FROM $tabla
        WHERE user_id = %d
    ", $user_id);

    return round((float) $wpdb->get_var($sql), 2); // Devuelve por ejemplo 17.85
}


/**
 * Devuelve la mejora o retroceso porcentual del usuario (últimas vs anteriores).
 */
function get_tendencia_porcentual($user_id) {
    global $wpdb;

    $tabla = "{$wpdb->prefix}evaluaciones";

    // Obtener los últimos 5 IDs de evaluaciones del usuario
    $ultimos_ids = $wpdb->get_col($wpdb->prepare("
        SELECT id FROM $tabla
        WHERE user_id = %d
        ORDER BY fecha_evaluacion DESC
        LIMIT 5
    ", $user_id));

    if (empty($ultimos_ids)) return 0;

    $ids_str = implode(',', array_map('intval', $ultimos_ids));

    // Calcular promedio de esas últimas evaluaciones
    $prom_ultimas = (float) $wpdb->get_var("SELECT AVG(total_puntos) FROM $tabla WHERE id IN ($ids_str)");

    // Calcular promedio del resto (histórico anterior)
    $prom_restante = (float) $wpdb->get_var("
        SELECT AVG(total_puntos) 
        FROM $tabla 
        WHERE user_id = %d
        AND id NOT IN ($ids_str)
    ", $user_id);

    // Si no hay datos previos suficientes
    if ($prom_restante <= 0) return 0;

    // Cálculo de la tendencia
    $tendencia = (($prom_ultimas - $prom_restante) / $prom_restante) * 100;
    return round($tendencia, 2);
}



/**
 * Devuelve la cantidad total de comentarios hechos por el usuario.
 */
function get_cantidad_comentarios($user_id) {
    global $wpdb;

    // Cuenta todos los comentarios hechos por el usuario (estatus aprobados o en moderación)
    $cantidad = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*) 
        FROM {$wpdb->comments}
        WHERE user_id = %d
    ", $user_id));

    return (int) $cantidad;
}


/**
 * Devuelve la cantidad de publicaciones de IA realizadas por el usuario.
 */
function get_ia_publicadas($user_id) {
    global $wpdb;

    $cantidad = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*) 
        FROM {$wpdb->posts}
        WHERE post_type = 'inteligen_artificial'
        AND post_status = 'publish'
        AND post_author = %d
    ", $user_id));

    return (int) $cantidad;
}


/**
 * Devuelve la cantidad de medallas logradas por el usuario.
 */
function get_cantidad_medallas($user_id) {
    return 1001; // Simulado
}

/**
 * Devuelve la cantidad de horas que el usuario ha estado activo en la plataforma.
 */
function get_preguntas_creadas($user_id) {
    return 1001; // Simulado (en horas)
}

/**
 * Devuelve la cantidad de interacciones/acciones del usuario en la última semana.
 */
function get_actividad_semanal($user_id) {
    global $wpdb;

    $hoy = current_time('mysql');
    $hace_7_dias = date('Y-m-d H:i:s', strtotime('-7 days', strtotime($hoy)));

    // Comentarios hechos en los últimos 7 días
    $comentarios = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->comments} 
         WHERE user_id = %d AND comment_date >= %s",
        $user_id,
        $hace_7_dias
    ));

    // Publicaciones IA en los últimos 7 días
    $posts_ia = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} 
         WHERE post_author = %d 
         AND post_type = 'inteligen_artificial'
         AND post_status = 'publish'
         AND post_date >= %s",
        $user_id,
        $hace_7_dias
    ));

    return intval($comentarios) + intval($posts_ia);
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
    global $wpdb;

    // Obtener todas las categorías de la taxonomía personalizada ordenadas por term_id
    $categorias = get_terms([
        'taxonomy'   => 'categorias_problemas',
        'hide_empty' => false,
        'orderby'    => 'term_id',
        'order'      => 'ASC',
    ]);

    $resultado = [];

    foreach ($categorias as $cat) {
        // Total de problemas publicados en esta categoría
        $total = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(DISTINCT p.ID)
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            WHERE tr.term_taxonomy_id = %d
              AND p.post_type = 'problema'
              AND p.post_status = 'publish'
        ", $cat->term_id));

        // Total resueltos por el usuario (si comentó al menos una vez)
        $resueltos = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(DISTINCT p.ID)
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            INNER JOIN {$wpdb->comments} c ON p.ID = c.comment_post_ID
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE tr.term_taxonomy_id = %d
              AND p.post_type = 'problema'
              AND p.post_status = 'publish'
              AND pm.meta_key = 'num_problema'
              AND c.user_id = %d
        ", $cat->term_id, $user_id));

        // Íconos y colores por slug
        $icono = '📂';
        $color = '#999';
        switch ($cat->slug) {
            case 'capitulo-1': $icono = '1️⃣'; $color = '#3B5BA0'; break;
            case 'capitulo-2': $icono = '2️⃣'; $color = '#A84537'; break;
            case 'capitulo-3': $icono = '3️⃣'; $color = '#3BAA57'; break;
            case 'capitulo-4': $icono = '4️⃣'; $color = '#3DA7B3'; break;
            case 'capitulo-5': $icono = '5️⃣'; $color = '#A15EB6'; break;
            case 'capitulo-6': $icono = '6️⃣'; $color = '#B6A946'; break;
            case 'capitulo-7': $icono = '7️⃣'; $color = '#FF8C32'; break;
            case 'capitulo-8': $icono = '8️⃣'; $color = '#E53935'; break;
            case 'capitulo-9': $icono = '9️⃣'; $color = '#B67C4A'; break;
        }

        // Armar el array de respuesta
        $resultado[] = [
            'nombre'    => $cat->name,
            'icono'     => $icono,
            'color'     => $color,
            'total'     => intval($total),
            'resueltos' => intval($resueltos),
        ];
    }

    return $resultado;
}



/**
 * Devuelve el porcentaje de problemas resueltos por lenguaje.
 * 
 * @param int $user_id ID del usuario
 * @return array Arreglo de lenguajes con nombre, icono, color y porcentaje
 */
function get_porcentaje_lenguajes($user_id) {
    global $wpdb;

    // Contar la cantidad de comentarios por lenguaje
    $resultados = $wpdb->get_results($wpdb->prepare("
        SELECT cm.meta_value AS lenguaje, COUNT(*) AS cantidad
        FROM {$wpdb->comments} c
        INNER JOIN {$wpdb->commentmeta} cm ON c.comment_ID = cm.comment_id
        WHERE c.user_id = %d
          AND cm.meta_key = 'lenguaje_usado'
        GROUP BY cm.meta_value
    ", $user_id), OBJECT_K);

    // Total de todos los comentarios con lenguaje registrado
    $total = array_sum(array_map(fn($r) => $r->cantidad, $resultados));
    if ($total === 0) return [];

    // Definir íconos y colores por lenguaje
    $info_lenguajes = [
        'python' => ['nombre' => 'Python', 'icono' => '🐍', 'color' => '#4caf50'],
        'java'   => ['nombre' => 'Java',   'icono' => '☕', 'color' => '#f44336'],
        'c'      => ['nombre' => 'C',      'icono' => '🔧', 'color' => '#2196f3'],
    ];

    // Armar el resultado
    $resultado = [];
    foreach ($resultados as $lenguaje => $datos) {
        $clave = strtolower($lenguaje);
        if (!isset($info_lenguajes[$clave])) continue;

        $porcentaje = round(($datos->cantidad / $total) * 100);

        $resultado[] = [
            'nombre'     => $info_lenguajes[$clave]['nombre'],
            'icono'      => $info_lenguajes[$clave]['icono'],
            'color'      => $info_lenguajes[$clave]['color'],
            'porcentaje' => $porcentaje
        ];
    }

    return $resultado;
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