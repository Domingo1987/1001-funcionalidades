<?php
// Acción AJAX para enviar prompt simple a OpenAI (no Assistant)

add_action('wp_ajax_openai_request', 'handle_openai_request');

function handle_openai_request() {
    $api_key = defined('OPENAI_API_KEY_SIMPLE') ? OPENAI_API_KEY_SIMPLE : '';

    if (empty($api_key)) {
        wp_send_json_error('API key is missing', 400);
        return;
    }

    $prompt = $_POST['prompt'] ?? '';
    $max_tokens = $_POST['max_tokens'] ?? 100;

    $response = wp_remote_post('https://api.openai.com/v1/engines/davinci/completions', array(
        'headers' => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        ),
        'body' => json_encode([
            'prompt'     => $prompt,
            'max_tokens' => (int) $max_tokens
        ])
    ));

    $body = wp_remote_retrieve_body($response);
    wp_send_json(json_decode($body, true)); // Devuelve JSON real
}