<?php
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode [cantidad_comentarios] - Cantidad total de comentarios realizados por usuarios registrados
function mostrar_cant_comentarios_en_respuestas() {
    $cantidad = cant_comentarios_de_usuarios();
    return $cantidad;
}
add_shortcode('cantidad_comentarios', 'mostrar_cant_comentarios_en_respuestas');

// Shortcode [cantidad_codigo] - Cantidad de comentarios con bloques de código
function mostrar_cant_codigo_en_respuestas() {
    $cantidad = cant_codigo_en_respuestas();
    return $cantidad;
}
add_shortcode('cantidad_codigo', 'mostrar_cant_codigo_en_respuestas');

// Alias directo para acceder a la función de cantidad de usuarios
add_shortcode('comentarios_de_usuarios_totales_sh', 'cant_comentarios_de_usuarios');

// Alias directo para acceder a la función de cantidad de códigos
add_shortcode('actualizar_cant_codigo_sh', 'cant_codigo_en_respuestas');
