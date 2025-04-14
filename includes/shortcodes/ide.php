<?php
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode [IDE_C] — Muestra un iframe con un IDE online para lenguaje C
function show_ide_c() {
    $url = 'https://www.programiz.com/c-programming/online-compiler/';
    return '<iframe src="' . $url . '" style="width:100%; height:500px;"></iframe>';
}
add_shortcode('IDE_C', 'show_ide_c');

// Shortcode [IDE_PYTHON] — Muestra un iframe con un IDE online para lenguaje Python
function show_ide_python() {
    $url = 'https://www.programiz.com/python-programming/online-compiler/';
    return '<iframe src="' . $url . '" style="width:100%; height:500px;"></iframe>';
}
add_shortcode('IDE_PYTHON', 'show_ide_python');
