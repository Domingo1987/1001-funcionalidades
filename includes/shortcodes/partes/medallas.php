<?php
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();
$logradas = get_medallas_logradas($user_id);
$pendientes = get_medallas_pendientes($user_id);
?>

<details>
    <summary>🏆 Medallas logradas y pendientes</summary>

    <!-- Medallas logradas -->
    <h4 style="margin-top: 1rem;">✨ Logros obtenidos</h4>
    <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
        <?php foreach ($logradas as $medalla): ?>
            <article class="card-1001" style="--color: <?php echo $medalla['color']; ?>;">
                <div class="barra-color"></div>
                <div class="contenido-card">
                    <div class="icono"><?php echo $medalla['icono']; ?></div>
                    <div class="texto">
                        <p><?php echo $medalla['nombre']; ?></p>
                        <strong>✔️</strong>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <!-- Medallas pendientes -->
    <h4 style="margin-top: 2rem;">🕓 En camino</h4>
    <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
        <?php foreach ($pendientes as $medalla): ?>
            <article class="card-1001" style="--color: #ccc;">
                <div class="barra-color"></div>
                <div class="contenido-card">
                    <div class="icono"><?php echo $medalla['icono']; ?></div>
                    <div class="texto">
                        <p><?php echo $medalla['nombre']; ?></p>
                        <strong>❌</strong>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</details>
