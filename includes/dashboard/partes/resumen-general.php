<?php
// Archivo: shortcodes/partes/resumen-general.php

if (!defined('ABSPATH')) exit;

$problemas         = dashboardData['problemas'] ?? 0;
$puntaje           = dashboardData['puntaje'] ?? 0;
$tendencia         = dashboardData['tendencia'] ?? 0;
$comentarios       = dashboardData['comentarios'] ?? 0;
$ia_posts          = dashboardData['ia_posts'] ?? 0;
$medallas          = dashboardData['medallas'] ?? 0;
$preguntas_creadas = dashboardData['preguntas'] ?? 0;
$actividad         = dashboardData['actividadSemanal'] ?? 0;

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
