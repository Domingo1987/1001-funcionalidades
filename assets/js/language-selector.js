jQuery(document).ready(function($) {
    // Manejar clics en los botones del selector
    $('.ls-button').click(function() {
        var lang = $(this).data('lang');
        
        // Actualizar botones activos
        $('.ls-button').removeClass('active');
        $(this).addClass('active');
        
        // Actualizar input oculto con el lenguaje seleccionado
        var lenguajeInput = $('#lenguaje_usado');
        if (lenguajeInput.length) {
            lenguajeInput.val(lang);
        }

        // Ocultar todos los contenidos
        $('.problem-content-original, .problem-content-python, .problem-content-java').hide();
        
        // Mostrar el contenido seleccionado
        if (lang === 'c') {
            $('.problem-content-original').show();
        } else if (lang === 'python') {
            $('.problem-content-python').show();
        } else if (lang === 'java') {
            $('.problem-content-java').show();
        }
    });
    
    // Eliminar texto duplicado si existe
    $('.post-content').contents().each(function() {
        if (this.nodeType === 3 && this.nodeValue.trim() === "C | Python | Java") {
            $(this).remove();
        }
    });
});


