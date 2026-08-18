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

    'ghtk' => [
        'token' => env('GHTK_TOKEN'),
        'client_source' => env('GHTK_CLIENT_SOURCE'),
        'base_url' => env('GHTK_BASE_URL', 'https://services.giaohangtietkiem.vn'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL', '/auth/google/callback'),
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
        // Provider: 'gemini' | 'openai' | 'vit_local' (model local best_vit.keras)
        'provider' => env('AI_PLANT_DOCTOR_PROVIDER', 'gemini'),
        'gemini_api_key' => env('AI_PLANT_DOCTOR_GEMINI_API_KEY'),
        'gemini_model' => env('AI_PLANT_DOCTOR_GEMINI_MODEL', 'gemini-3.1-flash-lite'),
        'openai_api_key' => env('AI_PLANT_DOCTOR_OPENAI_API_KEY'),
        'openai_model' => env('AI_PLANT_DOCTOR_OPENAI_MODEL', 'gpt-4o'),

        // ViT local inference (microservice FastAPI, port 8501)
        'vit_service_url' => env('AI_PLANT_DOCTOR_VIT_SERVICE_URL', 'http://localhost:8501'),
        'vit_token' => env('AI_PLANT_DOCTOR_VIT_TOKEN', ''),
        // Tự động phát hiện lá (Grounding DINO) trước khi predict
        'vit_auto_detect' => (bool) env('AI_PLANT_DOCTOR_VIT_AUTO_DETECT', true),
        'vit_defaults' => [
            // Khi prediction confidence dưới ngưỡng này -> báo user chụp lại ảnh
            'min_confidence' => (float) env('AI_PLANT_DOCTOR_VIT_MIN_CONFIDENCE', 0.45),
            // Số top-k melting trả về
            'top_k' => (int) env('AI_PLANT_DOCTOR_VIT_TOP_K', 5),
            // Timeout gọi microservice (giây)
            'timeout' => (int) env('AI_PLANT_DOCTOR_VIT_TIMEOUT', 30),
        ],
        // Bổ sung mô tả/biện pháp từ cloud (gemini|openai|null) sau khi ViT đã chẩn đoán.
        // null = chỉ trả về kết quả ViT mà không bổ sung mô tả chi tiết.
        'info_provider' => env('AI_PLANT_DOCTOR_INFO_PROVIDER', 'gemini'),
    ],

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'credentials' => env('FIREBASE_CREDENTIALS', ''),
    ],

    'mailchimp_api_key' => env('MAILCHIMP_API_KEY', ''),
    'mailchimp_list_id' => env('MAILCHIMP_LIST_ID', ''),

    'pusher' => [
        'app_id' => env('PUSHER_APP_ID', ''),
        'app_key' => env('PUSHER_APP_KEY', ''),
        'app_secret' => env('PUSHER_APP_SECRET', ''),
        'app_cluster' => env('PUSHER_APP_CLUSTER', ''),
    ],
];
