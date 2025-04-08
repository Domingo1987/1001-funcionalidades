<?php
// Shortcode [problema_azar capitulo="X"]
function problema_azar_shortcode($atts) {
    global $wpdb;

    $atts = shortcode_atts(['capitulo' => ''], $atts);
    $capitulo = intval($atts['capitulo']);
    $capitulo = intval($atts['capitulo']);
    $term_id = $capitulo > 0 ? $capitulo + 53 : 0;
    $user_id = get_current_user_id();

    $where_taxonomy = '';
    $join_taxonomy = '';
    $where_comments = '';

    error_log('🔍 Shortcode [problema_azar] activado');
    error_log('👤 Usuario ID: ' . $user_id);
    error_log('📘 Capítulo recibido: ' . $capitulo);

    // Filtrar por categoría (si se pasa el atributo)
    if ($capitulo > 0) {
        $join_taxonomy = "INNER JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)
                          INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)";
        $where_taxonomy = $wpdb->prepare("AND tt.taxonomy = 'categorias_problemas' AND tt.term_id = %d", $term_id);
    }

    // Si el usuario está logueado, evitar los ya comentados
    if ($user_id > 0) {
        $where_comments = $wpdb->prepare(
            "AND NOT EXISTS (
                SELECT 1 FROM {$wpdb->comments} c
                WHERE c.comment_post_ID = p.ID AND c.user_id = %d AND c.comment_approved = 1
            )",
            $user_id
        );
    }

    // Query final
    $sql = "
        SELECT DISTINCT CAST(pm.meta_value AS UNSIGNED) AS num_problema
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id)
        $join_taxonomy
        WHERE p.post_type = 'problema'
          AND p.post_status = 'publish'
          AND pm.meta_key = 'num_problema'
          $where_taxonomy
          $where_comments
        ORDER BY RAND()
        LIMIT 1
    ";

    error_log('🧠 SQL ejecutado: ' . $sql);

    $num_problema = $wpdb->get_var($sql);

    if ($num_problema) {
        error_log('🎯 Redirigiendo a problema: ' . $num_problema);
        wp_redirect("https://pruebas.1001problemas.com/problema/problema-$num_problema");
        exit;
    } else {
        error_log('😢 No se encontró ningún problema. Redirigiendo a /felicitaciones');
        wp_redirect('https://pruebas.1001problemas.com/felicitaciones');
        exit;
    }

    exit;
}
add_shortcode('problema_azar', 'problema_azar_shortcode');



/*
function problema_azar_shortcode() {
    ob_start();
    global $wpdb;

    $sql = "SELECT comment_post_ID FROM $wpdb->comments WHERE user_id = " . get_current_user_id();
    $comentados = $wpdb->get_col($sql);

    $todos = get_posts(array(
        'post_type' => 'problema',
        'meta_key' => 'num_problema',
        'fields' => 'ids',
        'posts_per_page' => -1
    ));

    $sin_comentar = array_diff($todos, $comentados);

    if (!empty($sin_comentar)) {
        $post_id = $sin_comentar[array_rand($sin_comentar)];
        $num = get_post_meta($post_id, 'num_problema', true);
        // TODO: mirar esto de la ruta
        error_log('Problema al azar: ' . $num);
        $url = 'https://pruebas.1001problemas.com/problema/problema-' . $num;
        //echo '<a href="' . esc_url($url) . '">Problema al azar</a>';
        wp_redirect('https://pruebas.1001problemas.com/problema/problema-' . $num);
        exit;
    } else {
        //echo 'No hay más problemas sin comentar';
        wp_redirect('https://pruebas.1001problemas.com/felicitaciones');
        exit;
    }

    //return ob_get_clean();
}
add_shortcode('problema_azar', 'problema_azar_shortcode');


// Shortcode [problemas_usuario] - Redirige automáticamente a un problema no comentado
function problemas_usuario_shortcode() {
    $user_id = get_current_user_id();

    $comments = get_comments(array('user_id' => $user_id, 'status' => 'approve'));
    $comentados = array_map(fn($c) => $c->comment_post_ID, $comments);

    $todos = get_posts(array(
        'post_type' => 'problema',
        'meta_key' => 'num_problema',
        'fields' => 'ids',
        'posts_per_page' => -1
    ));

    $sin_comentar = array_diff($todos, $comentados);

    if (empty($sin_comentar)) {
        wp_redirect('https://pruebas.1001problemas.com/felicitaciones');
        exit;
    } else {
        $post_id = reset($sin_comentar);
        $num = get_post_meta($post_id, 'num_problema', true);
        wp_redirect('https://pruebas.1001problemas.com/problema-' . $num);
        exit;
    }
}
add_shortcode('problemas_usuario', 'problemas_usuario_shortcode');


// Shortcode [problema_azar_cap capitulo=54] - Redirige a un problema al azar dentro de una categoría (nueva taxonomía)
function problema_azar_cap_shortcode($atts) {
    $capitulo = intval($atts['capitulo']); // ID del término en categorias_problemas
    global $wpdb;

    $num_problema = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT DISTINCT CAST(pm.meta_value AS UNSIGNED) AS num_problema
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)
            INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
            INNER JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id)
            WHERE p.post_type = 'problema'
              AND p.post_status = 'publish'
              AND tt.taxonomy = 'categorias_problemas'
              AND tt.term_id = %d
              AND pm.meta_key = 'num_problema'
              AND NOT EXISTS (
                  SELECT 1 FROM {$wpdb->comments} c
                  WHERE c.comment_post_ID = p.ID AND c.user_id = %d AND c.comment_approved = 1
              )
            ORDER BY RAND()
            LIMIT 1",
            $capitulo,
            get_current_user_id()
        )
    );

    if (!$num_problema) {
        wp_redirect('https://pruebas.1001problemas.com/felicitaciones-capitulo');
        exit;
    }

    $url = 'https://pruebas.1001problemas.com/problema-' . $num_problema;
    wp_redirect($url);
    exit;
}
add_shortcode('problema_azar_cap', 'problema_azar_cap_shortcode');


// Shortcode [num_problema] - Devuelve el número de problema del post actual
function num_problema_shortcode() {
    return get_post_meta(get_the_ID(), 'num_problema', true);
}
add_shortcode('num_problema', 'num_problema_shortcode');
*/