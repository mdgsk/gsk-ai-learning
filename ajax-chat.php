<?php

header('Content-Type: application/json');

require_once 'vendor/autoload.php';

require_once 'chat.php';
require_once 'ai.php';

$parsedown = new Parsedown();
$parsedown->setSafeMode(true);

$answer = '';
$error = '';


$prompt = trim(
    $_POST['prompt'] ?? ''
);

if ($prompt === '') {

    echo json_encode([
        'success' => false,
        'error' => 'Please enter a question.'
    ]);

} elseif (strlen($prompt) < 2) {

    echo json_encode([
        'success' => false,
        'error' => 'Question must be at least 2 characters long.'
    ]);

    exit;
}

$recentChats = getRecentChats(5);

$context = buildContext(
    $recentChats
);

$fullPrompt =
    SYSTEM_PROMPT
    . "\n\n"
    . $context
    . "\nUser: "
    . $prompt;

// echo json_encode($fullPrompt); die;

// sleep(3);


$startTime = microtime(true);

$answer = askAi($fullPrompt);

$timeTaken = round(
    microtime(true) - $startTime,
    2
);

// print_r($answer); die;

if($answer['success']) {
    $currentChatId = saveChat(
        $prompt,
        $answer['message'],
        $answer['provider'],
        $answer['model'],
        $timeTaken
    );
}

$answer['html'] = $parsedown->text(
    $answer['message']
);

$answer['time_taken'] = $timeTaken;

// sending extra results
$answer['prompt'] = $prompt;
$answer['fullPrompt'] = $fullPrompt;

echo json_encode($answer); die;