<?php
if (!defined('ABSPATH')) {
    exit;
}

// 🧠 Forzar que el plugin SyntaxHighlighter registre el shortcode [code] para el CPT 'problema'
add_action('init', function () {
    global $syntaxhighlighter;

    if (
        !shortcode_exists('code') &&
        class_exists('SyntaxHighlighter') &&
        method_exists('SyntaxHighlighter', 'setup')
    ) {
        $syntaxhighlighter = new SyntaxHighlighter();
        $syntaxhighlighter->setup(); // Forzar que registre el shortcode [code]
    }
});

// 🎯 Encolar scripts y estilos solo en posts individuales
function language_selector_scripts() {
    if (!is_single() || get_post_type() !== 'problema') return;

    wp_enqueue_style('language-selector-styles', FUNC_URL . 'assets/css/language-selector.css');
    wp_enqueue_script('language-selector-script', FUNC_URL . 'assets/js/language-selector.js', array('jquery'), '1.0', true);

    $post_id = get_the_ID();
    $python_version = get_post_meta($post_id, 'problem_content_python', true);
    $java_version   = get_post_meta($post_id, 'problem_content_java', true);

    // Debug
    error_log('PYTHON: ' . print_r($python_version, true));
    error_log('PYTHON PROCESADO: ' . print_r(do_shortcode($python_version), true));
}
add_action('wp_enqueue_scripts', 'language_selector_scripts');

// 🧩 Insertar selector automáticamente al comienzo del contenido
function language_selector_process_content($content) {
    if (!is_single() || get_post_type() !== 'problema') return $content;
    if (strpos($content, 'showContent') !== false) return $content;

    $post_id = get_the_ID();
    $python_version = get_post_meta($post_id, 'problem_content_python', true);
    $java_version   = get_post_meta($post_id, 'problem_content_java', true);

    if (empty($python_version) && empty($java_version)) {
        return $content;
    }

    // 🔘 Generar HTML del selector
    $selector_html  = '<div id="ls-language-selector" class="ls-selector-wrapper">';
    $selector_html .= '<div class="ls-buttons">';
    $selector_html .= '<button class="ls-button active" data-lang="c">C</button>';
    if (!empty($python_version)) $selector_html .= '<button class="ls-button" data-lang="python">Python</button>';
    if (!empty($java_version))   $selector_html .= '<button class="ls-button" data-lang="java">Java</button>';
    $selector_html .= '</div></div>';

    // 📦 Contenido original y alternativos
    $output  = $selector_html;
    $output .= '<div class="problem-content-original">' . $content . '</div>';

    if (!empty($python_version)) {
        $output .= '<div class="problem-content-python" style="display:none;">';
        $output .= apply_filters('the_content', html_entity_decode($python_version, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $output .= '</div>';
    }

    if (!empty($java_version)) {
        $output .= '<div class="problem-content-java" style="display:none;">';
        $output .= apply_filters('the_content', html_entity_decode($java_version, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $output .= '</div>';
    }


    return $output;
}
add_filter('the_content', 'language_selector_process_content', 20);
