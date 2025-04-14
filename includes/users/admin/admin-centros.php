<?php
if (!defined('ABSPATH')) {
    exit;
}

// Obtener todos los centros
$admin = new Admin();
$centros = $admin->get_all_centros();
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="users1001-centros-container">
        <div class="users1001-form-container">
            <h2>Agregar Nuevo Centro</h2>
            <div id="mensaje-centro" class="notice" style="display: none;"></div>
            
            <form id="agregar-centro-form">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="nuevo-centro">Nombre del centro</label></th>
                        <td>
                            <input type="text" id="nuevo-centro" name="nuevo-centro" class="regular-text" required>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" id="submit-centro" class="btn btn-success">Agregar Centro</button>
                </p>
            </form>


        </div>
        
        <div class="users1001-lista-container">
            <h2>Centros Disponibles</h2>
            
            <?php if (!empty($centros)) : ?>
                <table class="table table-striped table-hover table-scroll" style="width: 100%;">
                    <thead style="background-color: #32b643; color: white;">
                        <tr>
                            <th class="text-left">🏫 Nombre del Centro</th>
                            <th class="text-center">🛠️ Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-centros">
                        <?php foreach ($centros as $centro) : ?>
                            <tr>
                                <td><?php echo esc_html($centro); ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-error btn-sm eliminar-centro" data-centro="<?php echo esc_attr($centro); ?>">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No hay centros disponibles.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Agregar nuevo centro
    $('#agregar-centro-form').on('submit', function(e) {
        e.preventDefault();
        
        var centroProp = $('#nuevo-centro').val();
        
        if (centroProp) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'guardar_centro',
                    nonce: users1001_vars.nonce,
                    centro: centroProp
                },
                success: function(response) {
                    if (response.success) {
                        $('#mensaje-centro')
                            .removeClass('notice-error')
                            .addClass('notice-success')
                            .html('<p>' + response.data.message + '</p>')
                            .show();
                        
                        $('#nuevo-centro').val('');
                        actualizarTablaCentros(response.data.centros);
                    } else {
                        $('#mensaje-centro')
                            .removeClass('notice-success')
                            .addClass('notice-error')
                            .html('<p>' + response.data + '</p>')
                            .show();
                    }
                },
                error: function() {
                    $('#mensaje-centro')
                        .removeClass('notice-success')
                        .addClass('notice-error')
                        .html('<p>Error al procesar la solicitud</p>')
                        .show();
                }
            });
        }
    });

    // Eliminar centro
    $(document).on('click', '.eliminar-centro', function() {
        var centro = $(this).data('centro');
        var confirmar = confirm('¿Está seguro de eliminar el centro "' + centro + '"?');
        
        if (confirmar) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'eliminar_centro',
                    nonce: users1001_vars.nonce,
                    centro: centro
                },
                success: function(response) {
                    if (response.success) {
                        $('#mensaje-centro')
                            .removeClass('notice-error')
                            .addClass('notice-success')
                            .html('<p>' + response.data.message + '</p>')
                            .show();
                        
                        actualizarTablaCentros(response.data.centros);
                    } else {
                        $('#mensaje-centro')
                            .removeClass('notice-success')
                            .addClass('notice-error')
                            .html('<p>' + response.data + '</p>')
                            .show();
                    }
                },
                error: function() {
                    $('#mensaje-centro')
                        .removeClass('notice-success')
                        .addClass('notice-error')
                        .html('<p>Error al procesar la solicitud</p>')
                        .show();
                }
            });
        }
    });

    // Actualizar tabla de centros
    function actualizarTablaCentros(centros) {
        var html = '';
        
        if (centros.length > 0) {
            $.each(centros, function(index, centro) {
                html += '<tr>' +
                    '<td>' + centro + '</td>' +
                    '<td class="text-center">' +
                    '<button type="button" class="btn btn-error btn-sm eliminar-centro" data-centro="' + centro + '">Eliminar</button>' +
                    '</td>' +
                    '</tr>';
            });
        } else {
            html = '<tr><td colspan="2">No hay centros disponibles.</td></tr>';
        }
        
        $('#lista-centros').html(html);
    }
});
</script>
