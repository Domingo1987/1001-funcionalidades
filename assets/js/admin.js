(function($) {
    'use strict';

    $(document).ready(function() {

        // 🔹 Ocultar notificaciones generales
        setTimeout(function() {
            $('.notice').fadeOut('slow');
        }, 5000);

        // 🔸 Sección: Gestión de Usuarios (Historial Académico)
        if ($('body').hasClass('users_page_users1001-usuarios')) {

            // Seleccionar todos los checkboxes
            $('#select-all').on('change', function() {
                $('.user-checkbox').prop('checked', this.checked);
            });

            // Enviar formulario
            $('#form-asignar-historial').on('submit', function(e) {
                e.preventDefault();

                const userIds = $('.user-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                const curso = $('#curso').val();
                const centro = $('#centro').val();
                const anio = $('#anio').val();
                const nonce = $('#asignar_historial_nonce').val();

                if (userIds.length === 0 || !curso || !centro || !anio) {
                    alert('Completa todos los campos y selecciona al menos un usuario.');
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
                        const mensaje = $('#mensaje-historial');
                        if (response.success) {
                            mensaje.removeClass('error').addClass('updated').html('<p>' + response.data + '</p>').fadeIn();
                        } else {
                            mensaje.removeClass('updated').addClass('error').html('<p>' + response.data + '</p>').fadeIn();
                        }
                    },
                    error: function() {
                        $('#mensaje-historial').removeClass('updated').addClass('error').html('<p>Error al procesar la solicitud.</p>').fadeIn();
                    }
                });
            });
        }

        // 🔸 Sección: Gestión de Cursos
        if ($('body').hasClass('toplevel_page_users1001-cursos')) {

            // Agregar nuevo curso
            $('#agregar-curso-form').on('submit', function(e) {
                e.preventDefault();
                const cursoProp = $('#nuevo-curso').val();

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
                            mostrarMensajeCurso(response);
                            if (response.success) {
                                $('#nuevo-curso').val('');
                                actualizarTablaCursos(response.data.cursos);
                            }
                        },
                        error: function() {
                            mostrarMensajeCurso({ success: false, data: 'Error al procesar la solicitud.' });
                        }
                    });
                }
            });

            // Eliminar curso
            $(document).on('click', '.eliminar-curso', function() {
                const curso = $(this).data('curso');
                if (!confirm(`¿Está seguro de eliminar el curso "${curso}"?`)) return;

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'eliminar_curso',
                        nonce: users1001_vars.nonce,
                        curso: curso
                    },
                    success: function(response) {
                        mostrarMensajeCurso(response);
                        if (response.success) {
                            actualizarTablaCursos(response.data.cursos);
                        }
                    },
                    error: function() {
                        mostrarMensajeCurso({ success: false, data: 'Error al procesar la solicitud.' });
                    }
                });
            });

            // Helpers
            function mostrarMensajeCurso(response) {
                const mensaje = $('#mensaje-curso');
                mensaje.removeClass('notice-error notice-success')
                    .addClass(response.success ? 'notice-success' : 'notice-error')
                    .html('<p>' + response.data.message || response.data + '</p>')
                    .show();
            }

            function actualizarTablaCursos(cursos) {
                let html = '';
                if (cursos.length > 0) {
                    $.each(cursos, function(index, curso) {
                        html += `<tr>
                            <td>${curso}</td>
                            <td><button class="btn btn-error btn-sm eliminar-curso" data-curso="${curso}">Eliminar</button></td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="2">No hay cursos disponibles.</td></tr>';
                }
                $('#lista-cursos').html(html);
            }
        }

        // 📦 Futuras secciones pueden agregarse igual con:
        // if ($('body').hasClass('toplevel_page_loquesea')) { ... }
        // 🔸 Sección: Gestión de Centros
        if ($('body').hasClass('toplevel_page_users1001-centros')) {

            // Agregar nuevo centro
            $('#agregar-centro-form').on('submit', function(e) {
                e.preventDefault();

                const centroProp = $('#nuevo-centro').val();

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
                            mostrarMensajeCentro(response);
                            if (response.success) {
                                $('#nuevo-centro').val('');
                                actualizarTablaCentros(response.data.centros);
                            }
                        },
                        error: function() {
                            mostrarMensajeCentro({ success: false, data: 'Error al procesar la solicitud.' });
                        }
                    });
                }
            });

            // Eliminar centro
            $(document).on('click', '.eliminar-centro', function() {
                const centro = $(this).data('centro');
                const confirmar = confirm(`¿Está seguro de eliminar el centro "${centro}"?`);
                if (!confirmar) return;

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'eliminar_centro',
                        nonce: users1001_vars.nonce,
                        centro: centro
                    },
                    success: function(response) {
                        mostrarMensajeCentro(response);
                        if (response.success) {
                            actualizarTablaCentros(response.data.centros);
                        }
                    },
                    error: function() {
                        mostrarMensajeCentro({ success: false, data: 'Error al procesar la solicitud.' });
                    }
                });
            });

            // Helpers
            function mostrarMensajeCentro(response) {
                const mensaje = $('#mensaje-centro');
                mensaje.removeClass('notice-error notice-success')
                    .addClass(response.success ? 'notice-success' : 'notice-error')
                    .html('<p>' + (response.data.message || response.data) + '</p>')
                    .show();
            }

            function actualizarTablaCentros(centros) {
                let html = '';
                if (centros.length > 0) {
                    $.each(centros, function(index, centro) {
                        html += `<tr>
                            <td>${centro}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-error btn-sm eliminar-centro" data-centro="${centro}">Eliminar</button>
                            </td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="2">No hay centros disponibles.</td></tr>';
                }
                $('#lista-centros').html(html);
            }
        }


    });

})(jQuery);
