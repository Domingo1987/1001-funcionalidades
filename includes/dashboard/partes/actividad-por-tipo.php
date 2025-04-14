<?php
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();

// Simulación de funciones
$categorias = get_problemas_por_categoria($user_id);
$lenguajes = get_lenguajes_por_tipo($user_id);
?>


    <!-- Problemas por categoría -->
    <h4 style="margin-top: 1rem;">📚 Problemas por categoría</h4>
    <section class="resumen-general-cards">
        <?php foreach ($categorias as $cat): 
            $porcentaje = $cat['total'] > 0 ? round(($cat['resueltos'] / $cat['total']) * 100) : 0;
        ?>
            <article class="card-1001" style="--color: <?php echo $cat['color']; ?>;">
                <div class="barra-color"></div>
                <div class="contenido-card">
                    <div class="icono"><?php echo $cat['icono']; ?></div>
                    <div class="texto">
                        <p><?php echo $cat['nombre']; ?></p>
                        <strong><?php echo "{$cat['resueltos']} / {$cat['total']}"; ?></strong>
                        <div class="barra">
                            <div class="relleno" style="width: <?php echo $porcentaje; ?>%;"></div>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <!-- Lenguajes utilizados -->
    <h4 style="margin-top: 2rem;">💻 Lenguajes utilizados</h4>
    <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
        <?php foreach ($lenguajes as $lang): ?>
            <article class="card-1001" style="--color: <?php echo $lang['color']; ?>;">
                <div class="barra-color"></div>
                <div class="contenido-card">
                    <div class="icono"><?php echo $lang['icono']; ?></div>
                    <div class="texto">
                        <p><?php echo $lang['nombre']; ?></p>
                        <strong><?php echo $lang['porcentaje']; ?>%</strong>
                        <div class="barra">
                            <div class="relleno" style="width: <?php echo $lang['porcentaje']; ?>%;"></div>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
