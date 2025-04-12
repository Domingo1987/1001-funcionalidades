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

function mis_soluciones_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>Debes estar logueado para ver tus soluciones.</p>';
    }

    $problemas = get_user_problems_with_categories();
    $nodos = generar_nodos_problemas($problemas);
    $enlaces = generar_links_por_categoria($problemas);

    wp_enqueue_script(
        'mis-soluciones-js',
        FUNC_URL . 'assets/js/mis-soluciones.js',
        [],
        '1.0',
        true
    );

    wp_localize_script('mis-soluciones-js', 'misSolucionesData', [
        'nodos' => $nodos,
        'enlaces' => $enlaces,
    ]);

    ob_start();
    ?>
    <div>
        <p>Problemas comentados y sus categorías:</p>
        <ul>
            <?php foreach ($problemas as $p): ?>
                <li>Problema: <?php echo $p['problem_number']; ?> - Categoría: <?php echo $p['category']; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div id="graph"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('mis_soluciones', 'mis_soluciones_shortcode');
