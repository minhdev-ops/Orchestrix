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

    'waqi' => [
        'token' => env('WAQI'),
        'base_url' => 'https://api.waqi.info',
    ],

    'ghn' => [
        'token' => env('GHN_TOKEN'),
        'shop_id' => env('GHN_SHOP_ID'),
        'base_url' => env('GHN_BASE_URL', 'https://dev-online-gateway.ghn.vn/api/v2'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    ],

    'facebook' => [
        'app_id' => env('FACEBOOK_APP_ID'),
        'app_secret' => env('FACEBOOK_APP_SECRET'),
    ],

    'captcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

    'ai_plant_doctor' => [
        'provider' => env('AI_PLANT_DOCTOR_PROVIDER', 'gemini'),
        'gemini_api_key' => env('AI_PLANT_DOCTOR_GEMINI_API_KEY'),
        'gemini_model' => env('AI_PLANT_DOCTOR_GEMINI_MODEL', 'gemini-2.0-flash'),
        'openai_api_key' => env('AI_PLANT_DOCTOR_OPENAI_API_KEY'),
        'openai_model' => env('AI_PLANT_DOCTOR_OPENAI_MODEL', 'gpt-4o'),
    ],

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'credentials' => env('FIREBASE_CREDENTIALS', ''),
    ],
];
