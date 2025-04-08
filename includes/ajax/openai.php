<?php
// Archivo: openai.php

// Acción AJAX: enviar un prompt a la API de OpenAI y devolver la respuesta
function handle_openai_request() {
    $api_key = "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"; // Reemplazá con un sistema seguro si se publica

    if (empty($api_key)) {
        wp_send_json_error('API key is missing', 400);
        return;
    }

    $prompt = $_POST['prompt'] ?? '';
    $max_tokens = $_POST['max_tokens'] ?? 100;

    $response = wp_remote_post('https://api.openai.com/v1/engines/davinci/completions', array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        ),
        'body' => json_encode(array(
            'prompt' => $prompt,
            'max_tokens' => $max_tokens
        ))
    ));

    $body = wp_remote_retrieve_body($response);
    wp_send_json($body);
}
add_action('wp_ajax_openai_request', 'handle_openai_request');
