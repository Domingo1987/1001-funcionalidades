<?php
// Archivo: hooks/interfaz.php
/*
// Shortcode [redireccionar] — Redirige al inicio si el usuario no está logueado
function redireccionar_si_no_logueado() {
    if (!is_user_logged_in()) {
        $img_url = FUNC_URL . 'assets/img/inicia_sesion.webp';

        ob_start(); ?>
        
        <dialog id="modal-construccion">
            <main data-theme="pico">
                <img src="<?php echo esc_url($img_url); ?>" alt="Inicia sesión para acceder" />
                <div class="text-center" style="margin-top: -1rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                    <a href="https://pruebas.1001problemas.com/login/" class="contrast" style="background-color: #3993d5; color: white; padding: 0.6rem 1rem; border-radius: 8px; text-decoration: none;">
                        🔐 Iniciar Sesión
                    </a>
                    <form method="dialog">
                        <button class="secondary">Cerrar</button>
                    </form>
                </div>
            </main>
        </dialog>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('modal-construccion');
                if (modal && typeof modal.showModal === 'function') {
                    modal.showModal();
                    setTimeout(() => {
                        window.location.href = "https://pruebas.1001problemas.com/listado-problemas/";
                    }, 2000);
                }
            });
        </script>

        <?php
        return ob_get_clean();
    }

    return ''; // Si está logueado, no hace nada
}
add_shortcode('redireccionar', 'redireccionar_si_no_logueado');*/


add_action('template_redirect', 'verificar_acceso_paginas_privadas');

function verificar_acceso_paginas_privadas() {
    $paginas_protegidas = ['usuarios', 'dashboard', 'profile', 'estadisticas', 'mis-soluciones', 'listado-problemas', 'tools', 'construccion'];


    if (
        !is_user_logged_in() &&
        is_page($paginas_protegidas)
    ) 
    {

        // Mostrar modal y redirigir en 2 segundos
        add_action('wp_footer', function () {
            $img_url = FUNC_URL . 'assets/img/inicia_sesion.webp';
            ?>
            <dialog id="modal-construccion">
                <main data-theme="pico">
                    <img src="<?php echo esc_url($img_url); ?>" alt="Inicia Sesión" />
                    <form method="dialog" class="text-center">
                        <button onclick="window.location.href='https://pruebas.1001problemas.com/login/'">Iniciar Sesión</button>
                        <button class="secondary">Cerrar</button>
                    </form>
                </main>
            </dialog>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const modal = document.getElementById('modal-construccion');
                    if (modal && typeof modal.showModal === 'function') {
                        modal.showModal();
                        setTimeout(() => {
                            window.location.href = "https://pruebas.1001problemas.com/";
                        }, 2000);
                    }
                });
            </script>
            <?php
        });
    }
}




// 🔁 Hook: mostrar cantidad de códigos en el footer con Pico
add_action('wp_footer', 'codigos_footer');
function codigos_footer() {
    ?>
        <article class="footer-card-pico">
            <h2 class="footer-numero"><?php echo do_shortcode('[cantidad_codigo]'); ?></h2>
            <p class="footer-descripcion">CÓDIGOS</p>
        </article>
    <?php
}

// 🔁 Hook: mostrar cantidad de comentarios en el footer con Pico
add_action('wp_footer', 'comentarios_footer');
function comentarios_footer() {
    ?>
    <article data-theme="pico" class="footer-card-pico">
        <h2 class="footer-numero"><?php echo do_shortcode('[cantidad_comentarios]'); ?></h2>
        <p class="footer-descripcion">SOLUCIONES</p>
    </article>
    <?php
}

// 🎛️ Ocultar menús del admin para usuarios con rol 'creador_de_ia'
add_action('admin_menu', 'hide_menu_items_for_ia_creator', 99);
function hide_menu_items_for_ia_creator() {
    $user = wp_get_current_user();
    if (in_array('creador_de_ia', $user->roles)) {
        remove_menu_page('index.php');
        remove_menu_page('edit.php');
        remove_menu_page('edit.php?post_type=portfolio_item');
        remove_menu_page('upload.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('tools.php');
    }
}

// 🔗 Ocultar accesos de barra superior para 'creador_de_ia'
add_action('admin_bar_menu', 'remove_admin_bar_links_for_ia_creator', 999);
function remove_admin_bar_links_for_ia_creator($wp_admin_bar) {
    $user = wp_get_current_user();
    if (in_array('creador_de_ia', $user->roles)) {
        $wp_admin_bar->remove_node('new-content');
        $wp_admin_bar->remove_node('comments');
    }
}

// 🧑‍💻 Mostrar columna "Autor" en listado de entradas
add_filter('manage_posts_columns', 'add_author_column');
function add_author_column($columns) {
    $first = array_slice($columns, 0, 1, true);
    $rest = array_slice($columns, 1, null, true);
    return array_merge($first, ['author' => 'Autor'], $rest);
}

add_action('manage_posts_custom_column', 'show_author_column', 10, 2);
function show_author_column($column_name, $post_id) {
    if ($column_name == 'author') {
        echo get_the_author_meta('display_name', get_post_field('post_author', $post_id));
    }
}

add_filter('dynamic_sidebar_params', 'ajustar_clases_footer_top');
function ajustar_clases_footer_top($params) {
    // Detectamos si estamos dentro del footer-top
    if (is_admin() || !is_active_sidebar('Footer_1st_box') && !is_active_sidebar('Footer_2nd_box') && !is_active_sidebar('Footer_3rd_box') && !is_active_sidebar('Footer_4th_box')) {
        return $params;
    }

    $footer_boxes = [
        'Footer_1st_box',
        'Footer_2nd_box',
        'Footer_3rd_box',
        'Footer_4th_box'
    ];

    // Contamos cuántos sidebars están activos
    $activos = array_filter($footer_boxes, function($box) {
        return is_active_sidebar($box);
    });

    // Si hay exactamente dos sidebars activos, los hacemos "one-half"
    if (count($activos) === 2) {
        static $contador = 0;
        if (in_array($params[0]['id'], $activos)) {
            $params[0]['before_widget'] = str_replace('one-fourth', 'one-half', $params[0]['before_widget']);
            $contador++;
            if ($contador === 2) {
                $params[0]['before_widget'] = str_replace('one-half', 'one-half last', $params[0]['before_widget']);
            }
        }
    }

    return $params;
}

