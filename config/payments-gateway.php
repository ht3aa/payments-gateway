<?php

// config for Ht3aa/PaymentsGateway
return [
    /*
    |--------------------------------------------------------------------------
    | Switch Payment Gateway
    |--------------------------------------------------------------------------
    */
    'switch' => [
        'base_url' => 'https://eu-test.oppwa.com/v1',
        'resource_path_base_url' => 'https://eu-test.oppwa.com',
        'token' => env('SWITCH_TOKEN'),
        'entity_id' => env('SWITCH_ENTITY_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fib Payment Gateway
    |--------------------------------------------------------------------------
    */
    'fib' => [
        'test_base_url' => 'https://fib.stage.fib.iq',
        'production_base_url' => 'https://fib.prod.fib.iq',
        'is_production' => env('FIB_IS_PRODUCTION', false),
        'client_id' => env('FIB_CLIENT_ID'),
        'client_secret' => env('FIB_CLIENT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | ZainCash Payment Gateway
    |--------------------------------------------------------------------------
    */
    'zaincash' => [
        'is_production' => env('ZAIN_CASH_IS_PRODUCTION', false),
        'merchant_id' => env('ZAIN_CASH_MERCHANT_ID', '5ffacf6612b5777c6d44266f'),
        'merchant_secret' => env('ZAIN_CASH_MERCHANT_SECRET', '$2y$10$hBbAZo2GfSSvyqAyV2SaqOfYewgYpfR1O19gIh4SqyGWdmySZYPuS'),
        'msisdn' => env('ZAIN_CASH_MSISDN', '9647835077893'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Qi Card Payment Gateway
    |--------------------------------------------------------------------------
    */
    'qi_card' => [
        'api_url' => env('QI_CARD_API_URL', 'https://uat-sandbox-3ds-api.qi.iq/api/v1'),
        'terminal_id' => env('QI_CARD_TERMINAL_ID', '237984'),
        'username' => env('QI_CARD_USERNAME', 'paymentgatewaytest'),
        'password' => env('QI_CARD_PASSWORD', 'WHaNFE5C3qlChqNbAzH4'),
    ],
];
