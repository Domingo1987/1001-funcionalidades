<?php
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();

// Datos simulados (futuros get_* reemplazables)
$tipos_ia = get_publicaciones_ia_por_tipo($user_id);
$likes = get_likes_ia($user_id);
?>

<details>
    <summary>📡 Publicaciones de IA</summary>

    <!-- IA por tipo creado -->
    <h4 style="margin-top: 1rem;">🧠 Tipos de publicaciones IA</h4>
    <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
        <?php foreach ($tipos_ia as $ia): ?>
            <article class="card-1001" style="--color: <?php echo $ia['color']; ?>;">
                <div class="barra-color"></div>
                <div class="contenido-card">
                    <div class="icono"><?php echo $ia['icono']; ?></div>
                    <div class="texto">
                        <p><?php echo $ia['tipo']; ?></p>
                        <strong><span class="contador-animado" data-valor="<?php echo $ia['cantidad']; ?>">0</span></strong>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <!-- Likes propios y dados -->
    <h4 style="margin-top: 2rem;">👍 Interacciones con publicaciones</h4>
    <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
        <article class="card-1001" style="--color: #4caf50;">
            <div class="barra-color"></div>
            <div class="contenido-card">
                <div class="icono">❤️</div>
                <div class="texto">
                    <p>Likes Recibidos</p>
                    <strong><span class="contador-animado" data-valor="<?php echo $likes['recibidos']; ?>">0</span></strong>
                </div>
            </div>
        </article>

        <article class="card-1001" style="--color: #f44336;">
            <div class="barra-color"></div>
            <div class="contenido-card">
                <div class="icono">🤝</div>
                <div class="texto">
                    <p>Likes Dados</p>
                    <strong><span class="contador-animado" data-valor="<?php echo $likes['dados']; ?>">0</span></strong>
                </div>
            </div>
        </article>
    </section>
</details>
