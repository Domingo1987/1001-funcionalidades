<?php
// Archivo: includes/utils/imagenes.php

// Asegurar que FUNC_URL está definido
if (!defined('FUNC_URL')) {
    define('FUNC_URL', plugin_dir_url(__FILE__));
}

/**
 * Devuelve la URL de imagen basada en la categoría del post (usando taxonomía personalizada).
 * @param int $post_id
 * @param bool $comentado
 * @return string URL de imagen
 */
function get_imagen_categoria($post_id, $comentado = false) {
    $terms = get_the_terms($post_id, 'categorias_problemas');

    if (!empty($terms) && !is_wp_error($terms)) {
        $ids = array_map(fn($t) => $t->term_taxonomy_id, $terms);
        error_log('🔢 Term Taxonomy IDs para post ' . $post_id . ': ' . implode(', ', $ids));

        foreach ($terms as $term) {
            $tax_id = $term->term_taxonomy_id;
            if ($tax_id >= 54 && $tax_id <= 62) {
                $num_imagen = $tax_id - 53; // Cap1 empieza en 1
                $nombre = 'cap' . $num_imagen . '-v5';
                if ($comentado) $nombre .= '-gris';
                return FUNC_URL . 'assets/img/' . $nombre . '.png';
            }
        }
    }

    return FUNC_URL . 'assets/img/default.png';
}
