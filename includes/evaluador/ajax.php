<?php

// Endpoint REST para evaluar el problema con IA
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

    if (empty($user_id)) {
        return new WP_REST_Response(['error' => 'User ID no enviado.'], 400);
    }

    $user_message = "Nombre: $user_id\nLetra del problema: $problema\nSolución:\n$solucion\nPor favor, proporciona la evaluación en formato JSON siguiendo la estructura especificada.";

    $assistant_response = getChatGPTResponse($user_message, $user_id);

    if (!$assistant_response) {
        return new WP_REST_Response(['error' => 'No se recibió respuesta del asistente.'], 500);
    }

    $assistant_response_data = json_decode($assistant_response, true);

    if ($assistant_response_data) {
        $total_puntos = $assistant_response_data['total_puntos'];
        $criterios = $assistant_response_data['criterios'];

        $evaluacion_id = guardar_evaluacion_en_bd($problema, $solucion, $total_puntos, $user_id, $criterios);

        return new WP_REST_Response($assistant_response_data, 200);
    } else {
        write_log('Error al decodificar la respuesta JSON del asistente.');
        return new WP_REST_Response(['error' => 'La respuesta del asistente no es un JSON válido.'], 500);
    }
}
