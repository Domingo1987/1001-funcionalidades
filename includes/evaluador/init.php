<?php
// Shortcode del dashboard
require_once __DIR__ . '/shortcode.php';
// Funciones auxiliares
require_once __DIR__ . '/functions.php';
// Funciones de ajax
require_once __DIR__ . '/ajax-v2.php';
// Funcooones de OPENAI
require_once __DIR__ . '/openai.php';
// Funciones de ajax de openai
//require_once __DIR__ . '/ajax-openai.php';

// Ajax si lo usás
// require_once __DIR__ . '/ajax.php';

// Enqueue estilos y scripts solo en la página del dashboard
add_action('wp_enqueue_scripts', function () {
    if (is_page('evaluador-de-problemas')) {
        wp_enqueue_style(
            'evaluador-css',
            FUNC_URL . 'assets/css/evaluador.css',
            [],
            '1.1'
        );
        wp_enqueue_script(
            'evaluador-js',
            FUNC_URL . 'assets/js/evaluador.js',
            [],
            '2.0',
            true
        );
    }
});
