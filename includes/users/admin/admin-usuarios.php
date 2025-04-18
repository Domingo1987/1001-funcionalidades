<?php

if (!defined('ABSPATH')) {
    exit; // Evita el acceso directo
}

if (!current_user_can('manage_options')) {
    wp_die('No tienes permisos suficientes para acceder a esta página.');
}

$usuarios = get_users(['role' => 'Estudiante']);

$cursos = get_all_cursos(); // función ya existente
$centros = get_all_centros(); // función ya existente

?>

<div class="wrap">
    <h1>Gestionar Historial Académico</h1>

    <p>Selecciona estudiantes y asígnales curso, centro y año. Se agregará a su historial sin borrar entradas anteriores.</p>

    <form id="form-asignar-historial">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Nombre</th>
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

        <h3>Datos a asignar</h3>

        <label for="curso">Curso:</label>
        <select id="curso" name="curso" required>
            <option value="">Selecciona curso</option>
            <?php foreach ($cursos as $curso): ?>
                <option value="<?= esc_attr($curso); ?>"><?= esc_html($curso); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="centro">Centro:</label>
        <select id="centro" name="centro" required>
            <option value="">Selecciona centro</option>
            <?php foreach ($centros as $centro): ?>
                <option value="<?= esc_attr($centro); ?>"><?= esc_html($centro); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="anio">Año:</label>
        <input type="number" id="anio" name="anio" value="<?= date('Y'); ?>" required>

        <?php wp_nonce_field('asignar_historial', 'asignar_historial_nonce'); ?>

        <p>
            <button type="submit" class="button button-primary">Asignar a seleccionados</button>
        </p>
    </form>

    <div id="mensaje-historial" style="display:none; margin-top: 1rem;"></div>
</div>