<?php

require_once 'chat.php';

header('Content-Type: application/json');

try {

    $action = $_POST['action'] ?? '';
    $conversationId = (int) ($_POST['conversation_id'] ?? 0);

    if (!$conversationId) {
        throw new Exception('Invalid conversation.');
    }

    switch ($action) {

        case 'rename':

            $title = trim($_POST['title'] ?? '');

            if ($title === '') {
                throw new Exception('Title cannot be empty.');
            }

            updateConversation(
                $conversationId,
                [
                    'title' => $title
                ]
            );

            break;

        case 'delete':

            updateConversation(
                $conversationId,
                [
                    'deleted_at' => date('Y-m-d H:i:s')
                ]
            );

            break;

        default:

            throw new Exception('Invalid action.');
    }

    echo json_encode([
        'success' => true
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

}