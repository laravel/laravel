#!/bin/bash

echo "إعداد النسخة 2.0 من نظام وكالات السفر RTLA"
echo "============================================"

# تأكيد أننا في المجلد الصحيح
if [ ! -f "artisan" ]; then
    echo "خطأ: يجب تشغيل هذا السكريبت من مجلد Laravel الرئيسي"
    exit 1
fi

# التأكد من تحديث الفرع الرئيسي
echo "جاري تحديث الفرع الرئيسي..."
git checkout main
git pull origin main

# إنشاء فرع للنسخة 2.0
echo "جاري إنشاء فرع للنسخة 2.0..."
git checkout -b version-2.0

# تحديث ملف composer.json للنسخة 2.0
echo "تحديث معلومات الإصدار..."
sed -i 's/"version": "1.0.0"/"version": "2.0.0-dev"/g' composer.json 2>/dev/null || 
  sed -i '' 's/"version": "1.0.0"/"version": "2.0.0-dev"/g' composer.json

# إنشاء ملف تكوين لميزات النسخة 2.0
echo "إنشاء ملف تكوين للنسخة 2.0..."
cat > config/v2_features.php << 'EOL'
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
EOL

# إضافة الملفات الجديدة للنسخة 2.0
echo "إضافة التغييرات إلى Git..."
git add .
git commit -m "بدء النسخة 2.0: إعداد الفرع وملفات التكوين الأساسية"

# طباعة إرشادات المتابعة
echo ""
echo "تم إعداد فرع النسخة 2.0 بنجاح!"
echo ""
echo "للبدء في تطوير النسخة 2.0، يمكنك البدء بتنفيذ المهام التالية:"
echo "1. راجع خريطة الطريق في ملف VERSION-2-ROADMAP.md"
echo "2. ابدأ بتنفيذ البنية التحتية للميزات ذات الأولوية العالية"
echo "3. قم بتحديث ملف التكوين config/v2_features.php عند إضافة ميزات جديدة"
echo ""
echo "لدفع التغييرات إلى المستودع البعيد:"
echo "git push -u origin version-2.0"
echo ""
echo "لاختبار ميزات النسخة 2.0، تأكد من تشغيل المهاجرات الجديدة:"
echo "php artisan migrate"
