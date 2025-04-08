<?php
// Archivo: comentarios.php

// Filtro para ocultar los comentarios si el usuario no ha comentado aún
// Aplica solo en páginas individuales de tipo post (is_single)
function ocultar_comentarios_hasta_comentar($comment_text) {
    $current_user = wp_get_current_user();

    if (is_singular('problema')) {

        if (!is_user_logged_in()) {
            $comments = get_comments(array('post_id' => get_the_ID()));
            if (count($comments) > 0) {
                return '<div class="hidden-comments">Comentarios ocultos hasta que realices un comentario.</div>';
            }
        } elseif ($current_user->ID != 1) { // Se exceptúa al admin ID 1
            $user_comments = get_comments(array(
                'user_id' => $current_user->ID,
                'post_id' => get_the_ID(),
                'count' => true
            ));
            if ($user_comments == 0) {
                return '<div class="hidden-comments">ESTÁS LOGUEADO ' . esc_html($current_user->display_name) . '! Pero... Los comentarios permanecerán ocultos hasta que respondas tu solución.</div>';
            }
        }
    }

    return $comment_text;
}
add_filter('comment_text', 'ocultar_comentarios_hasta_comentar');

// 🔁 Hook: actualizar cantidad de respuestas con código al hacer login
add_action('wp_login', 'actualizar_cant_codigo');
function actualizar_cant_codigo() {
    $cantidad = cant_codigo_en_respuestas();
    update_option('cant_codigo', $cantidad);
}

// 🔁 Hook: actualizar cantidad de comentarios de usuarios al login
add_action('wp_login', 'actualizar_comentarios_de_usuarios');
function actualizar_comentarios_de_usuarios() {
    $cantidad = cant_comentarios_de_usuarios();
    update_option('comentarios_de_usuarios', $cantidad);
}

// 🛡️ Seguridad: restringir rutas REST sensibles si el usuario no está logueado
add_filter('rest_endpoints', function($endpoints) {
    if (!is_user_logged_in()) {
        unset($endpoints['/wp/v2/users']);
        unset($endpoints['/wp/v2/comments']);
    }
    return $endpoints;
});

// 🛡️ Ocultar links REST API del <head> y headers HTTP
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11);