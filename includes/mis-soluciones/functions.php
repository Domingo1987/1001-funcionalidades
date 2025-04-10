<?php

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
        AND tt.taxonomy = 'categorias_problemas'
    ";

    $results = $wpdb->get_results($sql);

    error_log("🎯 Problemas con categorías: " . json_encode($results)); // Debugging

    $problems_with_categories = [];
    foreach ($results as $row) {
        $problems_with_categories[] = [
            'problem_number' => $row->problem_number,
            'category' => $row->category
        ];
    }

    return $problems_with_categories;
}