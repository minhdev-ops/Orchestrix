<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Default payment gateway: vnpay, momo, cod, banking
    |
    */
    'default' => env('PAYMENT_DEFAULT', 'cod'),

    /*
    |--------------------------------------------------------------------------
    | VNPay Configuration
    |--------------------------------------------------------------------------
    */
    'vnpay' => [
        'enabled' => env('VNPAY_ENABLED', false),
        'tmn_code' => env('VNPAY_TMN_CODE', ''),
        'hash_secret' => env('VNPAY_HASH_SECRET', ''),
        'payment_url' => env('VNPAY_PAYMENT_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL', '/payment/vnpay/callback'),
        'ipn_url' => env('VNPAY_IPN_URL', '/payment/vnpay/ipn'),
        'api_url' => env('VNPAY_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'),
    ],

    /*
    |--------------------------------------------------------------------------
    | MoMo Configuration
    |--------------------------------------------------------------------------
    */
    'momo' => [
        'enabled' => env('MOMO_ENABLED', false),
        'partner_code' => env('MOMO_PARTNER_CODE', ''),
        'access_key' => env('MOMO_ACCESS_KEY', ''),
        'secret_key' => env('MOMO_SECRET_KEY', ''),
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api'),
        'return_url' => env('MOMO_RETURN_URL', '/payment/momo/callback'),
        'ipn_url' => env('MOMO_IPN_URL', '/payment/momo/ipn'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Commission Rate (%)
    |--------------------------------------------------------------------------
    */
    'commission_rate' => env('COMMISSION_RATE', 5),

];
