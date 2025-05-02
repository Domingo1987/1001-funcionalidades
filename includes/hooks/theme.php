<?php
// includes/hooks/theme.php

// Forzar el tema claro (light) en toda la web ignorando el modo oscuro del navegador
add_filter('language_attributes', function($output) {
    // Verifica si ya hay un data-theme (para evitar duplicación)
    if (strpos($output, 'data-theme=') === false) {
        $output .= ' data-theme="light"';
    }
    return $output;
});
