<?php
if (!defined('ABSPATH')) {
    exit;
}

// Guarda metadatos: curso, centro, cohorte
function cienuno_guardar_metadatos_usuario($user_id) {
    if (!current_user_can('edit_user', $user_id)) return false;

    update_user_meta($user_id, 'curso', sanitize_text_field($_POST['curso']));
    update_user_meta($user_id, 'centro', sanitize_text_field($_POST['centro']));
    update_user_meta($user_id, 'anio', intval($_POST['anio']));
}
add_action('personal_options_update', 'cienuno_guardar_metadatos_usuario');
add_action('edit_user_profile_update', 'cienuno_guardar_metadatos_usuario');
