<?php
// Archivo: utils/problemas.php

// Función: Obtener número de problema (v2.0, acepta ID opcional)
function get_num_problema($post_id = null) {
    if ($post_id === null) {
        global $post;
        $post_id = $post->ID;
    }
    return get_post_meta($post_id, 'num_problema', true);
}

// 🧩 Función: Verificar si el usuario comentó en el post actual
function post_comentado_por_usuario() {
    $post_id = get_the_ID();
    $user_id = get_current_user_id();

    $comments = get_comments(array(
        'post_id' => $post_id,
        'status' => 'approve'
    ));

    foreach ($comments as $comment) {
        if ($comment->user_id == $user_id) return true;
    }

    return false;
}

// 🧩 Función: Obtener cantidad de problemas distintos comentados por el usuario
function get_num_problemas_comentados() {
    $user_id = get_current_user_id();
    global $wpdb;
    return $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(DISTINCT comment_post_ID) FROM $wpdb->comments WHERE user_id = %d AND comment_approved = '1'",
            $user_id
        )
    );
}

// 📊 Función: Obtener lista de problemas comentados con cantidad de comentarios
function get_user_comments_problems() {
    global $wpdb;
    $sql = "
        SELECT comment_post_ID, COUNT(*) as comment_count 
        FROM $wpdb->comments 
        WHERE user_id = " . get_current_user_id() . " 
        GROUP BY comment_post_ID";

    $posts_con_comentarios = $wpdb->get_results($sql);
    
    $problem_numbers = [];
    foreach ($posts_con_comentarios as $post) {
        $problem_number = get_post_meta($post->comment_post_ID, 'num_problema', true);
        if ($problem_number) {
            $problem_numbers[] = [
                'number' => $problem_number,
                'count' => $post->comment_count
            ];
        }
    }
    
    return $problem_numbers;
}

// 📊 Función: Obtener problemas comentados por el usuario con sus categorías
function get_user_problems_with_categories() {
    global $wpdb;

    $sql = "
        SELECT comment_post_ID, COUNT(*) as comment_count 
        FROM $wpdb->comments 
        WHERE user_id = " . get_current_user_id() . " 
        GROUP BY comment_post_ID";
    
    $posts_con_comentarios = $wpdb->get_results($sql);
    
    $problem_numbers = [];
    foreach ($posts_con_comentarios as $post) {
        $problem_number = get_post_meta($post->comment_post_ID, 'num_problema', true);
        if ($problem_number) {
            $problem_numbers[] = $problem_number;
        }
    }

    if (empty($problem_numbers)) return [];

    $problem_numbers_list = implode(',', array_map('intval', $problem_numbers));

    $sql = "
        SELECT pm.meta_value as problem_number, t.name as category
        FROM wp_posts p
        JOIN wp_postmeta pm ON p.ID = pm.post_id
        JOIN wp_term_relationships tr ON p.ID = tr.object_id
        JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        JOIN wp_terms t ON tt.term_id = t.term_id
        WHERE pm.meta_key = 'num_problema' AND pm.meta_value IN ($problem_numbers_list)
        AND p.post_status != 'inherit'
        AND tt.taxonomy = 'category'
        AND t.name != 'Blog';
    ";

    $results = $wpdb->get_results($sql);

    $problems_with_categories = [];
    foreach ($results as $row) {
        $problems_with_categories[] = [
            'problem_number' => $row->problem_number,
            'category' => $row->category
        ];
    }

    return $problems_with_categories;
}

// 🔢 Función: Contar cuántos comentarios incluyen bloques de código
function cant_codigo_en_respuestas() {
    global $wpdb;

    $query = "
        SELECT COUNT(*) 
        FROM {$wpdb->comments}
        WHERE comment_approved = 1 
        AND comment_approved != 'trash' 
        AND comment_approved != 'post-trashed' 
        AND comment_content LIKE '%ql-syntax%'
    ";

    return $wpdb->get_var($query);
}

// 🔢 Función: Contar la cantidad total de comentarios realizados por usuarios registrados
function cant_comentarios_de_usuarios() {
    global $wpdb;

    $query = "
        SELECT COUNT(*) 
        FROM {$wpdb->comments} 
        WHERE user_id != 0 
        AND comment_approved = 1 
        AND comment_approved != 'trash' 
        AND comment_approved != 'post-trashed'";
    
    return $wpdb->get_var($query);
}

// 🔁 Función: Redirigir al usuario a un problema que aún no ha comentado
function problema_azar() {
    global $wpdb;

    $sql = "SELECT post_id FROM $wpdb->comments WHERE user_id = " . get_current_user_id();
    $posts_con_comentarios = $wpdb->get_col($sql);

    $posts_sin_comentarios = array_diff(
        get_posts(array('meta_key' => 'num_problema', 'fields' => 'ids')),
        $posts_con_comentarios
    );

    if (!empty($posts_sin_comentarios)) {
        $post_id = $posts_sin_comentarios[array_rand($posts_sin_comentarios)];
        $num_problema = get_post_meta($post_id, 'num_problema', true);
        $url = 'https://pruebas.1001problemas.com/problema-' . $num_problema;
        wp_redirect($url);
        exit();
    } else {
        echo 'No hay más problemas sin comentar';
    }
}

function get_problemas_para_grid($offset = 0, $limite = 100, $slug_categoria = null) {
    global $wpdb;

    $comentados = get_user_comments_problems();
    $comentados_nums = array_column($comentados, 'number');

    // Modo aleatorio si está activa la sesión con IDs
    if (isset($_SESSION['ids_problemas_aleatorios'])) {
        $todos_los_ids = $_SESSION['ids_problemas_aleatorios'];
        $ids_slice = array_slice($todos_los_ids, $offset, $limite);

        if (empty($ids_slice)) return [];

        $placeholders = implode(',', array_fill(0, count($ids_slice), '%d'));

        $sql = "
            SELECT 
                p.ID,
                p.post_title,
                p.post_content,
                pm.meta_value AS num_problema,
                tt.term_taxonomy_id
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'num_problema'
            JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE p.ID IN ($placeholders)
            AND tt.taxonomy = 'category'
            GROUP BY p.ID
        ";

        $query = $wpdb->prepare($sql, ...$ids_slice);
    } else {
        // Modo clásico por categoría u orden normal
        $categoria_join = '';
        $categoria_where = '';

        if ($slug_categoria) {
            $categoria_join = "
                JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            ";
            $categoria_where = $wpdb->prepare("AND t.slug = %s", $slug_categoria);
        }

        $sql = "
            SELECT 
                p.ID,
                p.post_title,
                p.post_content,
                pm.meta_value AS num_problema,
                tt.term_taxonomy_id
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'num_problema'
            JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            $categoria_join
            WHERE p.post_status = 'publish'
            AND p.post_type = 'post'
            AND tt.taxonomy = 'category'
            AND pm.meta_value != ''
            $categoria_where
            GROUP BY p.ID
            ORDER BY CAST(pm.meta_value AS UNSIGNED)
            LIMIT %d OFFSET %d
        ";

        $query = $wpdb->prepare($sql, $limite, $offset);
    }

    $resultados = $wpdb->get_results($query);

    $problemas = [];

    foreach ($resultados as $post) {
        $num = $post->num_problema;
        $comentado = in_array($num, $comentados_nums);
        $term_id = intval($post->term_taxonomy_id);

        $img_index = $term_id - 2;
        $nombre_imagen = "cap{$img_index}-v5" . ($comentado ? '-gris' : '');
        $imagen = FUNC_URL . "assets/img/{$nombre_imagen}.png";

        $problemas[] = [
            'id' => $post->ID,
            'titulo' => $post->post_title,
            'num' => $num,
            'term_taxonomy_id' => $term_id,
            'letra' => wp_trim_words(strip_tags($post->post_content), 15, '...'),
            'comentado' => $comentado,
            'imagen' => $imagen,
            'url' => get_permalink($post->ID)
        ];
    }

    return $problemas;
}


/**
 * Devuelve la URL de imagen basada en la categoría del post (usando taxonomía personalizada).
 * @param int $post_id
 * @param bool $comentado
 * @return string URL de imagen
 */
function get_imagen_categoria($post_id, $comentado = false) {
    $terms = get_the_terms($post_id, 'categorias_problemas');

    if (!empty($terms) && !is_wp_error($terms)) {
        $ids = array_map(fn($t) => $t->term_taxonomy_id, $terms);
        error_log('🔢 Term Taxonomy IDs para post ' . $post_id . ': ' . implode(', ', $ids));

        foreach ($terms as $term) {
            $tax_id = $term->term_taxonomy_id;
            if ($tax_id >= 54 && $tax_id <= 62) {
                $num_imagen = $tax_id - 53; // Cap1 empieza en 1
                $nombre = 'cap' . $num_imagen . '-v5';
                if ($comentado) $nombre .= '-gris';
                return FUNC_URL . 'assets/img/' . $nombre . '.png';
            }
        }
    }

    return FUNC_URL . 'assets/img/default.png';
}
