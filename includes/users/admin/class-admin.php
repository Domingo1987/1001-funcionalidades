<?php

if (!defined('ABSPATH')) {
    exit; // Evita el acceso directo
}

class Admin {
    public function __construct() {
        // Inicialización
    }

    public function enqueue_styles($hook) {
        if (strpos($hook, 'users1001') !== false) {
            // Incluir Spectre en todo el admin
            wp_enqueue_style(
                'spectre-css',
                'https://unpkg.com/spectre.css/dist/spectre.min.css',
                [],
                '0.5.9'
            );
        }
    }

    public function enqueue_scripts($hook) {
        if (strpos($hook, 'users1001') !== false) {
            wp_enqueue_script(
                'users1001-admin',
                FUNC_URL . 'assets/js/admin.js',
                array('jquery'),
                '1.0.0',
                false
            );
    
            // Pasar variables al script
            wp_localize_script('users1001-admin', 'users1001_vars', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('users1001_nonce')
            ));

            
        }
    }


    public function add_plugin_admin_menu() {
        add_menu_page(
            'Users1001', 
            'Users1001', 
            'manage_options', 
            'users1001-dashboard', 
            array($this, 'display_plugin_dashboard_page'), 
            'dashicons-groups', 
            26
        );

        add_submenu_page(
            'users1001-dashboard',
            'Buscar estudiantes',
            'Buscar estudiantes',
            'manage_options',
            'users1001-buscar',
            [$this, 'display_busqueda_estudiantes_page']
        );
        

        

        // Nuevo submenu para "Gestión de Usuarios"
        add_submenu_page(
            'users1001-dashboard',
            'Gestión de Usuarios',
            'Gestión de Usuarios',
            'manage_options',
            'users1001-usuarios',
            array($this, 'display_usuarios_page')
        );
        
        
        add_submenu_page(
            'users1001-dashboard',
            'Gestión de Cursos',
            'Gestión de Cursos',
            'manage_options',
            'users1001-cursos',
            array($this, 'display_cursos_page')
        );
        
        add_submenu_page(
            'users1001-dashboard',
            'Gestión de Centros',
            'Gestión de Centros',
            'manage_options',
            'users1001-centros',
            array($this, 'display_centros_page')
        );
    }

    public function display_plugin_dashboard_page() {
        include_once FUNC_PATH . 'includes/users/admin/admin-dashboard.php';
    }

    // y luego el método
    public function display_busqueda_estudiantes_page() {
        include_once FUNC_PATH . 'includes/users/admin/admin-buscar-estudiantes.php';
    }
    
    // Nuevo método para mostrar la página de gestión de usuarios
    public function display_usuarios_page() {
        include_once FUNC_PATH . 'includes/users/admin/admin-usuarios.php';
    }    

    public function display_cursos_page() {
        include_once FUNC_PATH . 'includes/users/admin/admin-cursos.php';
    }

    public function display_centros_page() {
        include_once FUNC_PATH . 'includes/users/admin/admin-centros.php';
    }

    public function get_all_cursos($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $cursos = get_option('users1001_cursos', array());
        
        if (empty($cursos)) {
            // Valores por defecto
            $cursos = array(
                'Programación 1',
                'Programación 2',
                'Matemáticas',
                'Física',
                'Química',
                'Biología'
            );
            
            update_option('users1001_cursos', $cursos);
        }
        
        return $cursos;
    }

    public function get_all_centros($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $centros = get_option('users1001_centros', array());
        
        if (empty($centros)) {
            // Valores por defecto
            $centros = array(
                'CeRP del Suroeste',
                'CeRP del Litoral',
                'CeRP del Norte',
                'CeRP del Este',
                'CeRP del Centro',
                'IFD de Montevideo'
            );
            
            update_option('users1001_centros', $centros);
        }
        
        return $centros;
    }

    public function ajax_guardar_curso() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'users1001_nonce')) {
            wp_send_json_error('Acceso no autorizado');
        }
        
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No tiene permisos para realizar esta acción');
        }
        
        // Verificar datos
        if (!isset($_POST['curso']) || empty($_POST['curso'])) {
            wp_send_json_error('Nombre de curso requerido');
        }
        
        $curso = sanitize_text_field($_POST['curso']);
        $cursos = $this->get_all_cursos();
        
        // Verificar si ya existe
        if (in_array($curso, $cursos)) {
            wp_send_json_error('Este curso ya existe');
        }
        
        // Agregar nuevo curso
        $cursos[] = $curso;
        update_option('users1001_cursos', $cursos);
        
        wp_send_json_success(array(
            'message' => 'Curso agregado exitosamente',
            'cursos' => $cursos
        ));
    }

    public function ajax_eliminar_curso() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'users1001_nonce')) {
            wp_send_json_error('Acceso no autorizado');
        }
        
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No tiene permisos para realizar esta acción');
        }
        
        // Verificar datos
        if (!isset($_POST['curso']) || empty($_POST['curso'])) {
            wp_send_json_error('Nombre de curso requerido');
        }
        
        $curso = sanitize_text_field($_POST['curso']);
        $cursos = $this->get_all_cursos();
        
        // Verificar si existe
        $key = array_search($curso, $cursos);
        if ($key === false) {
            wp_send_json_error('Este curso no existe');
        }
        
        // Eliminar curso
        unset($cursos[$key]);
        $cursos = array_values($cursos); // Reindexar
        update_option('users1001_cursos', $cursos);
        
        wp_send_json_success(array(
            'message' => 'Curso eliminado exitosamente',
            'cursos' => $cursos
        ));
    }

    public function ajax_guardar_centro() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'users1001_nonce')) {
            wp_send_json_error('Acceso no autorizado');
        }
        
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No tiene permisos para realizar esta acción');
        }
        
        // Verificar datos
        if (!isset($_POST['centro']) || empty($_POST['centro'])) {
            wp_send_json_error('Nombre de centro requerido');
        }
        
        $centro = sanitize_text_field($_POST['centro']);
        $centros = $this->get_all_centros();
        
        // Verificar si ya existe
        if (in_array($centro, $centros)) {
            wp_send_json_error('Este centro ya existe');
        }
        
        // Agregar nuevo centro
        $centros[] = $centro;
        update_option('users1001_centros', $centros);
        
        wp_send_json_success(array(
            'message' => 'Centro agregado exitosamente',
            'centros' => $centros
        ));
    }

    public function ajax_eliminar_centro() {
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'users1001_nonce')) {
            wp_send_json_error('Acceso no autorizado');
        }
        
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            wp_send_json_error('No tiene permisos para realizar esta acción');
        }
        
        // Verificar datos
        if (!isset($_POST['centro']) || empty($_POST['centro'])) {
            wp_send_json_error('Nombre de centro requerido');
        }
        
        $centro = sanitize_text_field($_POST['centro']);
        $centros = $this->get_all_centros();
        
        // Verificar si existe
        $key = array_search($centro, $centros);
        if ($key === false) {
            wp_send_json_error('Este centro no existe');
        }
        
        // Eliminar centro
        unset($centros[$key]);
        $centros = array_values($centros); // Reindexar
        update_option('users1001_centros', $centros);
        
        wp_send_json_success(array(
            'message' => 'Centro eliminado exitosamente',
            'centros' => $centros
        ));
    }

    // Nueva función ajax para guardar el historial académico
    public function ajax_guardar_historial_usuario() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'asignar_historial')) {
            wp_send_json_error('Nonce inválido');
        }
    
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permiso denegado');
        }
    
        $usuarios = $_POST['usuarios'] ?? array();
        $curso = sanitize_text_field($_POST['curso'] ?? '');
        $centro = sanitize_text_field($_POST['centro'] ?? '');
        $anio = sanitize_text_field($_POST['anio'] ?? '');
    
        if (empty($usuarios) || empty($curso) || empty($centro) || empty($anio)) {
            wp_send_json_error('Datos incompletos');
        }
    
        foreach ($usuarios as $user_id) {
            $user_id = intval($user_id);
            $historial = get_user_meta($user_id, 'historico_academico', true);
            $historial = !empty($historial) ? json_decode($historial, true) : array();
    
            if (!isset($historial[$anio])) {
                $historial[$anio] = array();
            }
    
            // Prevenir duplicados exactos
            $ya_existe = false;
            foreach ($historial[$anio] as $entry) {
                if ($entry['curso'] === $curso && $entry['centro'] === $centro) {
                    $ya_existe = true;
                    break;
                }
            }
    
            if (!$ya_existe) {
                $historial[$anio][] = array(
                    'curso' => $curso,
                    'centro' => $centro
                );
                update_user_meta($user_id, 'historico_academico', json_encode($historial, JSON_UNESCAPED_UNICODE));
            }
        }
    
        wp_send_json_success('Historial actualizado correctamente.');
    }
    
}