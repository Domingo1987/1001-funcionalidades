<?php
// Módulo usuarios: inicialización completa

require_once __DIR__ . '/roles.php';
require_once __DIR__ . '/metadatos.php';
require_once __DIR__ . '/profile-fields.php';
require_once __DIR__ . '/admin-columns.php';
require_once __DIR__ . '/filters.php';

// Solo en admin: cargar clase y hooks del administrador de usuarios
if (is_admin()) {
    require_once __DIR__ . '/admin/class-admin.php';

    $admin = new Admin();

    add_action('admin_menu', [$admin, 'add_plugin_admin_menu']);
    add_action('admin_enqueue_scripts', [$admin, 'enqueue_scripts']);
    add_action('admin_enqueue_scripts', [$admin, 'enqueue_styles']);

    add_filter('manage_users_columns', [$admin, 'add_curso_columns']);
    add_filter('manage_users_custom_column', [$admin, 'display_curso_column_content'], 10, 3);
    add_filter('manage_users_sortable_columns', [$admin, 'make_curso_columns_sortable']);
    add_action('restrict_manage_users', [$admin, 'add_curso_filters']);
    add_action('pre_get_users', [$admin, 'filter_users_by_curso']);
    add_action('show_user_profile', [$admin, 'add_curso_fields']);
    add_action('edit_user_profile', [$admin, 'add_curso_fields']);
    add_action('personal_options_update', [$admin, 'save_curso_fields']);
    add_action('edit_user_profile_update', [$admin, 'save_curso_fields']);

    add_action('wp_ajax_guardar_curso', [$admin, 'ajax_save_curso']);
    add_action('wp_ajax_eliminar_curso', [$admin, 'ajax_delete_curso']);
    add_action('wp_ajax_guardar_centro', [$admin, 'ajax_save_centro']);
    add_action('wp_ajax_eliminar_centro', [$admin, 'ajax_delete_centro']);
}
