<?php

require_once 'config.php';

function askAi($prompt)
{
    if(USE_LOCAL_LLM) {
        return askLocalLlm($prompt);
    }
    return askGemini($prompt);
}

function askGemini($prompt)
{
    $provider = 'gemini';
    $model = GEMINI_MODEL;

    if (GEMINI_SAMPLE_RESPONSE) {
        return GEMINI_SAMPLE_RESPONSE_SUCCESS
            ? aiResponse(true, 'Success msg from gemini sample', $provider.' sample', $model)
            : aiResponse(false, 'Error msg from gemini sample', $provider.' sample', $model);
    }

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.GEMINI_API_KEY;

    $payload = [
        'contents' => [[
            'parts' => [[
                'text' => $prompt
            ]]
        ]]
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) return aiResponse(false, curl_error($ch), $provider, $model);

    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['error'])) return aiResponse(false, $result['error']['message'], $provider, $model);

    if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return aiResponse(false, 'No response received from Gemini.', $provider, $model);
    }

    return aiResponse(
        true,
        $result['candidates'][0]['content']['parts'][0]['text'],
        $provider,
        $model
    );
}

function askLocalLlm($prompt)
{
    $provider = 'ollama';
    $model = OLLAMA_MODEL;

    $payload = [
        'model' => $model,
        'prompt' => $prompt,
        'stream' => false
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => 'http://localhost:11434/api/generate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($response === false) return aiResponse(false, 'Failed to connect to Ollama: '.$curlError, $provider, $model);

    $data = json_decode($response, true);

    if (isset($data['error'])) return aiResponse(false, $data['error'], $provider, $model);

    if ($httpCode != 200) return aiResponse(false, $response, $provider, $model);

    if (empty($data['response'])) return aiResponse(false, 'Empty response from Ollama', $provider, $model);

    return aiResponse(true, $data['response'], $provider, $model);
}


function aiResponse(bool $success, string $message, string $provider, string $model)
{
    return [
        'success' => $success,
        'message' => $message,
        'provider' => $provider,
        'model' => $model,
        'timestamp' => date('d M H:i')
    ];
}
