<?php

function mostrar_modal_redireccion_login($retardo = 2000, $mensaje = '') {
    $img_url = FUNC_URL . 'assets/img/inicia_sesion.webp';

    ob_start(); ?>
    <dialog id="modal-construccion">
        <main data-theme="pico">
            <img src="<?php echo esc_url($img_url); ?>" alt="Inicia Sesión" />

            <?php if (!empty($mensaje)): ?>
                <p style="text-align: center; margin: 1rem auto; font-weight: bold;">
                    <?= esc_html($mensaje); ?>
                </p>
            <?php endif; ?>

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
                setTimeout(() => {
                    window.location.href = "https://pruebas.1001problemas.com/login/";
                }, <?php echo intval($retardo); ?>);
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
/*
function mostrar_modal_cookies() {
    // Si ya aceptó cookies, no mostrar el modal
    if (isset($_COOKIE['acepto_cookies'])) return;

    $img_url = FUNC_URL . 'assets/img/cookies.webp'; // Cambia si tienes otra imagen

    ob_start(); ?>
    <dialog id="modal-cookies">
        <main data-theme="pico">
            <img src="<?php echo esc_url($img_url); ?>" alt="Consentimiento de cookies" />
            <p style="margin-top: 1rem;">
                Usamos cookies para mejorar tu experiencia en el sitio. ¿Aceptas su uso?
            </p>
            <form method="dialog" class="text-center" style="display: flex; gap: 1rem; justify-content: center;">
                <button id="aceptar-cookies">Aceptar</button>
                <button class="secondary">Rechazar</button>
            </form>
        </main>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modal-cookies');
            if (modal && typeof modal.showModal === 'function') {
                modal.showModal();
            }

            document.getElementById('aceptar-cookies')?.addEventListener('click', () => {
                const fecha = new Date();
                fecha.setFullYear(fecha.getFullYear() + 1);
                document.cookie = "acepto_cookies=1; expires=" + fecha.toUTCString() + "; path=/";
            });
        });
    </script>
    <?php
    echo ob_get_clean();
}
*/

function mostrar_modal_cookies() {
    if (isset($_COOKIE['acepto_cookies'])) return;

    $img_url = FUNC_URL . 'assets/img/cookies.webp';

    ob_start(); ?>
    <dialog id="modal-cookies">
        <article style="max-width: 500px; text-align: center;" data-theme="pico">
            <img src="<?php echo esc_url($img_url); ?>" alt="Consentimiento de cookies" style="max-width: 150px; margin: auto;" />
            
            <h3 style="margin-top: 1rem;">Uso de Cookies</h3>

            <div class="texto-consentimiento">
                <small style="display: block; color: var(--pico-muted-color); margin-top: 0.5rem;">
                    Utilizamos cookies para asegurar la mejor experiencia. Analizamos el tráfico web y puedes aceptar o rechazar las cookies no necesarias.
                </small>
            </div>

            <footer style="display: flex; justify-content: center; gap: 1rem; margin-top: 1.5rem;">
                <button id="aceptar-cookies">Aceptar</button>
                <button class="secondary" id="rechazar-cookies">Rechazar</button>
            </footer>
        </article>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modal-cookies');
            if (modal && typeof modal.showModal === 'function') {
                modal.showModal();
            }

            const aceptarBtn = document.getElementById('aceptar-cookies');
            const rechazarBtn = document.getElementById('rechazar-cookies');

            function setCookie(name, value, days) {
                const d = new Date();
                d.setTime(d.getTime() + (days*24*60*60*1000));
                let expires = "expires=" + d.toUTCString();
                document.cookie = name + "=" + value + ";" + expires + ";path=/";
            }

            aceptarBtn?.addEventListener('click', () => {
                setCookie('acepto_cookies', '1', 180);
                modal.close();
            });

            rechazarBtn?.addEventListener('click', () => {
                setCookie('acepto_cookies', '0', 180);
                modal.close();
            });
        });
    </script>
    <?php
    echo ob_get_clean();
}
