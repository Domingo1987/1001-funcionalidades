<?php
// Shortcode [soluciones] - Muestra iframe con la solución en Replit si el número es válido
function soluciones_replit_shortcode() {
    if (!isset($_GET['num'])) {
        wp_redirect(home_url());
        exit;
    }

    $num_code = intval($_GET['num']);
    $num = ($num_code - 1987) / 1001;

    if (is_numeric($num) && intval($num) == $num && $num >= 1 && $num <= 1000) {
        $replit_url = 'https://replit.com/@DomingoPerez/problema-' . intval($num) . '?embed=true';
        return '<p style="text-align: center;"><iframe src="' . esc_url($replit_url) . '" width="900" height="600" frameborder="0" loading="lazy"></iframe></p>';
    } else {
        wp_redirect(home_url());
        exit;
    }
}
add_shortcode('soluciones', 'soluciones_replit_shortcode');


// Shortcode [solucion] - Muestra botón de solución si el usuario ha comentado el problema actual y al menos 4 problemas
function solucion_shortcode() {
    if (!is_singular('problema')) return ''; // Asegura que estamos en un CPT de tipo problema

    $num_problemas = get_num_problemas_comentados(); // ← ya deberías tener esta función
    $problema_resuelto = post_comentado_por_usuario(); // ← ya deberías tener esta función
    $num_prob = get_num_problema(); // ← ya deberías tener esta función personalizada
    $num_prob_code = $num_prob * 1001 + 1987;

    $url = ($num_problemas > 3 && $problema_resuelto)
        ? home_url('/soluciones/?num=' . $num_prob_code)
        : home_url('/denegado');

    return '<div style="text-align:center;margin:2rem 0;">
        <a href="' . esc_url($url) . '" class="button medium rounded grey">Ver Solución</a>
    </div>';
}
add_shortcode('solucion', 'solucion_shortcode');
