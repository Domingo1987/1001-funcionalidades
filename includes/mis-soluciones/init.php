<?php

// Shortcodes relacionados
require_once __DIR__ . '/shortcode.php';
// Funciones relacionadas
require_once __DIR__ . '/functions.php';

// Solo ejecutar si estamos en la página correcta
add_action('wp_enqueue_scripts', function () {
    if (is_page('mis-soluciones')) {
        /*wp_enqueue_style(
            'listado-problemas-css',
            FUNC_URL . 'assets/css/listado-problemas.css',
            [],
            '1.0'
        );

        wp_enqueue_script(
            'listado-problemas-js',
            FUNC_URL . 'assets/js/listado-problemas.js',
            ['jquery'],
            '1.0',
            true
        );*/
    }
});
