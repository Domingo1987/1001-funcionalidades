<?php
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();

// Simulación de progreso mensual por capítulo (10 filas x 12 columnas)
$progreso = get_progreso_mensual($user_id);

$capitulos = [
    'Cap. 1', 'Cap. 2', 'Cap. 3', 'Cap. 4', 'Cap. 5',
    'Cap. 6', 'Cap. 7', 'Cap. 8', 'Cap. 9', 'IA'
];

$meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
?>

<details>
    <summary>⏳ Progreso por mes y categoría</summary>

    <div class="progreso-matriz">
        <!-- Cabecera de meses -->
        <div class="fila header">
            <div class="celda-etiqueta"></div>
            <?php foreach ($meses as $mes): ?>
                <div class="celda-mes"><?php echo $mes; ?></div>
            <?php endforeach; ?>
        </div>

        <!-- Filas por capítulo -->
        <?php foreach ($capitulos as $i => $nombre): ?>
            <div class="fila">
                <div class="celda-etiqueta"><?php echo $nombre; ?></div>
                <?php for ($m = 0; $m < 12; $m++): 
                    $nivel = $progreso[$i][$m] ?? 0;
                ?>
                    <div class="celda-progreso nivel-<?php echo $nivel; ?>"></div>
                <?php endfor; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <p style="font-size: 0.8rem; color: #777; margin-top: 0.5rem;">*Progreso simulado. Pronto se basará en tus datos reales.</p>
</details>
