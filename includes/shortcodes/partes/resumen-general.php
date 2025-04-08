<?php
// Archivo: shortcodes/partes/resumen-general.php

if (!defined('ABSPATH')) exit;

// ID del usuario actual
$user_id = get_current_user_id();

// Simulaciones con funciones que luego se definirán en estadisticas-dashboard.php
$problemas       = get_problemas_resueltos($user_id);
$puntaje         = get_puntaje_promedio($user_id);
$tendencia       = get_tendencia_porcentual($user_id);
$comentarios     = get_cantidad_comentarios($user_id);
$ia_posts        = get_ia_publicadas($user_id);
$medallas        = get_cantidad_medallas($user_id);
$tiempo          = get_tiempo_total_plataforma($user_id);
$actividad       = get_actividad_semanal($user_id);

// Variables derivadas
$color           = $tendencia >= 0 ? 'green' : 'red';
$icono           = $tendencia >= 0 ? '⬆️' : '⬇️';
$tendencia_valor = abs($tendencia);
?>

<details open>
  <summary>📌 Resumen general</summary>
  <section class="resumen-general-cards" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; margin-top: 1rem;">
    
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

    <article class="card-1001" style="--color: #ff9800;">
      <div class="barra-color"></div>
      <div class="contenido-card">
          <div class="icono">❓</div>
          <div class="texto">
            <p>Preguntas creadas</p>
            <strong><span class="contador-animado" data-valor="<?php echo $preguntas_creadas; ?>">0</span></strong>
          </div>
      </div>
    </article>

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
