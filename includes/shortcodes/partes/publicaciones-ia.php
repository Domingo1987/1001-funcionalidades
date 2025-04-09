<?php
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();

// Datos simulados (futuros get_* reemplazables)
$tipos_ia = get_publicaciones_ia_por_tipo($user_id);
$valoraciones = get_valoraciones_ia($user_id);
$promedio  = $valoraciones['cantidad_valoraciones'] > 0 
    ? round($valoraciones['estrellas_totales'] / $valoraciones['cantidad_valoraciones'],1) 
    : 0;

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

     <!-- Likes propios  -->
    <h4 style="margin-top: 2rem;">👍 Interacciones con mis publicaciones</h4>
    <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
        <article class="card-1001" style="--color: #ff9800;">
            <div class="barra-color"></div>
            <div class="contenido-card">
                <div class="icono">⭐</div>
                <div class="texto">
                    <p>Valoraciones en IA</p>
                    <strong>
                        <span class="contador-animado" data-valor="<?php echo $valoraciones['estrellas_totales']; ?>">0</span>
                        <span style="font-size: 0.9rem;"> / <?php echo $valoraciones['cantidad_valoraciones']; ?> votos</span>
                    </strong>
                    <?php echo render_estrellas_promedio($promedio); ?>
                </div>
            </div>
        </article>
    </section>

    <!-- Likes propios  
    <h4 style="margin-top: 2rem;">👍 Interacciones con mis publicaciones</h4>
    <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
        <article class="card-1001" style="--color: #ff9800;">
            <div class="barra-color"></div>
            <div class="contenido-card">
                <div class="icono">⭐</div>
                <div class="texto">
                    <p>Valoraciones en IA</p>
                    <strong>
                        <span class="contador-animado" data-valor="<?php echo $valoraciones['estrellas_totales']; ?>">0</span>
                        <span style="font-size: 0.9rem;"> / <?php echo $valoraciones['cantidad_valoraciones']; ?> votos</span>
                    </strong>
                    <div class="estrellas" data-promedio="<?php echo $promedio; ?>"></div>
                </div>
            </div>
        </article>
    </section>-->
</details>
