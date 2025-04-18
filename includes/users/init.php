<?php
if (!defined('ABSPATH')) {
    exit;
}
// Solo en admin: cargar clase y hooks del administrador de usuarios
if (is_admin()) {
    require_once __DIR__ . '/admin/class-admin.php';

    $admin = new Admin();

    add_action('admin_menu', [$admin, 'add_plugin_admin_menu']);
    add_action('admin_enqueue_scripts', [$admin, 'enqueue_scripts']);
    add_action('admin_enqueue_scripts', [$admin, 'enqueue_styles']);

    // AJAX actions
    add_action('wp_ajax_guardar_historial_usuario', [$admin, 'ajax_guardar_historial_usuario']);
    add_action('wp_ajax_guardar_curso', [$admin, 'ajax_guardar_curso']);
    add_action('wp_ajax_eliminar_curso', [$admin, 'ajax_eliminar_curso']);
    add_action('wp_ajax_guardar_centro', [$admin, 'ajax_guardar_centro']);
    add_action('wp_ajax_eliminar_centro', [$admin, 'ajax_eliminar_centro']);
}
