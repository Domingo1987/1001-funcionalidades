<?php
if (!defined('ABSPATH')) {
    exit;
}

function shortcode_en_construccion_modal() {
    return mostrar_modal_en_construccion();
}
add_shortcode('en_construccion', 'shortcode_en_construccion_modal');


