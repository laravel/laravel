<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ميزات الإصدار 2.0
    |--------------------------------------------------------------------------
    |
    | هذا الملف يحدد حالة تفعيل ميزات الإصدار 2.0 من النظام
    | يمكنك تفعيل أو تعطيل أي من هذه الميزات حسب الحاجة
    |
    */

    'multilingual' => [
        'enabled' => false,
        'available_locales' => ['ar', 'en', 'fr', 'tr'],
        'default_locale' => 'ar',
    ],

    'dark_mode' => [
        'enabled' => false,
        'default' => 'light', // 'light', 'dark', 'system'
    ],

    'payment_system' => [
        'enabled' => false,
        'providers' => [
            'mada' => [
                'enabled' => false,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('MADA_MERCHANT_ID', ''),
                    'api_key' => env('MADA_API_KEY', ''),
                    'secret_key' => env('MADA_SECRET_KEY', ''),
                ],
            ],
            'visa' => [
                'enabled' => false,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('VISA_MERCHANT_ID', ''),
                    'api_key' => env('VISA_API_KEY', ''),
                ],
            ],
            'mastercard' => [
                'enabled' => false,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('MASTERCARD_MERCHANT_ID', ''),
                    'api_key' => env('MASTERCARD_API_KEY', ''),
                ],
            ],
            'apple_pay' => [
                'enabled' => false,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('APPLE_PAY_MERCHANT_ID', ''),
                    'certificate_path' => env('APPLE_PAY_CERTIFICATE_PATH', ''),
                ],
            ],
            'google_pay' => [
                'enabled' => false,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('GOOGLE_PAY_MERCHANT_ID', ''),
                    'api_key' => env('GOOGLE_PAY_API_KEY', ''),
                ],
            ],
        ],
    ],

    'enhanced_ui' => [
        'enabled' => true,
    ],
];
