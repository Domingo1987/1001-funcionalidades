<?php
// Archivo: shortcodes/dashboard.php

if (!defined('ABSPATH')) exit;

function shortcode_dashboard() {
    if (!is_user_logged_in()) {
        return '<article class="contrast"><p>Debes estar logueado para ver tu tablero personalizado.</p></article>';
    }

    // Datos simulados (luego irán desde la BD)
    $problemas = 145;
    $puntaje = 17.8;
    $tendencia = +12.5;
    $comentarios = 62;
    $ia_posts = 7;
    $medallas = 3;

    $color = $tendencia >= 0 ? 'green' : 'red';
    $icono = $tendencia >= 0 ? '⬆️' : '⬇️';
    $tendencia_valor = abs($tendencia); // solo el número positivo

    // Datos de la base de datos (simulados)
    global $wpdb;
    $user_id = get_current_user_id();

    ob_start();
    ?>
    <section class="container">
        <details open>
            <summary>📌 Resumen general</summary>
            <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; margin-top: 1rem;">
                <!-- PROBLEMAS -->
                <article class="card-1001" style="--color: #00c2ff;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                        <div class="icono">💻</div>
                        <div class="texto">
                            <p>Problemas</p>
                            <strong><span class="contador-animado" data-valor="<?php echo $problemas; ?>">0</span></strong>
                            <div class="barra">
                                <div class="relleno" style="width: <?php echo min(100, ($problemas / 1000) * 100); ?>%;"></div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- PUNTAJE PROMEDIO -->
                <article class="card-1001" style="--color: #4caf50;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                        <div class="icono">📊</div>
                        <div class="texto">
                            <p>Puntaje Promedio</p>
                            <strong><span class="contador-animado" data-valor="<?php echo $puntaje; ?>">0</span></strong>
                        </div>
                    </div>
                </article>

                <!-- TENDENCIA -->
                <article class="card-1001" style="--color: <?php echo $color; ?>;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                        <div class="icono"><?php echo $icono; ?></div>
                        <div class="texto">
                            <p>Tendencia</p>
                            <strong style="color: <?php echo $color; ?>;"><span class="contador-animado" data-valor="<?php echo $tendencia_valor; ?>">0</span>%</strong>
                        </div>
                    </div>
                </article>

                <!-- COMENTARIOS -->
                <article class="card-1001" style="--color: #2196f3;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                        <div class="icono">💬</div>
                        <div class="texto">
                            <p>Comentarios</p>
                            <strong><span class="contador-animado" data-valor="<?php echo $comentarios; ?>">0</span></strong>
                        </div>
                    </div>
                </article>

                <!-- POST IA -->
                <article class="card-1001" style="--color: #9c27b0;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                        <div class="icono">🤖</div>
                        <div class="texto">
                            <p>Post IA</p>
                            <strong><span class="contador-animado" data-valor="<?php echo $ia_posts; ?>">0</span></strong>
                        </div>
                    </div>
                </article>

                <!-- MEDALLAS -->
                <article class="card-1001" style="--color: #fbc02d;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                        <div class="icono">🏅</div>
                        <div class="texto">
                            <p>Medallas</p>
                            <strong><span class="contador-animado" data-valor="<?php echo $medallas; ?>">0</span></strong>
                        </div>
                    </div>
                </article>

                <!-- TIEMPO EN PLATAFORMA -->
                <article class="card-1001" style="--color: #ff9800;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                        <div class="icono">⏱️</div>
                        <div class="texto">
                            <p>Tiempo (h)</p>
                            <strong><span class="contador-animado" data-valor="<?php echo $tiempo; ?>">0</span></strong>
                        </div>
                    </div>
                </article>

                <!-- ACTIVIDAD SEMANAL -->
                <article class="card-1001" style="--color: #795548;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                        <div class="icono">📅</div>
                        <div class="texto">
                            <p>Actividad Semanal</p>
                            <strong><span class="contador-animado" data-valor="<?php echo $actividad; ?>">0</span></strong>
                        </div>
                    </div>
                </article>
            </section>
        </details>
        
        <!-- 🧩 Actividad por tipo de contenido -->
        <details>
            <summary>🔍 Actividad por tipo de contenido</summary>

            <!-- Problemas por categoría -->
            <h4 style="margin-top: 1rem;">📚 Problemas por categoría</h4>
            <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
                <?php
                $categorias = [
                    ['nombre' => 'Secuenciales', 'icono' => '📘', 'color' => '#00bcd4', 'total' => 40, 'resueltos' => 28],
                    ['nombre' => 'Condicionales', 'icono' => '📙', 'color' => '#ff9800', 'total' => 30, 'resueltos' => 22],
                    ['nombre' => 'Bucles', 'icono' => '📗', 'color' => '#4caf50', 'total' => 20, 'resueltos' => 15],
                ];

                foreach ($categorias as $cat) {
                    $porcentaje = $cat['total'] > 0 ? round(($cat['resueltos'] / $cat['total']) * 100) : 0;
                    ?>
                    <article class="card-1001" style="--color: <?php echo $cat['color']; ?>;">
                        <div class="barra-color"></div>
                        <div class="contenido-card">
                            <div class="icono"><?php echo $cat['icono']; ?></div>
                            <div class="texto">
                                <p><?php echo $cat['nombre']; ?></p>
                                <strong><?php echo $cat['resueltos'] . ' / ' . $cat['total']; ?></strong>
                                <div class="barra">
                                    <div class="relleno" style="width: <?php echo $porcentaje; ?>%;"></div>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php } ?>
            </section>

            <!-- Lenguajes -->
            <h4 style="margin-top: 2rem;">💻 Lenguajes utilizados</h4>
            <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
                <?php
                $lenguajes = [
                    ['nombre' => 'Python', 'icono' => '🐍', 'color' => '#4caf50', 'porcentaje' => 65],
                    ['nombre' => 'Java', 'icono' => '☕', 'color' => '#f44336', 'porcentaje' => 25],
                    ['nombre' => 'C', 'icono' => '🔧', 'color' => '#2196f3', 'porcentaje' => 10],
                ];

                foreach ($lenguajes as $lang) {
                    ?>
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
                <?php } ?>
            </section>
        </details>

        <details>
            <summary>📡 Publicaciones de IA</summary>

            <!-- IA por tipo creado -->
            <h4 style="margin-top: 1rem;">🧠 Tipos de publicaciones IA</h4>
            <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
                <?php
                $tipos_ia = [
                    ['tipo' => 'Imagen', 'icono' => '🖼️', 'color' => '#3f51b5', 'cantidad' => 4],
                    ['tipo' => 'Chatbot', 'icono' => '💬', 'color' => '#009688', 'cantidad' => 2],
                    ['tipo' => 'Música', 'icono' => '🎵', 'color' => '#e91e63', 'cantidad' => 1],
                ];

                foreach ($tipos_ia as $ia) {
                    ?>
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
                <?php } ?>
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
                            <strong><span class="contador-animado" data-valor="28">0</span></strong>
                        </div>
                    </div>
                </article>

                <article class="card-1001" style="--color: #f44336;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                        <div class="icono">🤝</div>
                        <div class="texto">
                            <p>Likes Dados</p>
                            <strong><span class="contador-animado" data-valor="15">0</span></strong>
                        </div>
                    </div>
                </article>
            </section>
        </details>

        <details>
            <summary>🏆 Medallas logradas y pendientes</summary>

            <!-- Medallas logradas -->
            <h4 style="margin-top: 1rem;">✨ Logros obtenidos</h4>
            <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
                <?php
                $medallas_logradas = [
                    ['nombre' => 'Primer Problema', 'icono' => '🥇', 'color' => '#ffd700'],
                    ['nombre' => '5 Comentarios', 'icono' => '💬', 'color' => '#4caf50'],
                    ['nombre' => 'IA Inicial', 'icono' => '🤖', 'color' => '#9c27b0'],
                ];

                foreach ($medallas_logradas as $medalla) {
                    ?>
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
                <?php } ?>
            </section>

            <!-- Medallas pendientes -->
            <h4 style="margin-top: 2rem;">🕓 En camino</h4>
            <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
                <?php
                $medallas_pendientes = [
                    ['nombre' => '50 Problemas', 'icono' => '🎯'],
                    ['nombre' => '10 IA Publicadas', 'icono' => '🤖'],
                    ['nombre' => '100 Comentarios', 'icono' => '💬'],
                ];

                foreach ($medallas_pendientes as $medalla) {
                    ?>
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
                <?php } ?>
            </section>
        </details>




        <!-- 💬 Interacciones sociales -->
        <details>
            <summary>💬 Interacciones sociales</summary>

            <section style="margin-top: 1rem;">
                <h4>👍 Likes</h4>
                <div class="resumen-general-cards" style="display: flex; gap: 1rem;">
                <article class="card-1001" style="--color: #00bcd4;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                    <div class="icono">❤️</div>
                    <div class="texto">
                        <p>Dado</p>
                        <strong>19</strong>
                    </div>
                    </div>
                </article>
                <article class="card-1001" style="--color: #e91e63;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                    <div class="icono">🎯</div>
                    <div class="texto">
                        <p>Recibido</p>
                        <strong>11</strong>
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
                        <strong>22</strong>
                    </div>
                    </div>
                </article>
                <article class="card-1001" style="--color: #8bc34a;">
                    <div class="barra-color"></div>
                    <div class="contenido-card">
                    <div class="icono">📨</div>
                    <div class="texto">
                        <p>Recibidos</p>
                        <strong>8</strong>
                    </div>
                    </div>
                </article>
                </div>
            </section>
        </details>

        <!-- 🎯 Progreso por competencias -->
        <details>
            <summary>🎯 Progreso por competencias</summary>
            <p style="margin: 1rem;">Próximamente se habilitará esta sección para visualizar avances según las competencias evaluadas.</p>
        </details>

        <!-- ⏳ Evolución temporal -->
        <details>
            <summary>⏳ Evolución temporal</summary>

            <div style="margin-top: 1rem;">
                <p>Simulación de actividad diaria (últimos 84 días):</p>
                <div class="heatmap-github">
                    <?php
                    // Simular 12 semanas (7 días por semana)
                    for ($semana = 0; $semana < 12; $semana++) {
                        echo '<div class="semana">';
                        for ($dia = 0; $dia < 7; $dia++) {
                            // Simular nivel de actividad (0 a 4)
                            $nivel = rand(0, 4);
                            echo "<div class='dia actividad-$nivel'></div>";
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
                <p style="font-size: 0.8rem; color: #777; margin-top: 0.5rem;">*Actividad simulada. Pronto conectada a tus interacciones reales.</p>
            </div>


        </details>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('dashboard', 'shortcode_dashboard');


/*function shortcode_dashboard() {

    global $wpdb;
    $user_id = get_current_user_id();

    // 1. Datos principales
    $total_eval = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}evaluaciones WHERE user_id = %d", $user_id));
    $prom_eval = $wpdb->get_var($wpdb->prepare("SELECT AVG(total_puntos) FROM {$wpdb->prefix}evaluaciones WHERE user_id = %d", $user_id));
    $mensajes = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->comments} WHERE user_id = %d", $user_id));
    $resueltos = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(DISTINCT p.ID) 
        FROM {$wpdb->posts} p
        JOIN {$wpdb->comments} c ON p.ID = c.comment_post_ID
        JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE c.user_id = %d 
        AND pm.meta_key = 'num_problema'
        AND p.post_status = 'publish'
    ", $user_id));

    // 2. Tendencia
    $ultimas = $wpdb->get_col($wpdb->prepare("SELECT id FROM {$wpdb->prefix}evaluaciones WHERE user_id = %d ORDER BY fecha_evaluacion DESC LIMIT 5", $user_id));
    $prom_ult = !empty($ultimas) ? $wpdb->get_var("SELECT AVG(total_puntos) FROM {$wpdb->prefix}evaluaciones WHERE id IN (" . implode(",", $ultimas) . ")") : 0;
    $prom_hist = !empty($ultimas) ? $wpdb->get_var("SELECT AVG(total_puntos) FROM {$wpdb->prefix}evaluaciones WHERE user_id = $user_id AND id NOT IN (" . implode(",", $ultimas) . ")") : 0;
    $mejora = $prom_hist > 0 ? number_format((($prom_ult - $prom_hist) / $prom_hist) * 100, 2) : 0;
    $clase_mejora = $mejora > 0 ? 'green' : 'red';
    $icono = $mejora > 0 ? '↑' : '↓';

    // 3. Gráfico radar (usuario vs grupo)
    $grafico1_usuario = $wpdb->get_results($wpdb->prepare("
        SELECT ec.criterio_id, AVG(ec.criterio_puntos) as promedio_puntos
        FROM {$wpdb->prefix}evaluaciones_criterios ec
        JOIN {$wpdb->prefix}evaluaciones e ON ec.evaluacion_id = e.id
        WHERE e.user_id = %d
        GROUP BY ec.criterio_id
        ORDER BY FIELD(ec.criterio_id, 1, 2, 4, 3)
    ", $user_id));

    $grafico1_grupo = $wpdb->get_results("
        SELECT ec.criterio_id, AVG(ec.criterio_puntos) as promedio_puntos
        FROM {$wpdb->prefix}evaluaciones_criterios ec
        JOIN {$wpdb->prefix}evaluaciones e ON ec.evaluacion_id = e.id
        GROUP BY ec.criterio_id
        ORDER BY FIELD(ec.criterio_id, 1, 2, 4, 3)
    ");

    // 4. Gráfico de barras (últimas 10 evaluaciones)
    $grafico2 = $wpdb->get_results($wpdb->prepare("
        SELECT e.id, e.total_puntos, DATE_FORMAT(e.fecha_evaluacion, '%%Y-%%m-%%d') as fecha_evaluacion
        FROM {$wpdb->prefix}evaluaciones e
        WHERE e.user_id = %d
        ORDER BY e.fecha_evaluacion DESC
        LIMIT 10
    ", $user_id));

    ob_start();
    ?>
*/



   