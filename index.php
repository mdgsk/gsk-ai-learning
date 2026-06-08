<?php

require_once 'vendor/autoload.php';
require_once 'chat.php';

$parsedown = new Parsedown();
$parsedown->setSafeMode(true);

$history = getChats(
    20,
    $currentChatId ?? null
);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Ask AI</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime('assets/css/style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github-dark.min.css">
</head>
<body>

<div class="container">

    <h2>Ask AI</h2>

    <div class="chat-form">

        <form method="post" id="chat-form">

            <textarea
                id="prompt"    
                name="prompt"
                placeholder="Ask something..."
                required
                minlength="2"
            ></textarea>

            <button type="submit" id="submit-btn">
                Ask AI
            </button>

            <div
                id="loading-message"
                class="loading-message">
                Thinking...
            </div>

        </form>

    </div>

    <div id="response-container"></div>

        <h3 class="history-title">
            Chat History
        </h3>

        <div id="chat-history-container">

        
        <?php foreach ($history as $chat): ?>

            <div class="chat-card">

                <div class="question">
                    <strong>You: </strong>
                    <?= nl2br(htmlspecialchars($chat['question'])) ?>
                </div>

                <div class="answer">
                    <strong>Assistant: </strong>
                    <div class="answer-content">
                        <?= $parsedown->text($chat['answer']) ?>
                    </div>
                    <div class="chat-meta">
                        <?= $chat['provider'].' | '.$chat['model'].' | '.htmlspecialchars(date('d M H:i', strtotime($chat['created_at']))) ?>
                    </div>
                </div>             

            </div>

        <?php endforeach; ?>
        </div>

        <?php if (empty($history)): ?>
            <p id="no-history-message">
                No chat history available.
            </p>
        <?php endif; ?>

</div>

<script src="assets/js/app.js?v=<?= filemtime('assets/js/app.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>

<script>
hljs.highlightAll();
</script>

</body>
</html>