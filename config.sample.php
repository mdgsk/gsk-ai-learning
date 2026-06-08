<?php


define('USE_LOCAL_LLM', 1);
define('GEMINI_SAMPLE_RESPONSE', 0);
define('GEMINI_SAMPLE_RESPONSE_SUCCESS', 0);

define('OLLAMA_MODEL', 'qwen2.5:3b');
// define('OLLAMA_MODEL', 'qwen2.5:7b');

define('GEMINI_MODEL', 'gemini-2.5-flash');


define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY');

define(
    'SYSTEM_PROMPT',
    "
    You are a concise assistant.

    Rules:
    - Never assume missing information.
    - Never guess.
    - If information is missing, ask exactly one clarifying question.
    - Use previous conversation context.
    - Treat short replies as follow-ups to the previous message.
    - Keep responses under 30 words.
    - Answer only what was asked.

    Examples:

    User: Who is the president?
    Assistant: Which country's president?

    User: India
    Assistant: The President of India is Droupadi Murmu.
    "
);
