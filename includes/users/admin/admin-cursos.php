<?php
if (!defined('ABSPATH')) {
    exit;
}

// Obtener todos los cursos
global $users1001_admin;
$cursos = $users1001_admin->get_all_cursos();
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="users1001-cursos-container">
        <div class="users1001-form-container">
            <h2>Agregar Nuevo Curso</h2>
            <div id="mensaje-curso" class="notice" style="display: none;"></div>
            
            <form id="agregar-curso-form">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="nuevo-curso">Nombre del curso</label></th>
                        <td>
                            <input type="text" id="nuevo-curso" name="nuevo-curso" class="regular-text" required>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button id="submit-curso" class="btn btn-success">Agregar Curso</button>
                </p>
            </form>
        </div>
        
        <div class="users1001-lista-container">
            <h2>Cursos Disponibles</h2>
            
            <?php if (!empty($cursos)) : ?>
                <table class="table table-striped table-hover table-scroll" style="width: 100%;">
                    <thead style="background-color: #32b643; color: #ffffff;">
                        <tr>
                            <th class="text-left">📚 Nombre del Curso</th>
                            <th class="text-center">🛠️ Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-cursos">
                        <?php foreach ($cursos as $curso) : ?>
                            <tr>
                                <td><?php echo esc_html($curso); ?></td>
                                <td class="text-center">
                                    <button class="btn btn-error btn-sm eliminar-curso" data-curso="<?php echo esc_attr($curso); ?>">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else : ?>
                <p>No hay cursos disponibles.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    console.log('📘 JS de Gestión de Cursos cargado');

    // Agregar nuevo curso
    $('#agregar-curso-form').on('submit', function(e) {
        e.preventDefault();

        var cursoProp = $('#nuevo-curso').val();
        console.log('🟢 Enviando curso:', cursoProp);

        if (cursoProp) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'guardar_curso',
                    nonce: users1001_vars.nonce,
                    curso: cursoProp
                },
                success: function(response) {
                    console.log('✅ Respuesta guardar curso:', response);

                    if (response.success) {
                        $('#mensaje-curso')
                            .removeClass('notice-error')
                            .addClass('notice-success')
                            .html('<p>' + response.data.message + '</p>')
                            .show();

                        $('#nuevo-curso').val('');
                        actualizarTablaCursos(response.data.cursos);
                    } else {
                        $('#mensaje-curso')
                            .removeClass('notice-success')
                            .addClass('notice-error')
                            .html('<p>' + response.data + '</p>')
                            .show();
                    }
                },
                error: function(err) {
                    console.error('🚨 Error AJAX guardar curso:', err);
                    $('#mensaje-curso')
                        .removeClass('notice-success')
                        .addClass('notice-error')
                        .html('<p>Error al procesar la solicitud</p>')
                        .show();
                }
            });
        }
    });

    // Eliminar curso
    $(document).on('click', '.eliminar-curso', function() {
        var curso = $(this).data('curso');
        console.log('🧨 Clic en eliminar curso:', curso);

        var confirmar = confirm('¿Está seguro de eliminar el curso "' + curso + '"?');
        if (!confirmar) return;

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'eliminar_curso',
                nonce: users1001_vars.nonce,
                curso: curso
            },
            success: function(response) {
                console.log('✅ Respuesta eliminar curso:', response);

                if (response.success) {
                    $('#mensaje-curso')
                        .removeClass('notice-error')
                        .addClass('notice-success')
                        .html('<p>' + response.data.message + '</p>')
                        .show();

                    actualizarTablaCursos(response.data.cursos);
                } else {
                    $('#mensaje-curso')
                        .removeClass('notice-success')
                        .addClass('notice-error')
                        .html('<p>' + response.data + '</p>')
                        .show();
                }
            },
            error: function(err) {
                console.error('🚨 Error AJAX eliminar curso:', err);
                $('#mensaje-curso')
                    .removeClass('notice-success')
                    .addClass('notice-error')
                    .html('<p>Error al procesar la solicitud</p>')
                    .show();
            }
        });
    });

    function actualizarTablaCursos(cursos) {
        var html = '';

        if (cursos.length > 0) {
            $.each(cursos, function(index, curso) {
                html += '<tr>' +
                    '<td>' + curso + '</td>' +
                    '<td><button class="btn btn-error btn-sm eliminar-curso" data-curso="' + curso + '">Eliminar</button></td>' +
                    '</tr>';
            });
        } else {
            html = '<tr><td colspan="2">No hay cursos disponibles.</td></tr>';
        }

        $('#lista-cursos').html(html);
    }
});
</script>
