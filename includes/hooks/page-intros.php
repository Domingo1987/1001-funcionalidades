<?php
// Archivo: hooks/page-intros.php

function insertar_bienvenida_si_es_centrada($content) {
    if (is_page() && get_post_meta(get_the_ID(), BRANKIC_VAR_PREFIX . 'centered_title', true)) {
        $html_intro = '
        <div class="section-title text-align-center">
            <h1 class="title">BIENVENIDOS AL LIBRO "1001 PROBLEMAS Y TEMAS DE PROGRAMACIÓN EN C"</h1>
            <h3 class="subtitle">por DOMINGO PÉREZ Y MARÍA BLANCA VIERA</h3>
            <p>Bienvenidos a <strong>1001 Problemas</strong>, la plataforma donde el desafío y el aprendizaje se unen en perfecta armonía. Les presentamos una exclusiva colección de 1001 problemas de programación, extraídos de nuestro libro, diseñado especialmente para estudiantes que estén aprendiendo el lenguaje C. Aunque, si C no es tu lenguaje de programación predilecto, no hay problema. Las habilidades de resolución y pensamiento lógico que podrás adquirir son transferibles a cualquier lenguaje. <a href="https://pruebas.1001problemas.com/problemas/" style="text-transform: uppercase; font-weight: bold;">¡ACEPTA EL DESAFÍO AQUÍ!</a></p>

<p>Pero 1001 Problemas es más que un conjunto de ejercicios. Esta plataforma digital ofrece la oportunidad de interactuar, discutir ideas y colaborar en soluciones con otros entusiastas de la programación. Aquí, no sólo estás resolviendo problemas, estás aprendiendo a trabajar en equipo, a colaborar, a compartir ideas y a crecer como profesional en la programación. Recuerda, el verdadero aprendizaje no se trata solo de obtener respuestas, sino de aprender a hacer las preguntas correctas.</p>
<p>Así que, ya sea que decidas explorar los problemas de forma secuencial, al azar o por capítulos, cada solución que aportes será un hito en tu camino de aprendizaje. Siempre hay un nuevo problema a la espera, siempre hay algo nuevo que aprender en 1001 Problemas.</p> <p>¡Te invitamos a sumarte a esta emocionante aventura!</p>
        </div>';
        return $html_intro . $content;
    }
    return $content;
}
add_filter('the_content', 'insertar_bienvenida_si_es_centrada');
