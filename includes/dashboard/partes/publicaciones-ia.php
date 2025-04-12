<?php
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();

// Simulación de funciones
$likes_dados = get_likes_dados($user_id);
$likes_recibidos = get_likes_recibidos($user_id);
$comentarios_hechos = get_comentarios_hechos($user_id);
$comentarios_recibidos = get_comentarios_recibidos($user_id);
?>

<details>
    <summary>📊 Comentarios por publicación IA</summary>
    <section style="margin-top: 2rem;">
        <div id="grafico-interacciones-ia" style="max-width: 100%; margin: auto;"></div>
    </section>
</details>
