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
            'deepseek-v3-2-free' => 'DeepSeek V3',
            'seed-1-8-free' => 'Seed 1.8',
        ],
        'qwen' => [
            'qwen3.5-plus' => 'Qwen 3.5 Plus',
            'qwen3-max' => 'Qwen 3 Max',
        ],
        'cloudflare' => [
            '@cf/zai-org/glm-4.7-flash' => 'GLM-4 9B',
            '@cf/openai/gpt-oss-20b' => 'GPT-OSS 20B',
            '@cf/meta/llama-3.2-3b-instruct' => 'Llama 3.2 3B',
        ],
    ],

];
