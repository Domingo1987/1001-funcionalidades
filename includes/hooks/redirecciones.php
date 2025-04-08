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
