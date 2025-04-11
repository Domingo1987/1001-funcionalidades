<?php
// Shortcode: [listar_problemas categoria="capitulo-3"]

function shortcode_listar_problemas($atts = []) {
    $atts = shortcode_atts([
        'categoria' => ''
    ], $atts);

    ob_start();
    ?>

    <div data-theme="pico">
        <div id="contenedor-problemas" class="grid-problemas" data-categoria="<?= esc_attr($atts['categoria']) ?>"></div>

        <div id="cargando-problemas" style="text-align:center; margin:20px; display:none;">
            <span>Cargando más problemas...</span>
        </div>

        <section style="text-align: center; margin: 2rem 0;">
            <button id="ver-mas-problemas">Ver más</button>
        </section>
    </div>


    <?php
    return ob_get_clean();
}
add_shortcode('listar_problemas', 'shortcode_listar_problemas');



// Shortcode: [barra_problemas]

function shortcode_barra_problemas() {
    ob_start();
    ?>
    <div data-theme="pico">
        <section id="barra-problemas" class="barra-problemas" style="display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center; margin-bottom: 1rem;">
            <button data-capitulo="0" class="btn-azar btn-cap0 contrast" aria-label="Elegir un problema al azar de todo el libro" style="padding: 0.5rem 1rem;">🎲 Aleatorio</button>
            <?php
            for ($i = 1; $i <= 9; $i++) {
                echo '<button data-capitulo="' . $i . '" class="btn-azar btn-cap' . $i . ' contrast" aria-label="Elegir un problema al azar del capítulo ' . $i . '" style="padding: 0.5rem 1rem;">🎲 Capítulo ' . $i . '</button>';
            }
            ?>
        </section>

        <section style="text-align:center; margin-bottom:2rem;">
            <input type="search" id="filtro-problemas" placeholder="Buscar problema..." style="width: 100%; max-width: 400px;">
        </section>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('barra_problemas', 'shortcode_barra_problemas');


// Shortcode [problema_azar capitulo="X"]
function problema_azar_shortcode($atts) {
    global $wpdb;

    $atts = shortcode_atts(['capitulo' => ''], $atts);
    $capitulo = intval($atts['capitulo']);
    $capitulo = intval($atts['capitulo']);
    $term_id = $capitulo > 0 ? $capitulo + 53 : 0;
    $user_id = get_current_user_id();

    $where_taxonomy = '';
    $join_taxonomy = '';
    $where_comments = '';

    /*error_log('🔍 Shortcode [problema_azar] activado');
    error_log('👤 Usuario ID: ' . $user_id);
    error_log('📘 Capítulo recibido: ' . $capitulo);*/

    // Filtrar por categoría (si se pasa el atributo)
    if ($capitulo > 0) {
        $join_taxonomy = "INNER JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)
                          INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)";
        $where_taxonomy = $wpdb->prepare("AND tt.taxonomy = 'categorias_problemas' AND tt.term_id = %d", $term_id);
    }

    // Si el usuario está logueado, evitar los ya comentados
    if ($user_id > 0) {
        $where_comments = $wpdb->prepare(
            "AND NOT EXISTS (
                SELECT 1 FROM {$wpdb->comments} c
                WHERE c.comment_post_ID = p.ID AND c.user_id = %d AND c.comment_approved = 1
            )",
            $user_id
        );
    }

    // Query final
    $sql = "
        SELECT DISTINCT CAST(pm.meta_value AS UNSIGNED) AS num_problema
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id)
        $join_taxonomy
        WHERE p.post_type = 'problema'
          AND p.post_status = 'publish'
          AND pm.meta_key = 'num_problema'
          $where_taxonomy
          $where_comments
        ORDER BY RAND()
        LIMIT 1
    ";

    //error_log('🧠 SQL ejecutado: ' . $sql);

    $num_problema = $wpdb->get_var($sql);

    if ($num_problema) {
        //error_log('🎯 Redirigiendo a problema: ' . $num_problema);
        wp_redirect("https://pruebas.1001problemas.com/problema/problema-$num_problema");
        exit;
    } else {
        //error_log('😢 No se encontró ningún problema. Redirigiendo a /felicitaciones');
        wp_redirect('https://pruebas.1001problemas.com/felicitaciones');
        exit;
    }

    exit;
}
add_shortcode('problema_azar', 'problema_azar_shortcode');
