const form = document.getElementById('chat-form');
const submitBtn = document.getElementById('submit-btn');
const loadingMessage = document.getElementById('loading-message');
const responseContainer = document.getElementById('response-container');
const historyContainer = document.getElementById('chat-history-container');
const noHistoryMessage = document.getElementById('no-history-message');
const promptInput = document.getElementById('prompt');

let isProcessing = false;


if (form) {

    form.addEventListener('submit', async (event) => {

        event.preventDefault();

        if (isProcessing) return;
        isProcessing = true;

        responseContainer.innerHTML = '';
        submitBtn.disabled = true;
        submitBtn.textContent = 'Thinking...';
        loadingMessage.style.display = 'block';

        // thinking...
        const promptText = promptInput.value.trim();
        const tempId = 'temp-' + Date.now();
        const timestamp = new Date().toLocaleString();

        const thinkingCard = createChatCard(
            promptText,
            '<em>Thinking...</em>',
            timestamp,
            tempId
        );
        historyContainer.insertAdjacentHTML(
            'afterbegin',
            thinkingCard
        );
        const tempCard = document.getElementById(tempId);

        try {

            const formData = new FormData(form);

            const response = await fetch(
                'ajax-chat.php',
                {
                    method: 'POST',
                    body: formData
                }
            );

            const data = await response.json();
            console.log(data);

            if (data.success) {

                if (data.success) {

                    if (noHistoryMessage) {
                        noHistoryMessage.remove();
                    }

                    tempCard.querySelector('.answer-content').innerHTML = data.html;
                    tempCard.querySelector('.chat-meta').textContent = `${data.provider} | ${data.model} | ${data.timestamp}`;
                    
                    promptInput.value = '';
                    promptInput.focus();

                }

            } else {
                tempCard.querySelector('.answer-content').innerHTML = `<div class="error-message">${data.message}</div>`;
                tempCard.querySelector('.chat-meta').textContent = `${data.provider} | ${data.model} | ${data.timestamp}`;
            }

        } catch (error) {

            console.error(error);
            tempCard.querySelector('.answer-content').innerHTML = `<div class="error-message">Something went wrong.</div>`;
            tempCard.querySelector('.chat-meta').textContent = `${data.provider} | ${data.model} | ${data.timestamp}`;

        } finally {

            loadingMessage.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Ask AI';
            isProcessing = false;

        }

    });

    promptInput.addEventListener(
        'keydown',
        function(event)
        {
            if (
                event.key === 'Enter'
                &&
                !event.shiftKey
            ) {

                event.preventDefault();

                form.requestSubmit();

            }
        }
    );

}


document.addEventListener('click', async event => {

    const button = event.target;

    if (!button.matches('.rename-btn, .delete-btn')) return;

    const action = button.classList.contains('rename-btn')
        ? 'rename'
        : 'delete';

    const conversationId = button.dataset.id;

    const formData = new FormData();

    formData.append('action', action);
    formData.append('conversation_id', conversationId);

    if (action === 'rename') {

        const title = prompt('Enter new title');

        if (!title) return;

        formData.append('title', title.trim());
    }

    if (
        action === 'delete' &&
        !confirm('Delete this conversation?')
    ) {
        return;
    }

    try {

        const response = await fetch(
            'conversation-action.php',
            {
                method: 'POST',
                body: formData
            }
        );

        if (!response.ok) {
            throw new Error(
                `HTTP Error: ${response.status}`
            );
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(
                data.message || 'Unknown error.'
            );
        }

        if (action === 'delete') {
            location.href = 'index.php';
            return;
        } else {
            location.reload();
            return;
        }

    } catch (error) {

        console.error(error);

        alert(
            error.message ||
            'Something went wrong.'
        );
    }

});


function createChatCard(prompt, answer, timestamp, cardId = '')
{
    return `
        <div id="${cardId}">

            <div class="message-row user-message">
                <div class="message-bubble">
                    ${escapeHtml(prompt)}
                </div>
            </div>

            <div class="message-row assistant-message">
                <div class="message-bubble">

                    <div class="answer-content">
                        ${answer}
                    </div>

                    <div class="chat-meta">
                        ${timestamp}
                    </div>

                </div>
            </div>

        </div>
    `;
}


function escapeHtml(text)
{
    const div = document.createElement('div');

    div.textContent = text;

    return div.innerHTML;
}