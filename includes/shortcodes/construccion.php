<?php
function shortcode_en_construccion_modal() {
    $img_url = FUNC_URL . 'assets/img/en_construccion.webp';

    ob_start(); ?>
    
    <dialog id="modal-construccion">
        <main data-theme="pico">
            <img src="<?php echo esc_url($img_url); ?>" alt="En construcción" />
            <form method="dialog" class="text-center">
                <button class="secondary">Cerrar</button>
            </form>
        </main>
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


