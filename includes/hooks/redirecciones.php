<?php
// Redirección automática si se accede a /?problema_azar=1
add_action('template_redirect', function() {
    if (isset($_GET['problema_azar'])) {
        $capitulo = isset($_GET['capitulo']) ? intval($_GET['capitulo']) : null;
        $shortcode = '[problema_azar' . ($capitulo ? ' capitulo="' . $capitulo . '"' : '') . ']';

        error_log('🌀 Ejecutando redirección desde template_redirect');
        error_log('➡️ Shortcode generado: ' . $shortcode);

        $output = do_shortcode($shortcode);

        error_log('📤 Resultado del shortcode: ' . var_export($output, true));
        // NOTA: el shortcode no devuelve contenido, hace wp_redirect() y exit.

        exit;
    }
});


add_action('template_redirect', 'verificar_acceso_paginas_privadas');
function verificar_acceso_paginas_privadas() {
    $paginas_protegidas = ['usuarios', 'dashboard', 'profile', 'estadisticas', 'mis-soluciones'];

    if (!is_user_logged_in() && is_page($paginas_protegidas)) {
        add_action('wp_footer', function () {
            echo mostrar_modal_redireccion_login(2000);
        });
    }
}

