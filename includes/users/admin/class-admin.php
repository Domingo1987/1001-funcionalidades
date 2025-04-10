<?php
class Admin {
    public function __construct() {
        // Inicialización
    }

    public function enqueue_styles($hook) {
        if (strpos($hook, 'users1001') !== false) {
           /* wp_enqueue_style(
                'users1001-admin',
                FUNC_URL . 'assets/css/admin.css',
                array(),
                '1.0.0',
                'all'
            );*/

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

    public function display_cursos_page() {
        include_once FUNC_PATH . 'includes/users/admin/admin-cursos.php';
    }

    public function display_centros_page() {
        include_once FUNC_PATH . 'includes/users/admin/admin-centros.php';
    }

    public function add_curso_columns($columns) {
        $year = date('Y');
        
        // Agregar después de la columna Email
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'email') {
                $new_columns['curso_' . $year] = 'Curso ' . $year;
                $new_columns['centro_' . $year] = 'Centro ' . $year;
            }
        }
        
        return $new_columns;
    }

    public function display_curso_column_content($output, $column_name, $user_id) {
        $year = date('Y');
        
        if ($column_name === 'curso_' . $year) {
            $curso = get_user_meta($user_id, 'curso_' . $year, true);
            return $curso ? $curso : '—';
        }
        
        if ($column_name === 'centro_' . $year) {
            $centro = get_user_meta($user_id, 'centro_' . $year, true);
            return $centro ? $centro : '—';
        }
        
        return $output;
    }

    public function make_curso_columns_sortable($columns) {
        $year = date('Y');
        $columns['curso_' . $year] = 'curso_' . $year;
        $columns['centro_' . $year] = 'centro_' . $year;
        return $columns;
    }

    public function add_curso_filters() {
        $year = date('Y');
        $screen = get_current_screen();
        
        if ($screen->id != 'users') {
            return;
        }
        
        // Obtener todos los cursos y centros únicos
        $cursos = $this->get_all_cursos($year);
        $centros = $this->get_all_centros($year);
        
        // Filtro de cursos
        $current_curso = isset($_GET['curso_' . $year]) ? $_GET['curso_' . $year] : '';
        echo '<select name="curso_' . $year . '">';
        echo '<option value="">Todos los cursos</option>';
        
        foreach ($cursos as $curso) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($curso),
                selected($curso, $current_curso, false),
                esc_html($curso)
            );
        }
        echo '</select>';
        
        // Filtro de centros
        $current_centro = isset($_GET['centro_' . $year]) ? $_GET['centro_' . $year] : '';
        echo '<select name="centro_' . $year . '">';
        echo '<option value="">Todos los centros</option>';
        
        foreach ($centros as $centro) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($centro),
                selected($centro, $current_centro, false),
                esc_html($centro)
            );
        }
        echo '</select>';
    }

    public function filter_users_by_curso($query) {
        global $pagenow;
        $year = date('Y');
        
        if (is_admin() && $pagenow == 'users.php') {
            // Meta query para filtros múltiples
            $meta_query = array('relation' => 'AND');
            
            if (isset($_GET['curso_' . $year]) && !empty($_GET['curso_' . $year])) {
                $meta_query[] = array(
                    'key'     => 'curso_' . $year,
                    'value'   => $_GET['curso_' . $year],
                    'compare' => '='
                );
            }
            
            if (isset($_GET['centro_' . $year]) && !empty($_GET['centro_' . $year])) {
                $meta_query[] = array(
                    'key'     => 'centro_' . $year,
                    'value'   => $_GET['centro_' . $year],
                    'compare' => '='
                );
            }
            
            if (count($meta_query) > 1) {
                $query->set('meta_query', $meta_query);
            }
        }
    }

    public function add_curso_fields($user) {
        $year = date('Y');
        $historico = get_user_meta($user->ID, 'historico_academico', true);
        $historico = !empty($historico) ? json_decode($historico, true) : array();
        
        // Obtener curso y centro actuales
        $curso_actual = get_user_meta($user->ID, 'curso_' . $year, true);
        $centro_actual = get_user_meta($user->ID, 'centro_' . $year, true);
        
        // Obtener listas para dropdowns
        $cursos = $this->get_all_cursos($year);
        $centros = $this->get_all_centros($year);
        
        // Campo nonce para seguridad
        wp_nonce_field('save_curso_fields', 'users1001_nonce');
        ?>
        <h3>Información Académica <?php echo $year; ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="curso_<?php echo $year; ?>">Curso</label></th>
                <td>
                    <select name="curso_<?php echo $year; ?>" id="curso_<?php echo $year; ?>">
                        <option value="">Seleccionar curso</option>
                        <?php foreach ($cursos as $curso) : ?>
                            <option value="<?php echo esc_attr($curso); ?>" <?php selected($curso_actual, $curso); ?>><?php echo esc_html($curso); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="centro_<?php echo $year; ?>">Centro</label></th>
                <td>
                    <select name="centro_<?php echo $year; ?>" id="centro_<?php echo $year; ?>">
                        <option value="">Seleccionar centro</option>
                        <?php foreach ($centros as $centro) : ?>
                            <option value="<?php echo esc_attr($centro); ?>" <?php selected($centro_actual, $centro); ?>><?php echo esc_html($centro); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        
        <h3>Historial Académico</h3>
        <div id="historial_academico">
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Año</th>
                        <th>Curso</th>
                        <th>Centro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($historico)) : ?>
                        <?php foreach ($historico as $anio => $datos) : ?>
                            <?php if ($anio != $year) : // No mostrar el año actual ?>
                                <tr>
                                    <td><?php echo esc_html($anio); ?></td>
                                    <td>
                                        <select name="historico[<?php echo $anio; ?>][curso]">
                                            <option value="">Seleccionar curso</option>
                                            <?php foreach ($cursos as $curso) : ?>
                                                <option value="<?php echo esc_attr($curso); ?>" <?php selected($datos['curso'], $curso); ?>><?php echo esc_html($curso); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="historico[<?php echo $anio; ?>][centro]">
                                            <option value="">Seleccionar centro</option>
                                            <?php foreach ($centros as $centro) : ?>
                                                <option value="<?php echo esc_attr($centro); ?>" <?php selected($datos['centro'], $centro); ?>><?php echo esc_html($centro); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr id="no-history-row">
                            <td colspan="3">No hay historial académico registrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <p>
            <button type="button" id="agregar_anio" class="button">Agregar año académico</button>
        </p>
        
        <script>
        jQuery(document).ready(function($) {
            $('#agregar_anio').on('click', function() {
                var anio = prompt('Ingrese el año:');
                if (anio && !isNaN(anio) && anio != <?php echo $year; ?>) {
                    // Eliminar mensaje de "no hay historial"
                    $('#no-history-row').remove();
                    
                    // Obtener los cursos y centros
                    var cursos = <?php echo json_encode($cursos); ?>;
                    var centros = <?php echo json_encode($centros); ?>;
                    
                    // Crear selectores
                    var cursoSelect = '<select name="historico[' + anio + '][curso]">';
                    cursoSelect += '<option value="">Seleccionar curso</option>';
                    $.each(cursos, function(index, curso) {
                        cursoSelect += '<option value="' + curso + '">' + curso + '</option>';
                    });
                    cursoSelect += '</select>';
                    
                    var centroSelect = '<select name="historico[' + anio + '][centro]">';
                    centroSelect += '<option value="">Seleccionar centro</option>';
                    $.each(centros, function(index, centro) {
                        centroSelect += '<option value="' + centro + '">' + centro + '</option>';
                    });
                    centroSelect += '</select>';
                    
                    // Añadir fila
                    var html = '<tr>' +
                        '<td>' + anio + '</td>' +
                        '<td>' + cursoSelect + '</td>' +
                        '<td>' + centroSelect + '</td>' +
                        '</tr>';
                    $('#historial_academico tbody').append(html);
                }
            });
        });
        </script>
        <?php
    }

    public function save_curso_fields($user_id) {
        if (!current_user_can('edit_user', $user_id) || !isset($_POST['users1001_nonce']) || 
            !wp_verify_nonce($_POST['users1001_nonce'], 'save_curso_fields')) {
            return;
        }
        
        $year = date('Y');
        
        // Guardar año actual
        if (isset($_POST['curso_' . $year])) {
            update_user_meta($user_id, 'curso_' . $year, sanitize_text_field($_POST['curso_' . $year]));
        }
        
        if (isset($_POST['centro_' . $year])) {
            update_user_meta($user_id, 'centro_' . $year, sanitize_text_field($_POST['centro_' . $year]));
        }
        
        // Actualizar historial
        $historico = array();
        
        // Añadir año actual al historial
        $historico[$year] = array(
            'curso' => sanitize_text_field($_POST['curso_' . $year]),
            'centro' => sanitize_text_field($_POST['centro_' . $year])
        );
        
        // Añadir años anteriores desde el formulario
        if (isset($_POST['historico']) && is_array($_POST['historico'])) {
            foreach ($_POST['historico'] as $anio => $datos) {
                $historico[$anio] = array(
                    'curso' => sanitize_text_field($datos['curso']),
                    'centro' => sanitize_text_field($datos['centro'])
                );
            }
        }
        
        // Guardar en formato JSON
        update_user_meta($user_id, 'historico_academico', json_encode($historico));
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

    public function ajax_save_curso() {
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

    public function ajax_delete_curso() {
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

    public function ajax_save_centro() {
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

    public function ajax_delete_centro() {
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
}