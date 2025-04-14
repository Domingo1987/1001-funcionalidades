<?php
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();
// Aquí en el futuro podríamos usar funciones como get_competencias_usuario($user_id);
?>

  <section class="container" style="max-width: 700px; margin: auto; text-align: center;">
    <div id="grafico-radar-competencias-loader" class="text-muted">
      <progress style="width: 50%; margin-top: 1rem;"></progress>
      <p style="margin-top: 0.5rem;">Cargando gráfico de competencias...</p>
    </div>
    <div id="grafico-radar-competencias"></div>
  </section>




