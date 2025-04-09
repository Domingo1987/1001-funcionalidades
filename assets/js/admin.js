/**
 * JavaScript para la administración del plugin Users1001
 *
 * @package    Users1001
 * @author     Domingo Pérez
 */

(function($) {
    'use strict';

    /**
     * Todo el código de JavaScript específico para la administración 
     * se incluye en este archivo.
     */

    $(document).ready(function() {
        // Ocultar mensajes de notificación después de 5 segundos
        setTimeout(function() {
            $('.notice').fadeOut('slow');
        }, 5000);
    });

})(jQuery);
