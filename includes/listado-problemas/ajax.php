<?php
// Archivo: includes/ajax/cargar-problemas.php
add_action('wp_ajax_cargar_mas_problemas', 'ajax_cargar_mas_problemas');
add_action('wp_ajax_nopriv_cargar_mas_problemas', 'ajax_cargar_mas_problemas');

// ✅ 1. Función que guarda los IDs aleatorios
function obtener_ids_problemas_aleatorios() {
    if (!isset($_SESSION['ids_problemas_aleatorios'])) {
        global $wpdb;

        $ids = $wpdb->get_col("
            SELECT DISTINCT p.ID FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'num_problema'
            INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE p.post_status = 'publish'
              AND tt.taxonomy = 'categorias_problemas'
        ");

        shuffle($ids); // ⚠️ Asegura mezcla al crear

        // Extra: volver a mezclar por si se regeneró en pruebas
        $_SESSION['ids_problemas_aleatorios'] = $ids;
    }
    return $_SESSION['ids_problemas_aleatorios'];
}

// ✅ 2. Función AJAX
function ajax_cargar_mas_problemas() {
    session_start();
    unset($_SESSION['ids_problemas_aleatorios']);

    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $limite = 100;

    $ids_aleatorios = obtener_ids_problemas_aleatorios();
    $ids_para_mostrar = array_slice($ids_aleatorios, $offset, $limite);

    error_log("🔀 IDs seleccionados para mostrar (offset $offset): " . json_encode($ids_para_mostrar));

    if (empty($ids_para_mostrar)) {
        wp_send_json_success([]);
    }

    global $wpdb;

    $placeholders = implode(',', array_fill(0, count($ids_para_mostrar), '%d'));

    $sql = "
        SELECT p.ID, pm.meta_value AS num_problema, p.post_content, tt.term_taxonomy_id
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'num_problema'
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE p.ID IN ($placeholders)
          AND tt.taxonomy = 'categorias_problemas'
    ";

    $query = $wpdb->prepare($sql, ...$ids_para_mostrar);
    $resultados = $wpdb->get_results($query);
    shuffle($resultados);

    $comentados = get_user_comments_problems();
    $comentados_nums = array_column($comentados, 'number');

    $problemas = [];
    foreach ($resultados as $fila) {
        $num = intval($fila->num_problema);
        $comentado = in_array($num, $comentados_nums);
        $img_index = intval($fila->term_taxonomy_id) - 53;
        $imagen = FUNC_URL . 'assets/img/cap' . $img_index . '-v5' . ($comentado ? '-gris' : '') . '.png';

        $problemas[] = [
            'num'            => $num,
            'comentado'      => $comentado,
            'imagen'         => $imagen,
            'url'            => get_permalink($fila->ID),
            'letra_completa' => strip_tags($fila->post_content),
        ];
    }

    error_log('🟦 Problemas cargados: ' . json_encode(array_column($problemas, 'num')));
    wp_send_json_success($problemas);
}