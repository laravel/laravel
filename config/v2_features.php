<?php

return [
    // إعدادات ميزات النسخة 2.0
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
        'test_mode' => true,
        'gateways' => [
            'mada' => false,
            'visa' => false,
            'mastercard' => false,
            'apple_pay' => false,
            'google_pay' => false,
        ],
    ],
    
    'ai_features' => [
        'enabled' => false,
        'virtual_assistant' => false,
        'recommendations' => false,
        'smart_pricing' => false,
        'customer_analysis' => false,
    ],
    
    'mobile_apps' => [
        'enabled' => false,
        'api_version' => '1.0',
        'push_notifications' => false,
    ],
];
