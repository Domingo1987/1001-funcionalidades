<?php
if (!defined('ABSPATH')) {
    exit;
}

// Agrega columnas en la tabla de usuarios
function cienuno_columnas_personalizadas($columns) {
    $columns['curso'] = 'Curso';
    $columns['centro'] = 'Centro';
    $columns['anio'] = 'Año';
    return $columns;
}
add_filter('manage_users_columns', 'cienuno_columnas_personalizadas');

function cienuno_valores_columnas($value, $column_name, $user_id) {
    if ($column_name == 'curso') {
        return get_user_meta($user_id, 'curso', true);
    }
    if ($column_name == 'centro') {
        return get_user_meta($user_id, 'centro', true);
    }
    if ($column_name == 'anio') {
        return get_user_meta($user_id, 'anio', true);
    }
    return $value;
}
add_filter('manage_users_custom_column', 'cienuno_valores_columnas', 10, 3);
