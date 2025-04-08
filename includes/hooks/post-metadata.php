<?php
// Archivo: hooks/post-metadata.php

function mostrar_capitulos_o_categoria_ia() {
    $output = '';

    $categories = get_the_category();
    if (!empty($categories)) {
        foreach ($categories as $category) {
            if ($category->name != 'Blog') {
                $output .= '<a href="https://pruebas.1001problemas.com/problemas/' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</a>, ';
            }
        }
    } else {
        $ia_categories = get_the_terms(get_the_ID(), 'ia_categoria');
        if (!empty($ia_categories)) {
            foreach ($ia_categories as $ia_category) {
                if ($ia_category->name != 'Inteligencia Artificial') {
                    $output .= '<a href="https://pruebas.1001problemas.com/ia/' . esc_attr($ia_category->slug) . '">' . esc_html($ia_category->name) . '</a>, ';
                }
            }
        }
    }

    return rtrim($output, ', ');
}
