<?php

require_once 'db.php';


function saveChat($conversationId, $question, $answer, $provider, $model, $timeTaken)
{
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO chat_history
        (conversation_id, question, answer, provider, model, time_taken)
        VALUES
        (:conversation_id, :question, :answer, :provider, :model, :time_taken)
    ");

    $stmt->execute([
        'conversation_id' => $conversationId,
        'question' => $question,
        'answer' => $answer,
        'provider' => $provider,
        'model' => $model,
        'time_taken' => $timeTaken
    ]);

    return $pdo->lastInsertId();
}


function getChats($conversationId = 1, $limit = 20, $excludeId = null)
{
    global $pdo;

    $sql = "SELECT * FROM chat_history WHERE conversation_id = :conversation_id";

    if ($excludeId !== null) $sql .= " AND id != :excludeId";

    $sql .= " ORDER BY id DESC LIMIT :limit";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':conversation_id', $conversationId, PDO::PARAM_INT);

    if ($excludeId !== null) $stmt->bindValue(':excludeId', $excludeId, PDO::PARAM_INT);

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getRecentChats($conversationId = 1, $limit = 5)
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT *
        FROM chat_history
        WHERE conversation_id = :conversation_id
        ORDER BY id DESC
        LIMIT :limit
    ");

    $stmt->bindValue(':conversation_id', $conversationId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
}


function buildContext(array $chats)
{
    $context = '';

    foreach ($chats as $chat) {

        $context .= "User: "
            . $chat['question']
            . PHP_EOL;

        $context .= "Assistant: "
            . $chat['answer']
            . PHP_EOL . PHP_EOL;
    }

    return $context;
}


function getCurrentConversationId()
{
    return (int) ($_GET['conversation_id'] ?? 0);
}


function getConversations()
{
    global $pdo;

    return $pdo->query("
        SELECT *
        FROM conversations
        ORDER BY id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}


function createConversation($title = 'New Chat')
{
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO conversations (title)
        VALUES (:title)
    ");

    $stmt->execute(['title' => $title]);

    return $pdo->lastInsertId();
}