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
// Variables de paginación
$usuarios_por_pagina = 50;
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_actual - 1) * $usuarios_por_pagina;

$usuarios_args = [
    'role'    => 'estudiante',
    'number'  => $usuarios_por_pagina,
    'offset'  => $offset,
    'orderby' => 'display_name',
    'order'   => 'ASC'
];

$usuarios = get_users($usuarios_args);
$total_usuarios = count(get_users(['role' => 'estudiante']));
$total_paginas = ceil($total_usuarios / $usuarios_por_pagina);
$page_slug = 'users1001-usuarios';
$current_url = admin_url('admin.php?page=' . $page_slug);

?>

<div class="container grid-lg">
    <h1 class="text-bold">👥 Gestionar Historial Académico</h1>
    <p class="text-gray">Selecciona estudiantes y asígnales curso, centro y año. Se agregará a su historial sin borrar entradas anteriores.</p>

    <form id="form-asignar-historial" class="form-horizontal">
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

        <div class="columns">
            <?php foreach (array_chunk($usuarios, ceil(count($usuarios)/2)) as $columna): ?>
                <div class="column col-6">
                    <table class="table table-striped table-hover">
                        <thead style="background-color: #32b643; color: #ffffff;">
                            <tr>
                                <th class="text-center" style="width:40px;"><input type="checkbox" id="select-all"></th>
                                <th>🆔 ID</th>
                                <th>👤 Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($columna as $user): ?>
                                <tr>
                                    <td><input type="checkbox" class="user-checkbox" name="usuarios[]" value="<?= esc_attr($user->ID); ?>"></td>
                                    <td><?= esc_html($user->ID); ?></td>
                                    <td><?= esc_html($user->display_name); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="mensaje-historial" class="toast mt-2" style="display:none;"></div>

        <?php if ($total_paginas > 1): ?>
        <ul class="pagination mt-2">
            <?php if ($pagina_actual > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $current_url . '&pagina=' . ($pagina_actual - 1); ?>">« Prev</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?= $i == $pagina_actual ? 'active' : ''; ?>">
                    <a class="page-link" href="<?= $current_url . '&pagina=' . $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($pagina_actual < $total_paginas): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $current_url . '&pagina=' . ($pagina_actual + 1); ?>">Next »</a>
                </li>
            <?php endif; ?>
        </ul>
        <?php endif; ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    console.log('📘 JS de Gestión de Usuarios cargado');

    // 🔹 Seleccionar todos los checkboxes
    $(document).on('change', '#select-all', function() {
        $('.user-checkbox').prop('checked', this.checked);
    });

    // 🔹 Enviar formulario de asignación de historial
    $('#form-asignar-historial').on('submit', function(e) {
        e.preventDefault();

        const userIds = $('.user-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        const curso = $('#curso').val();
        const centro = $('#centro').val();
        const anio = $('#anio').val();
        const nonce = $('#asignar_historial_nonce').val();

        console.log('📝 Enviando historial para:', userIds);

        if (userIds.length === 0 || !curso || !centro || !anio) {
            alert('Completa todos los campos y selecciona al menos un estudiante.');
            return;
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'guardar_historial_usuario',
                usuarios: userIds,
                curso: curso,
                centro: centro,
                anio: anio,
                nonce: nonce
            },
            success: function(response) {
                console.log('✅ Respuesta servidor:', response);
                const mensaje = $('#mensaje-historial');

                if (response.success) {
                    mensaje.removeClass('error').addClass('toast-success').html('<p>' + response.data + '</p>').fadeIn();
                } else {
                    mensaje.removeClass('toast-success').addClass('toast-error').html('<p>' + response.data + '</p>').fadeIn();
                }
            },
            error: function(err) {
                console.error('🚨 Error AJAX:', err);
                $('#mensaje-historial').removeClass('toast-success').addClass('toast-error').html('<p>Error al procesar la solicitud.</p>').fadeIn();
            }
        });
    });
});
</script>