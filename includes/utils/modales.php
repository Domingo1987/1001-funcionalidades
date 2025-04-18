<?php
if (!defined('ABSPATH')) {
    exit;
}


function mostrar_modal_redireccion_login($retardo, $mensaje = '') {
    $img_url = FUNC_URL . 'assets/img/inicia_sesion.webp';

    ob_start(); ?>
    <dialog id="modal-construccion">
        <main data-theme="pico" style="position: relative; padding: 0;">
            <div style="position: relative;">
                <img src="<?php echo esc_url($img_url); ?>" alt="Inicia Sesión" style="width: 100%; border-radius: 0;" />

                <?php if (!empty($mensaje)): ?>
                    <div style="
                        position: absolute;
                        bottom: 0;
                        width: 100%;
                        background: rgba(0, 0, 0, 0.6);
                        color: white;
                        text-align: center;
                        padding: 0.75rem 1rem;
                        font-weight: bold;
                        font-size: 1rem;
                    ">
                        <?= esc_html($mensaje); ?>
                    </div>
                <?php endif; ?>
            </div>

            <form method="dialog" class="text-center" style="margin-top: 1rem;">
                <button onclick="window.location.href='/login/'" class="primary">Iniciar Sesión</button>
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
                    window.location.href = "https://1001problemas.com/login/";
                }, <?php echo intval($retardo); ?>);
            }
        });
    </script>
    <?php
    return ob_get_clean();
}

function mostrar_modal_en_construccion() {
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

                modal.addEventListener('close', () => {
                window.location.href = "/";
            });
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
