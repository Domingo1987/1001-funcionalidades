<?php

if (!defined('ABSPATH')) {
    exit; // Evita el acceso directo
}

if (!current_user_can('manage_options')) {
    wp_die('No tienes permisos suficientes para acceder a esta página.');
}

$usuarios = get_users(['role' => 'Estudiante']);

global $users1001_admin;
$cursos = $users1001_admin->get_all_cursos();
$centros = $users1001_admin->get_all_centros();

// 🔢 Paginación
$usuarios_por_pagina = 50;
$total_usuarios = count($usuarios);
$total_paginas = ceil($total_usuarios / $usuarios_por_pagina);
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$inicio = ($pagina_actual - 1) * $usuarios_por_pagina;
$usuarios_pagina = array_slice($usuarios, $inicio, $usuarios_por_pagina);
$columnas = array_chunk($usuarios_pagina, ceil(count($usuarios_pagina) / 2));
?>

<div class="container grid-lg">
    <h1 class="text-bold">👥 Gestionar Historial Académico</h1>
    <p class="text-gray">Selecciona estudiantes y asígnales curso, centro y año. Se agregará a su historial sin borrar entradas anteriores.</p>

    <form id="form-asignar-historial" class="form-horizontal">
        <!-- 🔹 Selector de curso, centro, año y botón -->
        <div class="columns col-gapless col-oneline mb-2">
            <div class="column col-sm-12 col-md-3">
                <label for="curso" class="form-label">📘 Curso:</label>
                <select id="curso" name="curso" class="form-select" required>
                    <option value="">Selecciona curso</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?= esc_attr($curso); ?>"><?= esc_html($curso); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="column col-sm-12 col-md-4">
                <label for="centro" class="form-label">🏫 Centro:</label>
                <select id="centro" name="centro" class="form-select" required>
                    <option value="">Selecciona centro</option>
                    <?php foreach ($centros as $centro): ?>
                        <option value="<?= esc_attr($centro); ?>"><?= esc_html($centro); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="column col-sm-6 col-md-2">
                <label for="anio" class="form-label">📅 Año:</label>
                <input type="number" id="anio" name="anio" class="form-input" value="<?= date('Y'); ?>" required>
            </div>

            <div class="column col-sm-12 col-md-2 d-flex flex-items-end">
                <?php wp_nonce_field('asignar_historial', 'asignar_historial_nonce'); ?>
                <button type="submit" class="btn btn-primary btn-block mt-2">✅ Asignar</button>
            </div>
        </div>

        <!-- 📋 Tabla en columnas -->
        <div class="columns">
            <?php foreach ($columnas as $col): ?>
                <div class="column col-6">
                    <table class="table table-striped table-hover table-scroll">
                        <thead style="background-color: #32b643; color: white;">
                            <tr>
                                <th class="text-center"><input type="checkbox" class="select-col"></th>
                                <th class="text-center">🆔 ID</th>
                                <th>👤 Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($col as $user): ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="user-checkbox" name="usuarios[]" value="<?= esc_attr($user->ID); ?>"></td>
                                    <td class="text-center"><?= esc_html($user->ID); ?></td>
                                    <td><?= esc_html($user->display_name); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="mensaje-historial" class="toast mt-2" style="display:none;"></div>
    </form>

    <!-- 🔁 Paginación -->
    <ul class="pagination mt-2">
        <li class="page-item <?= ($pagina_actual == 1) ? 'disabled' : '' ?>">
            <a href="?pagina=<?= max(1, $pagina_actual - 1); ?>">« Prev</a>
        </li>
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                <a href="?pagina=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= ($pagina_actual == $total_paginas) ? 'disabled' : '' ?>">
            <a href="?pagina=<?= min($total_paginas, $pagina_actual + 1); ?>">Next »</a>
        </li>
    </ul>
</div>
