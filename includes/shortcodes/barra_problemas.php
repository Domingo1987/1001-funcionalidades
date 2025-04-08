<?php
// Shortcode: [barra_problemas]

function shortcode_barra_problemas() {
    ob_start();
    ?>
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
    <?php
    return ob_get_clean();
}
add_shortcode('barra_problemas', 'shortcode_barra_problemas');



/*
function shortcode_barra_problemas() {
    ob_start();
    ?>
    <section id="barra-problemas" class="barra-problemas" style="display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center; margin-bottom: 1rem;">
        <button data-capitulo="0" class="btn-azar btn-cap0 contrast" aria-label="Elegir un problema al azar de todo el libro">🎲 Aleatorio</button>
        <?php
        $nombres = ['Capítulo 1','Capítulo 2','Capítulo 3','Capítulo 4','Capítulo 5','Capítulo 6','Capítulo 7','Capítulo 8','Capítulo 9'];
        for ($i = 1; $i <= 9; $i++) {
            echo '<button data-capitulo="' . $i . '" class="btn-azar btn-cap' . $i . ' contrast" aria-label="Elegir un problema al azar del ' . $nombres[$i - 1] . '">🎲 ' . $nombres[$i - 1] . '</button>';
        }
        ?>
    </section>

    <section style="text-align:center; margin-bottom:2rem;">
        <input type="search" id="filtro-problemas" placeholder="Buscar problema..." style="width: 100%; max-width: 400px;">
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('barra_problemas', 'shortcode_barra_problemas');*/
