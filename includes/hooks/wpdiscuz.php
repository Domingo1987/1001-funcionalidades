<?php
// Habilitar wpDiscuz en el post type 'problema'
/*add_filter('wpdiscuz_supported_custom_post_types', function($post_types) {
    if (!in_array('problema', $post_types)) {
        $post_types[] = 'problema';
    }
    return $post_types;
});*/

add_action('comment_form_after_fields', 'agregar_lenguaje_oculto_1001');
add_action('comment_form_logged_in_after', 'agregar_lenguaje_oculto_1001');
function agregar_lenguaje_oculto_1001() {
    echo '<input type="hidden" name="lenguaje_usado" id="lenguaje_usado" value="python">';
}


