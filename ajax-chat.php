<?php

header('Content-Type: application/json');

require_once 'vendor/autoload.php';

require_once 'chat.php';
require_once 'ai.php';

// for showing parsed output
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);

$answer = ''; $error = '';
$conversationId = (int) ($_POST['conversation_id'] ?? 0);

if (!$conversationId) {
    echo json_encode([
        'success' => false,
        'message' => 'Conversation not selected.'
    ]);
    exit;
}

$prompt = trim($_POST['prompt'] ?? '');

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

// few recent chats for context building
$recentChats = getRecentChats($conversationId, 5);
$context = buildContext($recentChats);

$fullPrompt =
    SYSTEM_PROMPT
    . "\n\n"
    . $context
    . "\n\nActual User Message:\n"
    . $prompt
    . "\n\nAssistant:";

$startTime = microtime(true);
$answer = askAi($fullPrompt);
$timeTaken = round(microtime(true) - $startTime, 2);

if($answer['success']) {

    // save the chat if success
    $currentChatId = saveChat(
        $conversationId,
        $prompt,
        $answer['message'],
        $answer['provider'],
        $answer['model'],
        $timeTaken
    );

    // update conversation updated_at field to get the latest conversations
    updateConversation(
        $conversationId,
        [
            'updated_at' => date('Y-m-d H:i:s')
        ]
    );

    // update conversation title first time
    $conversation = getConversation($conversationId);
    if (str_starts_with($conversation['title'], 'New Chat')) {
        updateConversation(
            $conversationId,
            [
                'title' => mb_substr(trim($prompt), 0, 50)
            ]
        );
    }

}

$answer['html'] = $parsedown->text($answer['message']);
$answer['time_taken'] = $timeTaken;
$answer['prompt'] = $prompt;
$answer['fullPrompt'] = $fullPrompt;

echo json_encode($answer); die;