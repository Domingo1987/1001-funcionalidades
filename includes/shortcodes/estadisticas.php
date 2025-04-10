<?php
// Shortcode [estadisticas_usuario] - Muestra estadísticas del usuario en tarjetas
function estadisticas_usuario_shortcode() {
    $user_id = get_current_user_id();
    $user_name = get_user_meta($user_id, 'first_name', true);
    $user_email = get_userdata($user_id)->user_email;

    global $wpdb;
    $num_comentarios = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE user_id = %d AND comment_approved = '1'", $user_id)
    );
    $num_problemas_comentados = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(DISTINCT comment_post_ID) FROM $wpdb->comments WHERE user_id = %d AND comment_approved = '1'", $user_id)
    );
    $num_respuestas = $wpdb->get_var(
        $wpdb->prepare("
            SELECT COUNT(*) 
            FROM $wpdb->comments c1
            INNER JOIN $wpdb->comments c2 ON c1.comment_ID = c2.comment_parent
            WHERE c1.user_id = %d AND c2.comment_approved = '1'",
        $user_id)
    );
    $num_problemas_con_respuesta = $wpdb->get_var(
        $wpdb->prepare("
            SELECT COUNT(DISTINCT c1.comment_post_ID)
            FROM $wpdb->comments c1
            INNER JOIN $wpdb->comments c2 ON c1.comment_ID = c2.comment_parent
            WHERE c1.user_id = %d AND c2.comment_approved = '1'",
        $user_id)
    );
    $fecha_primer_acceso = $wpdb->get_var(
        $wpdb->prepare("SELECT user_registered FROM $wpdb->users WHERE ID = %d", $user_id)
    );

    $output = '
    <div class="estadisticas-usuario">
        <h2 class="estadisticas-titulo">Estadísticas de ' . esc_html($user_name) . '</h2>
        <div class="estadisticas-grid">
            <div class="stat-card"><strong>Nombre:</strong> ' . esc_html($user_name) . '</div>
            <div class="stat-card"><strong>Email:</strong> ' . esc_html($user_email) . '</div>
            <div class="stat-card"><strong>Comentarios:</strong> ' . $num_comentarios . '</div>
            <div class="stat-card"><strong>Problemas comentados:</strong> ' . $num_problemas_comentados . '</div>
            <div class="stat-card"><strong>Respuestas recibidas:</strong> ' . $num_respuestas . '</div>
            <div class="stat-card"><strong>Problemas con respuesta:</strong> ' . $num_problemas_con_respuesta . '</div>
            <div class="stat-card"><strong>Primer acceso:</strong> ' . $fecha_primer_acceso . '</div>
        </div>
    </div>';
    
    return $output;
}
add_shortcode('estadisticas_usuario', 'estadisticas_usuario_shortcode');



// Shortcode [cantidad_comentarios] - Cantidad total de comentarios realizados por usuarios registrados
function mostrar_cant_comentarios_en_respuestas() {
    $cantidad = cant_comentarios_de_usuarios();
    return $cantidad;
}
add_shortcode('cantidad_comentarios', 'mostrar_cant_comentarios_en_respuestas');

// Shortcode [cantidad_codigo] - Cantidad de comentarios con bloques de código
function mostrar_cant_codigo_en_respuestas() {
    $cantidad = cant_codigo_en_respuestas();
    return $cantidad;
}
add_shortcode('cantidad_codigo', 'mostrar_cant_codigo_en_respuestas');

// Alias directo para acceder a la función de cantidad de usuarios
add_shortcode('comentarios_de_usuarios_totales_sh', 'cant_comentarios_de_usuarios');

// Alias directo para acceder a la función de cantidad de códigos
add_shortcode('actualizar_cant_codigo_sh', 'cant_codigo_en_respuestas');
