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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
    ],

    // Formulário "Entre em Contato" da landing page: e-mail da equipe que
    // recebe uma cópia de cada solicitação (vazio = não envia cópia).
    'contato' => [
        'email_equipe' => env('CONTATO_EMAIL_EQUIPE'),
    ],

    // WhatsApp Cloud API (Meta) — opcional. Sem token/phone_id o envio por
    // WhatsApp simplesmente não acontece (o e-mail continua funcionando).
    // A Meta só permite iniciar conversa com um template aprovado; o
    // template deve ter 1 variável no corpo ({{1}} = primeiro nome).
    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'phone_id' => env('WHATSAPP_PHONE_ID'),
        'template' => env('WHATSAPP_TEMPLATE', 'confirmacao_demonstracao'),
        'idioma' => env('WHATSAPP_TEMPLATE_IDIOMA', 'pt_BR'),
    ],

];
