<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    | This provider is used when no provider is explicitly requested.
    */
    'default' => env('AI_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | AI Provider Configurations
    |--------------------------------------------------------------------------
    */
    'providers' => [

        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model'   => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        ],

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model'   => env('GEMINI_MODEL', 'gemini-2.0-flash'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1'),
        ],

        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'model'   => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
            'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
        ],

    ],

];
