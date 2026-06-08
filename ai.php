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

    // sample msg return block start  
    if(GEMINI_SAMPLE_RESPONSE){
        if(GEMINI_SAMPLE_RESPONSE_SUCCESS) {
            return aiResponse(true, 'Success msg from gemini sample', $provider.' sample', $model);
        } else {
            return aiResponse(false, 'Error msg from gemini sample', $provider.' sample', $model);
        }
    }
    // sample msg return block end


    $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key=' . GEMINI_API_KEY;
    $payload = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);

    // print_r($response); die;

    if (curl_errno($ch)) {
        return aiResponse(false, curl_error($ch), $provider, $model);
    }

    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['error'])) {
        return aiResponse(false, $result['error']['message'], $provider, $model);
    }

    if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $message = 'No response received from Gemini.';
        return aiResponse(false, $message, $provider, $model);
    }

    $message = $result['candidates'][0]['content']['parts'][0]['text'];
    return aiResponse(true, $message, $provider, $model);
}

function askLocalLlm($prompt)
{
    $provider = 'ollama';
    $model = OLLAMA_MODEL;

    $payload = [
        'model'  => $model,
        'prompt' => $prompt,
        'stream' => false
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => 'http://localhost:11434/api/generate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload)
    ]);

    $response = curl_exec($ch);

    curl_close($ch);

    $data = json_decode($response, true);

    if ($response === false) {
        $message = 'Failed to connect to Ollama: ' . curl_error($ch);
        return aiResponse(false, $message, $provider, $model);
    }
    
    if (isset($data['error'])) {
        return aiResponse(false, $data['error'], $provider, $model);
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode != 200) {
        return aiResponse(false, $response, $provider, $model);
    }

    if (empty($data['response'])) {
        $message = 'Empty response from Ollama';
        return aiResponse(false, $message, $provider, $model);
    }

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
