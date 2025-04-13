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

    // Últimos 5 IDs
    $ultimos_ids = $wpdb->get_col($wpdb->prepare("
        SELECT id FROM $tabla
        WHERE user_id = %d
        ORDER BY fecha_evaluacion DESC
        LIMIT 5
    ", $user_id));

    if (empty($ultimos_ids)) return 0;

    $ids_str = implode(',', array_map('intval', $ultimos_ids));

    // Promedio últimas evaluaciones
    $prom_ultimas = (float) $wpdb->get_var("
        SELECT AVG(total_puntos) FROM $tabla WHERE id IN ($ids_str)
    ");

    // Promedio del resto (usar prepare aquí)
    $prom_restante = (float) $wpdb->get_var(
        $wpdb->prepare("
            SELECT AVG(total_puntos) 
            FROM $tabla 
            WHERE user_id = %d
            AND id NOT IN ($ids_str)
        ", $user_id)
    );

    if ($prom_restante <= 0) return 0;

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
    global $wpdb;

    $resultados = $wpdb->get_results("
        SELECT t.name AS tipo, COUNT(p.ID) AS cantidad
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE p.post_type = 'inteligen_artificial'
          AND p.post_status = 'publish'
          AND p.post_author = {$user_id}
          AND tt.taxonomy = 'ia_categoria'
        GROUP BY t.term_id
    ");

    $iconos_colores = [
        'Generación de Imágenes'        => ['icono' => '🖼️', 'color' => '#3f51b5'],
        'Modelos Conversacionales'      => ['icono' => '💬', 'color' => '#009688'],
        'Generación de Audio'           => ['icono' => '🎵', 'color' => '#e91e63'],
        'Generación de Video'           => ['icono' => '🎬', 'color' => '#607d8b'],
        'Plataformas de Implementación' => ['icono' => '🛠️', 'color' => '#ff9800'],
        'Flujo'                         => ['icono' => '🔁', 'color' => '#795548'],
        'Inteligencia Artificial'       => ['icono' => '🤖', 'color' => '#673ab7'],
    ];

    $salida = [];

    foreach ($resultados as $fila) {
        $tipo = $fila->tipo;
        $cantidad = intval($fila->cantidad);
        $icono = $iconos_colores[$tipo]['icono'] ?? '📁';
        $color = $iconos_colores[$tipo]['color'] ?? '#999';

        $salida[] = [
            'tipo'     => $tipo,
            'icono'    => $icono,
            'color'    => $color,
            'cantidad' => $cantidad,
        ];
    }

    return $salida;
}


/**
 * Devuelve un array con la cantidad de likes recibidos y dados por el usuario.
 * @param int $user_id
 * @return array
 */
function get_valoraciones_ia($user_id) {
    global $wpdb;

    $sql = "
        SELECT 
            SUM(CASE WHEN pm.meta_key = 'wpdiscuz_post_rating' THEN pm.meta_value * (SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = pm.post_id AND meta_key = 'wpdiscuz_post_rating_count' LIMIT 1) ELSE 0 END) AS total_estrellas,
            SUM(CASE WHEN pm.meta_key = 'wpdiscuz_post_rating_count' THEN pm.meta_value ELSE 0 END) AS cantidad_valoraciones
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.post_type = 'inteligen_artificial'
          AND p.post_status = 'publish'
          AND p.post_author = %d
          AND pm.meta_key IN ('wpdiscuz_post_rating', 'wpdiscuz_post_rating_count')
    ";

    $datos = $wpdb->get_row($wpdb->prepare($sql, $user_id));

    return [
        'cantidad_valoraciones' => intval($datos->cantidad_valoraciones),
        'estrellas_totales'     => intval($datos->total_estrellas)
    ];
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
    global $wpdb;

    // Likes dados a publicaciones
    $likes_posts = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM wp_wc_users_rated
         WHERE user_id = %d AND rating > 0",
        $user_id
    ));

    // Likes dados a comentarios
    $likes_comentarios = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM wp_wc_users_voted
         WHERE user_id = %d",
        $user_id
    ));

    return $likes_posts + $likes_comentarios;
}

/**
 * Cantidad de likes que recibió el usuario en sus publicaciones de IA
 */
function get_likes_recibidos($user_id) {
    global $wpdb;

    // Likes recibidos en publicaciones
    $recibidos_posts = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM wp_wc_users_rated ur
         JOIN wp_posts p ON ur.post_id = p.ID
         WHERE ur.rating > 0 AND p.post_author = %d",
        $user_id
    ));

    // Likes recibidos en comentarios
    $recibidos_comentarios = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM wp_wc_users_voted uv
         JOIN wp_comments c ON uv.comment_id = c.comment_ID
         WHERE c.user_id = %d",
        $user_id
    ));

    return $recibidos_posts + $recibidos_comentarios;
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

 function render_estrellas_promedio($promedio) {
    $html = '<div class="rating-group solo-visual">';
    for ($i = 1; $i <= 5; $i++) {
        if ($promedio >= $i) {
            $html .= '<i class="fa fa-star rating__icon rating__icon--star" aria-hidden="true"></i>';
        } elseif ($promedio >= $i - 0.5) {
            $html .= '<i class="fa fa-star-half rating__icon rating__icon--star" aria-hidden="true"></i>';
        } else {
            $html .= '<i class="fa fa-star-o rating__icon rating__icon--star" aria-hidden="true"></i>';
        }
    }
    $html .= '</div>';
    return $html;
}

function get_progreso_por_categoria($user_id) {
    global $wpdb;

    // Problemas comentados por categoría
    $resueltos = $wpdb->get_results($wpdb->prepare("
        SELECT tt.term_id as id, t.name as categoria, COUNT(DISTINCT p.ID) as cantidad
        FROM {$wpdb->comments} c
        JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
        JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE c.user_id = %d
        AND tt.taxonomy = 'categorias_problemas'
        GROUP BY tt.term_id, t.name
    ", $user_id));

    // Total de problemas por categoría (ordenados por ID)
    $totales = $wpdb->get_results("
        SELECT tt.term_id as id, t.name as categoria, COUNT(p.ID) as total
        FROM {$wpdb->posts} p
        JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE tt.taxonomy = 'categorias_problemas'
        AND p.post_type = 'problema'
        AND p.post_status = 'publish'
        GROUP BY tt.term_id, t.name
        ORDER BY tt.term_id
    ");

    $resultado = [];
    foreach ($totales as $t) {
        $nombre = $t->categoria;
        $id     = $t->id;
        $total  = $t->total;
        $resuelto = 0;

        foreach ($resueltos as $r) {
            if ($r->id === $id) {
                $resuelto = $r->cantidad;
                break;
            }
        }

        $porcentaje = $total > 0 ? round(($resuelto / $total) * 100, 2) : 0;

        $resultado[] = [
            'id' => $id,
            'categoria' => $nombre,
            'porcentaje' => $porcentaje,
            'resueltos' => $resuelto,
            'total' => $total
        ];
    }

    return $resultado;
}

function get_comentarios_por_publicacion_ia($user_id) {
    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT 
            pia.id AS publicacion_id,
            pia.post_title AS titulo,
            categoria_ia.name AS categoria,
            COUNT(c.comment_ID) AS comentarios
        FROM {$wpdb->prefix}posts pia
        LEFT JOIN {$wpdb->prefix}comments c ON c.comment_post_ID = pia.id AND c.comment_approved = '1'
        LEFT JOIN {$wpdb->term_relationships} tr ON pia.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        LEFT JOIN {$wpdb->terms} categoria_ia ON tt.term_id = categoria_ia.term_id
        WHERE pia.post_type = 'inteligen_artificial'
          AND pia.post_status = 'publish'
          AND pia.post_author = %d
          AND tt.taxonomy = 'ia_categoria'
        GROUP BY pia.id, categoria_ia.name
        ORDER BY categoria_ia.name ASC
    ", $user_id);

    return $wpdb->get_results($sql);
}

function get_participacion_mensual($user_id) {
    global $wpdb;

    // Generar lista de últimos 12 meses
    $meses = [];
    $now = new DateTime();
    for ($i = 11; $i >= 0; $i--) {
        $mes = clone $now;
        $mes->modify("-$i months");
        $meses[] = $mes->format('Y-m');
    }

    // Comentarios en problemas
    $comentarios = $wpdb->get_results($wpdb->prepare("
        SELECT 
            DATE_FORMAT(c.comment_date, '%%Y-%%m') as mes,
            t.name as categoria,
            COUNT(*) as cantidad
        FROM {$wpdb->comments} c
        JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
        JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE c.user_id = %d
          AND c.comment_approved = '1'
          AND p.post_type = 'problema'
          AND tt.taxonomy = 'categorias_problemas'
          AND c.comment_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY mes, categoria
    ", $user_id));

    // Publicaciones IA
    $publicaciones = $wpdb->get_results($wpdb->prepare("
        SELECT 
            DATE_FORMAT(post_date, '%%Y-%%m') as mes,
            t.name as categoria,
            COUNT(*) as cantidad
        FROM {$wpdb->posts} p
        JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        WHERE p.post_author = %d
          AND p.post_type = 'inteligencia_artificial'
          AND p.post_status = 'publish'
          AND tt.taxonomy = 'categoria_ia'
          AND post_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY mes, categoria
    ", $user_id));

    // Combinar datos
    $datos = [];

    foreach ($comentarios as $fila) {
        $cat = $fila->categoria;
        $mes = $fila->mes;
        $datos[$cat][$mes] = ($datos[$cat][$mes] ?? 0) + (int) $fila->cantidad;
    }

    foreach ($publicaciones as $fila) {
        $cat = $fila->categoria ?: 'IA';
        $mes = $fila->mes;
        $datos[$cat][$mes] = ($datos[$cat][$mes] ?? 0) + (int) $fila->cantidad;
    }

    // Generar formato final para Apex Heatmap
    $resultado = [];

    $categorias_fijas = [
        'Introducción a la programación de computadores',
        'Conceptos generales de los lenguajes de programación',
        'Presentación del lenguaje C',
        'Procedimientos y funciones',
        'Tipos de datos definidos por el programador',
        'Tipos de datos estructurados',
        'Punteros',
        'Definición de tipos de datos dinámicos',
        'Archivos',
        'IA'
    ];
    
    foreach ($categorias_fijas as $categoria) {
        $por_mes = $datos[$categoria] ?? [];
    
        $fila = ['name' => $categoria, 'data' => []];
        foreach ($meses as $mes) {
            $etiqueta = DateTime::createFromFormat('Y-m', $mes)->format('M Y');
            $fila['data'][] = [
                'x' => $etiqueta,
                'y' => $por_mes[$mes] ?? 0
            ];
        }
    
        $resultado[] = $fila;
    }    

    return $resultado;
}

function get_colores_por_categoria() {
    return [
        'Introducción a la programación de computadores' => '#2e3897',
        'Conceptos generales de los lenguajes de programación' => '#943125',
        'Presentación del lenguaje C' => '#229434',
        'Procedimientos y funciones' => '#329292',
        'Tipos de datos definidos por el programador' => '#903292',
        'Tipos de datos estructurados' => '#929231',
        'Punteros' => '#929231',
        'Definición de tipos de datos dinámicos' => '#e41920',
        'Archivos' => '#815627',
        'IA' => '#fdff00'
    ];
}

function get_radar_series_por_usuario($user_id) {
    global $wpdb;

    // 🧠 Definí acá los criterios que querés mostrar (orden y etiquetas)
    $criterios = [
        1 => "Comprensión del Problema",
        2 => "Estructura del Código",
        3 => "Funcionalidad",
        4 => "Estrategias"
    ];
    $criterio_ids = array_keys($criterios);
    $labels = array_values($criterios);

    $series = [];

    // 🔍 Obtener promedio por criterio agrupado por práctico
    $resultados = $wpdb->get_results($wpdb->prepare("
        SELECT 
            pp.practico_id,
            p.nombre AS practico_nombre,
            ec.criterio_id,
            ROUND(AVG(ec.criterio_puntos), 2) AS promedio
        FROM wp_evaluaciones e
        JOIN wp_evaluaciones_criterios ec ON ec.evaluacion_id = e.id
        JOIN wp_practicos_problemas pp ON e.problema_id = pp.id
        JOIN wp_practicos p ON pp.practico_id = p.id
        WHERE e.user_id = %d
        GROUP BY pp.practico_id, ec.criterio_id
        ORDER BY pp.practico_id, ec.criterio_id
    ", $user_id));

    // 🧩 Agrupar resultados por práctico
    $por_practico = [];
    foreach ($resultados as $fila) {
        $pid = $fila->practico_id;
        $por_practico[$pid]['nombre'] = $fila->practico_nombre;
        $por_practico[$pid]['criterios'][$fila->criterio_id] = (float) $fila->promedio;
    }

    // 🧪 Construir cada serie con todos los criterios
    foreach ($por_practico as $practico) {
        $datos = [];
        foreach ($criterio_ids as $cid) {
            $datos[] = $practico['criterios'][$cid] ?? 0;
        }
        $series[] = [
            'name' => $practico['nombre'],
            'data' => $datos
        ];
    }

    // 📊 Calcular promedio general (serie "TODOS")
    $todos = [];
    foreach ($criterio_ids as $cid) {
        $prom = $wpdb->get_var($wpdb->prepare("
            SELECT ROUND(AVG(ec.criterio_puntos), 2)
            FROM wp_evaluaciones e
            JOIN wp_evaluaciones_criterios ec ON ec.evaluacion_id = e.id
            WHERE e.user_id = %d AND ec.criterio_id = %d
        ", $user_id, $cid));
        $todos[] = $prom ?: 0;
    }

    // ⬆️ Insertar al inicio
    array_unshift($series, [
        'name' => 'TODOS',
        'data' => $todos
    ]);

    return [
        'labels' => $labels,
        'series' => $series
    ];
}

function calcular_nivel_explorador($user_id) {
    $resueltos = get_total_problemas_resueltos($user_id); // Debe existir o crearse
    
    // 🐛 Log para debug
    error_log("👨‍💻 Usuario $user_id resolvió $resueltos problemas.");
    
    if ($resueltos >= 50) return 5;
    if ($resueltos >= 25) return 4;
    if ($resueltos >= 10) return 3;
    if ($resueltos >= 5) return 2;
    if ($resueltos >= 1) return 1;
    return 0;
}

function get_total_problemas_resueltos($user_id) {
    global $wpdb;

    // Asegurarse de que el post_type sea 'problema'
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT c.comment_post_ID)
         FROM {$wpdb->comments} c
         INNER JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
         WHERE c.user_id = %d
           AND c.comment_approved = 1
           AND p.post_type = 'problema'",
        $user_id
    ));

    return intval($count);
}

