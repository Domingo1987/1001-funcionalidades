<?php
function shortcode_en_construccion_modal() {
    $img_url = plugins_url('assets/img/en_construccion.webp', dirname(__DIR__, 2));

    ob_start(); ?>
    
    <dialog id="modal-construccion">
        <img src="<?php echo esc_url($img_url); ?>" alt="En construcción">
        <form method="dialog">
            <button>Cerrar</button>
        </form>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modal-construccion');
            if (modal && typeof modal.showModal === 'function') {
                modal.showModal();
            }
        });
    </script>

    <?php
    return ob_get_clean();
}

add_shortcode('en_construccion', 'shortcode_en_construccion_modal');

