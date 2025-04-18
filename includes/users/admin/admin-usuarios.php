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

            <div class="column col-sm-6 col-md-3 d-flex flex-justify-end align-end">
                <?php wp_nonce_field('asignar_historial', 'asignar_historial_nonce'); ?>
                <button type="submit" class="btn btn-primary mt-2">✅ Asignar</button>
            </div>
        </div>

        <!-- 📋 Tabla de usuarios -->
        <div class="table-responsive">
            <table class="table table-striped table-hover table-scroll" style="width: 100%;">
                <thead style="background-color: #32b643; color: #ffffff;">
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                        <th>🆔 ID</th>
                        <th>👤 Nombre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td><input type="checkbox" class="user-checkbox" name="usuarios[]" value="<?= esc_attr($user->ID); ?>"></td>
                            <td><?= esc_html($user->ID); ?></td>
                            <td><?= esc_html($user->display_name); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="mensaje-historial" class="toast mt-2" style="display:none;"></div>
    </form>
</div>
