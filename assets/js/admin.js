(function($) {
    'use strict';

    $(document).ready(function() {
        // Ocultar mensajes generales
        setTimeout(function() {
            $('.notice').fadeOut('slow');
        }, 5000);

        // 📘 Gestión de Cursos
        if ($('body').hasClass('toplevel_page_users1001-cursos')) {
            console.log('📘 JS activo: Gestión de Cursos');

            // Agregar nuevo curso
            $('#agregar-curso-form').on('submit', function(e) {
                e.preventDefault();

                const cursoProp = $('#nuevo-curso').val();
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
                            mostrarMensajeCurso(response);

                            if (response.success) {
                                $('#nuevo-curso').val('');
                                actualizarTablaCursos(response.data.cursos);
                            }
                        },
                        error: function(err) {
                            console.error('🚨 Error AJAX guardar curso:', err);
                            mostrarMensajeCurso({ success: false, data: 'Error al procesar la solicitud.' });
                        }
                    });
                }
            });

            // Eliminar curso
            $(document).on('click', '.eliminar-curso', function() {
                const curso = $(this).data('curso');
                console.log('🧨 Clic en eliminar curso:', curso);

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
                        console.log('✅ Respuesta eliminar curso:', response);
                        mostrarMensajeCurso(response);

                        if (response.success) {
                            actualizarTablaCursos(response.data.cursos);
                        }
                    },
                    error: function(err) {
                        console.error('🚨 Error AJAX eliminar curso:', err);
                        mostrarMensajeCurso({ success: false, data: 'Error al procesar la solicitud.' });
                    }
                });
            });

            // Helpers
            function mostrarMensajeCurso(response) {
                const mensaje = $('#mensaje-curso');
                mensaje.removeClass('notice-error notice-success')
                    .addClass(response.success ? 'notice-success' : 'notice-error')
                    .html('<p>' + (response.data.message || response.data) + '</p>')
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
    });

})(jQuery);
