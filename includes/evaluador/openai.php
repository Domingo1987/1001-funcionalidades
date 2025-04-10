<?php
// Función principal para gestionar el flujo completo con OpenAI
function getChatGPTResponse($message, $user_id3) {
    $api_key = OPENAI_API_KEY;
    $assistant_id = 'asst_v1JSBmPrzHS7bR4WDXbSF9J0';
    $api_url = 'https://api.openai.com/v1';

    $thread_res = get_user_meta($user_id3, 'thread_res', true);

    if (!$thread_res) {
        $thread_res = CreateThread($api_key, "$api_url/threads");
        if (!$thread_res) return false;
        update_user_meta($user_id3, 'thread_res', $thread_res);
    }

    $message_url = "$api_url/threads/$thread_res/messages";
    $add_msg_res = addMessageToThread($api_key, $message, $message_url);
    if (!$add_msg_res) return false;

    $run_url = "$api_url/threads/$thread_res/runs";
    $run_id = runMessageThread($api_key, ['assistant_id' => $assistant_id], $run_url);
    if (!$run_id) return false;

    $response_url = "$api_url/threads/$thread_res/messages";
    $response = getChatGPTMessage($api_key, $response_url, $add_msg_res);
    if (empty($response)) {
        // Reintento
        $response = getChatGPTMessage($api_key, $response_url, $add_msg_res);
    }

    return $response;
}

// Crear un hilo nuevo
function CreateThread($api_key, $url) {
    $response = curlAPIPost($api_key, $url);
    return $response['id'] ?? false;
}

// Agregar un mensaje al hilo
function addMessageToThread($api_key, $message, $url) {
    $data = ['role' => 'user', 'content' => $message];
    $response = curlAPIPost($api_key, $url, $data);
    return $response['id'] ?? false;
}

// Ejecutar el asistente en el hilo
function runMessageThread($api_key, $data, $url) {
    $response = curlAPIPost($api_key, $url, $data);
    return $response['id'] ?? false;
}

// Obtener la respuesta del asistente
function getChatGPTMessage($api_key, $url, $lastUserMessageId) {
    $maxAttempts = 5;
    $attempts = 0;
    $foundResponse = false;
    $assistantResponse = "";
    $userMessageTimestamp = null;

    while (!$foundResponse && $attempts < $maxAttempts) {
        sleep(5);
        $messages = getAssResponseMessages($api_key, $url);
        $data = json_decode($messages, true);

        if (!$userMessageTimestamp) {
            foreach ($data['data'] as $msg) {
                if ($msg['id'] === $lastUserMessageId) {
                    $userMessageTimestamp = $msg['created_at'];
                    break;
                }
            }
        }

        if ($userMessageTimestamp) {
            foreach ($data['data'] as $msg) {
                if ($msg['role'] === 'assistant' && $msg['created_at'] > $userMessageTimestamp) {
                    $assistantResponse = $msg['content'][0]['text']['value'] ?? "";
                    if (!empty($assistantResponse)) break;
                }
            }
        }

        $foundResponse = !empty($assistantResponse);
        $attempts++;
    }

    return $assistantResponse;
}

function getAssResponseMessages($api_key, $url) {
    return getCurlCall($api_key, $url);
}

// Llamadas API POST (JSON)
function curlAPIPost($api_key, $url, $data = null) {
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
        'OpenAI-Beta: assistants=v2',
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    if ($data) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($err || $httpcode >= 400) {
        write_log("API Error: $httpcode - $err - $response");
        return false;
    }

    return json_decode($response, true);
}

// Llamadas API GET
function getCurlCall($api_key, $url) {
    $headers = [
        'Authorization: Bearer ' . $api_key,
        'OpenAI-Beta: assistants=v2',
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 60,
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    return $err ? false : $response;
}
