<?php
if (!defined('ABSPATH')) exit;

if (!current_user_can('manage_options')) {
    wp_die('No tienes permisos suficientes para acceder a esta página.');
}

// Obtener listas de cursos y centros
global $users1001_admin;
$cursos = $users1001_admin->get_all_cursos();
$centros = $users1001_admin->get_all_centros();
$año_actual = date('Y');

$anio = isset($_GET['anio']) ? sanitize_text_field($_GET['anio']) : $año_actual;
$curso = isset($_GET['curso']) ? sanitize_text_field($_GET['curso']) : '';
$centro = isset($_GET['centro']) ? sanitize_text_field($_GET['centro']) : '';

$resultado = [];
if ($anio) {
    $usuarios = get_users(['role' => 'estudiante']);
    
    foreach ($usuarios as $user) {
        $historial = get_user_meta($user->ID, 'historico_academico', true);
        $historial = json_decode($historial, true);

        if (isset($historial[$anio])) {
            foreach ($historial[$anio] as $entrada) {
                echo '<script>console.log("📘 Historial curso esperado: ' . esc_js($curso) . '");</script>';
                echo '<script>console.log("📗 Curso en historial:", ' . json_encode($entrada['curso']) . ');</script>';
                if (($entrada['curso'] === $curso || $curso === '') 
                    && ($entrada['centro'] === $centro || $centro === '')) {
                    $resultado[] = [
                        'id' => $user->ID,
                        'nombre' => $user->display_name,
                        'anio' => $anio,
                        'curso' => $entrada['curso'],
                        'centro' => $entrada['centro'],
                    ];
                    break;
                }
            }
        }
    }

    // ✅ Ordenar por nombre (alfabéticamente)
    usort($resultado, function($a, $b) {
        return strcmp($a['nombre'], $b['nombre']);
    });
}

// Mostrar en consola
echo '<script>console.log("📊 Resultado filtrado:", ' . json_encode($resultado) . ');</script>';



?>

<div class="container grid-lg">
    <h1 class="text-bold">🔍 Buscar estudiantes por historial</h1>

    <form method="GET" class="form-horizontal">
        <input type="hidden" name="page" value="users1001-buscar">

        <div class="columns col-gapless col-oneline mb-2">
            <div class="column col-sm-12 col-md-3">
                <label class="form-label">📅 Año:</label>
                <input type="number" name="anio" class="form-input" value="<?= esc_attr($anio); ?>">
            </div>

            <div class="column col-sm-12 col-md-4">
                <label class="form-label">📘 Curso:</label>
                <select name="curso" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?= esc_attr($c); ?>" <?= $curso === $c ? 'selected' : ''; ?>><?= esc_html($c); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="column col-sm-12 col-md-4">
                <label class="form-label">🏫 Centro:</label>
                <select name="centro" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($centros as $ce): ?>
                        <option value="<?= esc_attr($ce); ?>" <?= $centro === $ce ? 'selected' : ''; ?>><?= esc_html($ce); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="column col-md-1 d-flex flex-items-end">
                <button type="submit" class="btn btn-primary">🔎 Buscar</button>
            </div>
        </div>
    </form>

    <?php if ($anio): ?>
        <h3 class="mt-2">👥 Resultados</h3>
        <?php if (!empty($resultado)): ?>
            <table class="table table-striped table-hover">
                <thead style="background-color:#667eea; color:white">
                    <tr>
                        <th>🆔 ID</th>
                        <th>👤 Nombre</th>
                        <th>📘 Curso</th>
                        <th>🏫 Centro</th>
                        <th>📅 Año</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultado as $r): ?>
                        <tr>
                            <td><?= esc_html($r['id']); ?></td>
                            <td><?= esc_html($r['nombre']); ?></td>
                            <td><?= esc_html($r['curso']); ?></td>
                            <td><?= esc_html($r['centro']); ?></td>
                            <td><?= esc_html($r['anio']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="toast toast-warning">No se encontraron estudiantes con esos datos.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>