<?php
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();

// Simulación de funciones
$likes_dados = get_likes_dados($user_id);
$likes_recibidos = get_likes_recibidos($user_id);
$comentarios_hechos = get_comentarios_hechos($user_id);
$comentarios_recibidos = get_comentarios_recibidos($user_id);
?>

    <section style="margin-top: 1rem;">
        <h4>👍 Likes</h4>
        <div class="resumen-general-cards" style="display: flex; gap: 1rem;">
            <article class="card-1001" style="--color: #00bcd4;">
                <div class="barra-color"></div>
                <div class="contenido-card">
                    <div class="icono">❤️</div>
                    <div class="texto">
                        <p>Dado</p>
                        <strong><span class="contador-animado" data-valor="<?php echo $likes_dados; ?>">0</span></strong>
                    </div>
                </div>
            </article>

            <article class="card-1001" style="--color: #e91e63;">
                <div class="barra-color"></div>
                <div class="contenido-card">
                    <div class="icono">🎯</div>
                    <div class="texto">
                        <p>Recibido</p>
                        <strong><span class="contador-animado" data-valor="<?php echo $likes_recibidos; ?>">0</span></strong>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section style="margin-top: 2rem;">
        <h4>🗨️ Comentarios</h4>
        <div class="resumen-general-cards" style="display: flex; gap: 1rem;">
            <article class="card-1001" style="--color: #03a9f4;">
                <div class="barra-color"></div>
                <div class="contenido-card">
                    <div class="icono">💬</div>
                    <div class="texto">
                        <p>Hechos</p>
                        <strong><span class="contador-animado" data-valor="<?php echo $comentarios_hechos; ?>">0</span></strong>
                    </div>
                </div>
            </article>

            <article class="card-1001" style="--color: #8bc34a;">
                <div class="barra-color"></div>
                <div class="contenido-card">
                    <div class="icono">📨</div>
                    <div class="texto">
                        <p>Recibidos</p>
                        <strong><span class="contador-animado" data-valor="<?php echo $comentarios_recibidos; ?>">0</span></strong>
                    </div>
                </div>
            </article>
        </div>
    </section>
