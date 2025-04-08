<?php
/**
 * Plugin Name:       1001 Funcionalidades
 * Plugin URI:        https://1001problemas.com/
 * Description:       Conjunto de funcionalidades personalizadas para el sitio 1001problemas.com: shortcodes, estadísticas de usuario, scripts interactivos, mejoras visuales y más.
 * Version:           5.3.12
 * Requires at least: 5.5
 * Requires PHP:      7.4
 * Author:            Domingo Pérez
 * Author URI:        https://1001problemas.com/
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cienuno
 * Domain Path:       /languages
 */

 defined('ABSPATH') or die('No script kiddies please!');

 // Rutas
define('FUNC_PATH', plugin_dir_path(__FILE__));
define('FUNC_URL', plugin_dir_url(__FILE__));

// Estilos y scripts
function cargar_recursos_1001_funcionalidades() {
    // Propios 
    wp_enqueue_style('1001-estilos', FUNC_URL . 'assets/css/1001-estilos.css');
    wp_enqueue_script('1001-scripts', FUNC_URL . 'assets/js/1001-scripts.js', array('jquery'), null, true);

    // SweetAlert2 (nuevo)
    wp_enqueue_style('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');
    wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), null, true);

    if (is_page('listado-problemas')) {
        error_log('✅ PicoCSS cargado porque estamos en listar-problemas');
        wp_enqueue_style('pico', 'https://unpkg.com/@picocss/pico@latest/css/pico.min.css', [], null);
    } else {
        error_log('🛑 No estamos en listar-problemas');
    }
    
    // ✅ Pasar la URL de admin-ajax.php al JS
    wp_localize_script('1001-scripts', 'cienuno', [
        'ajaxurl' => admin_url('admin-ajax.php')
    ]);
}
add_action('wp_enqueue_scripts', 'cargar_recursos_1001_funcionalidades');

add_action('wp_enqueue_scripts', function () {
    if (is_page('dashboard')) {
        error_log('✅ PicoCSS cargado porque estamos en dashboard');
        wp_enqueue_style('pico', 'https://unpkg.com/@picocss/pico@latest/css/pico.min.css', [], null);
 
        wp_enqueue_style('dashboard-css', FUNC_URL . 'assets/css/dashboard.css');
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
        wp_enqueue_script('dashboard-js', FUNC_URL . 'assets/js/dashboard.js', ['jquery', 'chart-js'], null, true);
    }
});

// INCLUDES ORGANIZADOS

// 🔄 AJAX
require_once FUNC_PATH . 'includes/ajax/openai.php';
require_once FUNC_PATH . 'includes/ajax/problema-num.php';
require_once FUNC_PATH . 'includes/ajax/cargar-problemas.php';

// 🎣 HOOKS
require_once FUNC_PATH . 'includes/hooks/comentarios.php';
require_once FUNC_PATH . 'includes/hooks/interfaz.php';
require_once FUNC_PATH . 'includes/hooks/post-metadata.php';
require_once FUNC_PATH . 'includes/hooks/page-intros.php';
require_once FUNC_PATH . 'includes/hooks/language-selector.php';
require_once FUNC_PATH . 'includes/hooks/wpdiscuz.php';
require_once FUNC_PATH . 'includes/hooks/redirecciones.php';


// 🧩 SHORTCODES
require_once FUNC_PATH . 'includes/shortcodes/problemas.php';
require_once FUNC_PATH . 'includes/shortcodes/estadisticas.php';
require_once FUNC_PATH . 'includes/shortcodes/ide.php';
require_once FUNC_PATH . 'includes/shortcodes/soluciones.php';
require_once FUNC_PATH . 'includes/shortcodes/listar_problemas.php';
require_once FUNC_PATH . 'includes/shortcodes/barra_problemas.php';
require_once FUNC_PATH . 'includes/shortcodes/dashboard.php';



// 🧠 UTILS
require_once FUNC_PATH . 'includes/utils/problemas.php';
require_once FUNC_PATH . 'includes/utils/imagenes.php';

// 🔌 INTEGRACIONES
require_once FUNC_PATH . 'includes/integraciones/chatgpt.php';
require_once FUNC_PATH . 'includes/integraciones/seguridad.php';
