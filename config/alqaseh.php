<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Al-Qaseh API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for the Al-Qaseh payment gateway.
    |
    */

    // API Key provided by Al-Qaseh
    'api_key' => env('ALQASEH_API_KEY'),

    // Merchant ID provided by Al-Qaseh
    'merchant_id' => env('ALQASEH_MERCHANT_ID'),

    // Base URL for API requests
    'base_url' => env('ALQASEH_BASE_URL', 'https://api.alqaseh.com/v1'),

    // Enable sandbox mode for testing (default: true)
    'sandbox' => env('ALQASEH_SANDBOX', true),

    // Sandbox credentials
    'sandbox_credentials' => [
        'api_key' => '1X6Bvq65kpx1Yes5fYA5mbm8ixiexONo',
        'merchant_id' => 'public_test',
        'base_url' => 'https://api-test.alqaseh.com/v1',
    ],

];