<?php

return [
    'provider' => env('AI_PROVIDER', 'none'),
    'api_key' => env('AI_API_KEY'),
    'model' => env('AI_MODEL', 'openai/gpt-oss-20b'),
    'timeout' => (int) env('AI_TIMEOUT', 20),
    'max_message_length' => (int) env('AI_MAX_MESSAGE_LENGTH', 1000),
];