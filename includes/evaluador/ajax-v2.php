<?php

// Endpoint REST para evaluar el problema con IA (versión con mejoras internas)
add_action('rest_api_init', function () {
    register_rest_route('evaluador/v1', '/evaluar', array(
        'methods' => 'POST',
        'callback' => 'evaluar_problema',
        'permission_callback' => '__return_true',
    ));
});

function evaluar_problema(WP_REST_Request $request) {
    $problema = sanitize_textarea_field($request->get_param('problema'));
    $solucion = sanitize_textarea_field($request->get_param('solucion'));
    $user_id = sanitize_text_field($request->get_param('user_id3'));
    $problema_id = intval($request->get_param('problema_id'));

    if (empty($user_id) || empty($problema_id)) {
        return new WP_REST_Response(['error' => 'Faltan datos obligatorios.'], 400);
    }

    // Generar mensaje con contexto enriquecido
    $user_message = construir_mensaje_v2($problema, $solucion, $user_id, $problema_id);

    // Usar un assistant mejorado (con ID específico para este flujo)
    $assistant_response = getChatGPTResponse($user_message, $user_id, 'asst_v1JSBmPrzHS7bR4WDXbSF9J0'); // Reemplazar ID
    write_log("🧪 Assistant devuelve: " . $assistant_response);


    if (!$assistant_response) {
        return new WP_REST_Response(['error' => 'No se recibió respuesta del asistente.'], 500);
    }

    $assistant_response_data = json_decode($assistant_response, true);

    if ($assistant_response_data) {
        $total_puntos = $assistant_response_data['total_puntos'] ?? null;
        $criterios = $assistant_response_data['criterios'] ?? [];

        // Reutiliza misma función de guardado
        $evaluacion_id = guardar_evaluacion_en_bd_v2($problema, $solucion, $total_puntos, $user_id, $criterios, $problema_id);

        return new WP_REST_Response($assistant_response_data, 200);
    } else {
        write_log('Error al decodificar la respuesta JSON del asistente.');
        return new WP_REST_Response(['error' => 'La respuesta del asistente no es un JSON válido.'], 500);
    }
}
