<?php

require_once 'vendor/autoload.php';
require_once 'chat.php';

if (isset($_GET['new_chat'])) {
    $conversationId = createConversation('New Chat '.date('H:i:s'));
    header("Location: ?conversation_id=".$conversationId);
    exit;
}

$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
$conversationId = getCurrentConversationId();

$conversations = getConversations();
$history = getChats($conversationId, 20, $currentChatId ?? null);

$cssVersion = filemtime('assets/css/style.css');
$jsVersion = filemtime('assets/js/app.js');

?>

<!DOCTYPE html>
<html>
<head>
    <title>Ask AI</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= $cssVersion ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github-dark.min.css">
</head>
<body>

<div class="container">


    <div class="app-layout">

        <div class="sidebar">

            <a href="?new_chat=1" class="new-chat-btn">+ New Chat</a>

            <h4>Chats</h4>
            <?php foreach ($conversations as $conversation): ?>
                <a href="?conversation_id=<?= $conversation['id'] ?>" class="conversation-link <?= $conversation['id'] == $conversationId ? 'active' : '' ?>">
                    <?= htmlspecialchars($conversation['title']) ?>
                </a>
            <?php endforeach; ?>

        </div>

        <div class="main-content">
        
            <div class="chat-form">

                <?php if ($conversationId): ?>
                    <h2>Ask AI</h2>

                    <form method="post" id="chat-form">
                        <input type="hidden" id="conversation_id" name="conversation_id" value="<?= $conversationId ?>">
                        <textarea id="prompt" name="prompt" placeholder="Ask something..." required minlength="2"
                        ></textarea>
                        <button type="submit" id="submit-btn">Ask AI</button>
                        <div id="loading-message" class="loading-message">Thinking...</div>
                    </form>

                <?php else: ?>

                    <div class="empty-state">
                        Select an existing chat or click New Chat to start.
                    </div>

                <?php endif; ?>

            </div>

            <div id="response-container"></div>

            <?php if ($conversationId): ?>    
            
            <h3 class="history-title">Chat History</h3>

                <div id="chat-history-container">
                <?php foreach ($history as $chat): ?>

                    <div class="message-row user-message">
                        <div class="message-bubble">
                            <?= nl2br(htmlspecialchars($chat['question'])) ?>
                        </div>
                    </div>

                    <div class="message-row assistant-message">
                        <div class="message-bubble">

                            <div class="answer-content">
                                <?= $parsedown->text($chat['answer']) ?>
                            </div>

                            <?php $chatMeta = "{$chat['provider']} | {$chat['model']} | " . date('d M H:i', strtotime($chat['created_at'])); ?>

                            <div class="chat-meta">
                                <?= htmlspecialchars($chatMeta) ?>
                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>
                </div>

                <?php if (empty($history)): ?>
                    <p id="no-history-message">No chat history available.</p>
                <?php endif; ?>
            <?php endif; ?>

        </div>

    </div>


</div>

<script src="assets/js/app.js?v=<?= $jsVersion ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>

<script>
hljs.highlightAll();
</script>

</body>
</html>