<?php
// Archivo: problema-num.php

// Acción AJAX: obtener el número de problema mediante el ID del post recibido por POST
function get_num_problema_ajax() {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

    error_log('ID del post recibido: ' . $post_id);  // Log de depuración

    $num_problema = get_num_problema($post_id);
    if (empty($num_problema)) {
        $num_problema = 'desconocido';
    }

    echo $num_problema;
    wp_die();
}
add_action('wp_ajax_get_num_problema', 'get_num_problema_ajax');
add_action('wp_ajax_nopriv_get_num_problema', 'get_num_problema_ajax');
