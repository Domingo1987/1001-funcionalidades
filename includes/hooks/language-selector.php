<?php
if (!defined('ABSPATH')) {
    exit;
}

// 🎯 Encolar scripts y estilos solo en posts individuales
function language_selector_scripts() {
    if (!is_single() || get_post_type() !== 'problema') return;

    wp_enqueue_style('language-selector-styles', FUNC_URL . 'assets/css/language-selector.css');
    wp_enqueue_script('language-selector-script', FUNC_URL . 'assets/js/language-selector.js', array('jquery'), '1.0', true);

    $post_id = get_the_ID();
    $python_version = get_post_meta($post_id, 'problem_content_python', true);
    $java_version   = get_post_meta($post_id, 'problem_content_java', true);

    /*wp_localize_script('language-selector-script', 'lsData', array(
        'postId'        => $post_id,
        'pythonVersion' => wpautop($python_version),
        'javaVersion'   => wpautop($java_version)
    ));*/
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
        $output .= '<h3 class="title">' . $python_version . '</h3>';
        $output .= '</div>';
    }

    if (!empty($java_version)) {
        $output .= '<div class="problem-content-java" style="display:none;">';
        $output .= '<h3 class="title">' . $java_version . '</h3>';
        $output .= '</div>';
    }

    return $output;
}
add_filter('the_content', 'language_selector_process_content', 20);
