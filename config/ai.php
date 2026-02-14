<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Models
    |--------------------------------------------------------------------------
    |
    | Here you may define the AI models available for generation and their
    | respective providers. The structure is grouped by provider, then
    | key-value pairs of model ID and model name.
    |
    */

    'models' => [
        'gemini' => [
            'gemini-3-flash-preview' => 'Gemini 3 Flash Preview',
        ],
        'sumopod' => [
            'deepseek-v3-2-251201' => 'DeepSeek V3',
            'glm-4-7-251222' => 'GLM-4',
            'kimi-k2-250905' => 'Kimi K2',
            'kimi-k2-thinking-251104' => 'Kimi K2 Thinking',
            'seed-1-8-251228' => 'Seed 1.8',
        ],
    ],

];
