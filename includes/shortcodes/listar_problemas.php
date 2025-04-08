<?php
// Shortcode: [listar_problemas categoria="capitulo-3"]

function shortcode_listar_problemas($atts = []) {
    $atts = shortcode_atts([
        'categoria' => ''
    ], $atts);

    ob_start();
    ?>

    <div id="contenedor-problemas" class="grid-problemas" data-categoria="<?= esc_attr($atts['categoria']) ?>"></div>

    <div id="cargando-problemas" style="text-align:center; margin:20px; display:none;">
        <span>Cargando más problemas...</span>
    </div>

    <section style="text-align: center; margin: 2rem 0;">
        <button id="ver-mas-problemas">Ver más</button>
    </section>


    <?php
    return ob_get_clean();
}
add_shortcode('listar_problemas', 'shortcode_listar_problemas');

