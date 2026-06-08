<?php

require_once 'db.php';

function saveChat($question, $answer, $provider, $model, $time_taken)
{
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO chat_history
        (
            question,
            answer,
            provider,
            model,
            time_taken
        )
        VALUES
        (
            :question,
            :answer,
            :provider,
            :model,
            :time_taken
        )
    ");

    $stmt->execute([
        'question' => $question,
        'answer'   => $answer,
        'provider'   => $provider,
        'model'   => $model,
        'time_taken'   => $time_taken
    ]);

    return $pdo->lastInsertId();
}

function getChats($limit = 20, $excludeId = null)
{
    global $pdo;

    $sql = "
        SELECT *
        FROM chat_history
    ";

    if ($excludeId !== null) {
        $sql .= " WHERE id != :excludeId ";
    }

    $sql .= "
        ORDER BY id DESC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);

    if ($excludeId !== null) {
        $stmt->bindValue(
            ':excludeId',
            $excludeId,
            PDO::PARAM_INT
        );
    }

    $stmt->bindValue(
        ':limit',
        $limit,
        PDO::PARAM_INT
    );

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRecentChats($limit = 5)
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT *
        FROM chat_history
        ORDER BY id DESC
        LIMIT :limit
    ");

    $stmt->bindValue(
        ':limit',
        $limit,
        PDO::PARAM_INT
    );

    $stmt->execute();

    return array_reverse(
        $stmt->fetchAll(PDO::FETCH_ASSOC)
    );
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