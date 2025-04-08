<?php
// Habilitar wpDiscuz en el post type 'problema'
add_filter('wpdiscuz_supported_custom_post_types', function($post_types) {
    if (!in_array('problema', $post_types)) {
        $post_types[] = 'problema';
    }
    return $post_types;
});
