<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'telegram' => [
        'api_key'         => env('TELEGRAM_API_KEY'),
        'default_chat_id' => env('TELEGRAM_DEFAULT_CHAT_ID'),
    ],

    'anthropic' => [
        'api_key'         => env('ANTHROPIC_API_KEY'),
    ],

    'grok' => [
        'api_key'         => env('GROK_API_KEY'),
    ],

    'chatProvider' => [
        'class'         => env('AI_CHAT_PROVIDER', "gpt"),
    ],

    'payme' => [
        'api_key' => env('PAYME_API_KEY'),
        'url'     => env('PAYME_URL', 'https://live.payme.io'),
    ],
];
