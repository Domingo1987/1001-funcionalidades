<?php
if (!defined('ABSPATH')) exit;

$tipos_ia     = dashboardData['publicacionesIA'] ?? [];
$valoraciones = dashboardData['valoracionesIA'] ?? ['estrellas_totales' => 0, 'cantidad_valoraciones' => 0];
$promedio     = dashboardData['promedioIA'] ?? 0;

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
