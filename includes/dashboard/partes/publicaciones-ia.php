<?php
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();

// Simulación de funciones
$likes_dados = get_likes_dados($user_id);
$likes_recibidos = get_likes_recibidos($user_id);
$comentarios_hechos = get_comentarios_hechos($user_id);
$comentarios_recibidos = get_comentarios_recibidos($user_id);
?>

<details open>
    <summary>📊 Comentarios por publicación IA</summary>
    <section class="container" style="max-width: 700px; margin: auto; text-align: center;">
        <div id="grafico-publicaciones-ia-loader" class="text-muted" data-theme="pico">
            <progress style="width: 50%; margin-top: 1rem;"></progress>
            <p style="margin-top: 0.5rem;">Cargando gráfico...</p>
        </div>
        <div id="grafico-publicaciones-ia" style="max-width: 80%; margin: auto;"></div>
    </section>

</details>
