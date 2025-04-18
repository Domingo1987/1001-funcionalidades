<?php
if (!defined('ABSPATH')) {
    exit;
}
// Solo en admin: cargar clase y hooks del administrador de usuarios
if (is_admin()) {
    require_once __DIR__ . '/admin/class-admin.php';

    // 🔄 Instancia global del admin
    global $users1001_admin;
    $users1001_admin = new Admin();

    //$admin = new Admin();

    add_action('admin_menu', [$$users1001_admin, 'add_plugin_admin_menu']);
    add_action('admin_enqueue_scripts', [$users1001_admin, 'enqueue_scripts']);
    add_action('admin_enqueue_scripts', [$users1001_admin, 'enqueue_styles']);

    // AJAX actions
    add_action('wp_ajax_guardar_historial_usuario', [$users1001_admin, 'ajax_guardar_historial_usuario']);
    add_action('wp_ajax_guardar_curso', [$users1001_admin, 'ajax_guardar_curso']);
    add_action('wp_ajax_eliminar_curso', [$users1001_admin, 'ajax_eliminar_curso']);
    add_action('wp_ajax_guardar_centro', [$users1001_admin, 'ajax_guardar_centro']);
    add_action('wp_ajax_eliminar_centro', [$users1001_adminn, 'ajax_eliminar_centro']);
}
