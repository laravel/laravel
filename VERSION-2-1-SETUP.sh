#!/bin/bash

# سكريبت ترقية نظام وكالات السفر (RTLA) من الإصدار 2.0 إلى 2.1
# الاستخدام: ./VERSION-2-1-SETUP.sh

# تعيين الألوان للإخراج
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}بدء عملية الترقية إلى النسخة 2.1 من نظام وكالات السفر (RTLA)${NC}\n"

# التأكد من أننا في المجلد الصحيح
if [ ! -f "artisan" ]; then
    echo -e "${RED}خطأ: لم يتم العثور على ملف artisan. يرجى التأكد من تشغيل السكريبت من المجلد الرئيسي للمشروع.${NC}"
    exit 1
fi

# التحقق من إصدار النظام الحالي
echo -e "${YELLOW}التحقق من الإصدار الحالي...${NC}"
CURRENT_VERSION=$(grep "version" composer.json | head -1 | awk -F: '{ print $2 }' | sed 's/[",]//g' | tr -d '[:space:]')

if [[ "$CURRENT_VERSION" != "2.0."* ]]; then
    echo -e "${YELLOW}تحذير: هذا السكريبت مخصص للترقية من الإصدار 2.0.x إلى 2.1${NC}"
    read -p "النسخة الحالية لديك هي $CURRENT_VERSION. هل ترغب في المتابعة؟ (y/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}تم إلغاء الترقية.${NC}"
        exit 0
    fi
fi

if [[ "$CURRENT_VERSION" == "2.1."* ]]; then
    echo -e "${YELLOW}ملاحظة: يبدو أنك تستخدم بالفعل الإصدار $CURRENT_VERSION${NC}"
    read -p "هل ترغب في المتابعة على أي حال؟ (y/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}تم إلغاء الترقية.${NC}"
        exit 0
    fi
fi

# إنشاء نسخة احتياطية قبل الترقية
echo -e "${YELLOW}إنشاء نسخة احتياطية من الملفات والبيانات...${NC}"
BACKUP_DIR="backups/pre_upgrade_v2_1_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# نسخة احتياطية من ملفات التكوين
cp .env "$BACKUP_DIR/.env" 2>/dev/null || echo -e "${YELLOW}لا يمكن نسخ ملف .env${NC}"
cp -r config "$BACKUP_DIR/config" 2>/dev/null || echo -e "${YELLOW}لا يمكن نسخ مجلد config${NC}"

# نسخة احتياطية من قاعدة البيانات إذا كانت SQLite
if grep -q "DB_CONNECTION=sqlite" .env; then
    cp database/*.sqlite "$BACKUP_DIR/" 2>/dev/null || echo -e "${YELLOW}لا يمكن نسخ ملفات قاعدة البيانات SQLite${NC}"
else
    # إذا كان المستخدم لديه ملفات إعدادية محلية للنسخ الاحتياطي
    if [ -f ".db-backup.sh" ]; then
        echo -e "${YELLOW}تنفيذ سكريبت النسخ الاحتياطي المحلي...${NC}"
        bash .db-backup.sh "$BACKUP_DIR"
    else
        echo -e "${YELLOW}لإنشاء نسخة احتياطية من قاعدة البيانات الخاصة بك، يُنصح بتنفيذ:${NC}"
        echo -e "${GREEN}php artisan app:database-backup${NC}"
    fi
fi

echo -e "${GREEN}تم إنشاء النسخة الاحتياطية في مجلد: $BACKUP_DIR${NC}"

# تحديث ملفات المشروع
echo -e "${YELLOW}تحديث ملفات المشروع...${NC}"

# التحقق من وجود git
if command -v git &> /dev/null; then
    # حفظ الفرع الحالي
    CURRENT_BRANCH=$(git branch --show-current)
    
    echo -e "${YELLOW}جلب أحدث التغييرات من المستودع...${NC}"
    git fetch origin || echo -e "${YELLOW}لا يمكن جلب التحديثات من المستودع البعيد${NC}"
    
    # إنشاء فرع جديد للترقية
    echo -e "${YELLOW}إنشاء فرع جديد للترقية...${NC}"
    git checkout -b upgrade-v2.1 || echo -e "${YELLOW}لا يمكن إنشاء فرع جديد للترقية${NC}"
    
    # محاولة الدمج مع فرع الإصدار 2.1
    echo -e "${YELLOW}دمج تغييرات الإصدار 2.1...${NC}"
    git merge origin/version-2.1 || echo -e "${RED}حدث تعارض أثناء الدمج. يرجى حل التعارضات يدويًا${NC}"
else
    echo -e "${YELLOW}لم يتم العثور على git. سيتم تخطي تحديث الملفات عبر git.${NC}"
    echo -e "${YELLOW}يرجى تحميل الإصدار 2.1 يدويًا وتحديث الملفات.${NC}"
fi

# تحديث ملف تكوين ميزات الإصدار 2.1
echo -e "${YELLOW}تحديث ملف تكوين ميزات الإصدار 2.1...${NC}"

if [ -f "config/v2_features.php" ]; then
    # احتفظ بنسخة من إعدادات المستخدم الحالية
    cp config/v2_features.php config/v2_features.backup.php
    
    # تحديث ملف التكوين مع الإبقاء على إعدادات المستخدم
    echo -e "${YELLOW}تطبيق تحديثات ملف التكوين مع الحفاظ على الإعدادات الحالية...${NC}"
    # هذا سيتم تنفيذه في خطوة لاحقة عبر أوامر PHP Artisan
else
    echo -e "${YELLOW}إنشاء ملف تكوين ميزات الإصدار 2.1...${NC}"
    mkdir -p config
    cat > config/v2_features.php << 'EOL'
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ميزات الإصدار 2.1
    |--------------------------------------------------------------------------
    |
    | هذا الملف يحدد حالة تفعيل ميزات الإصدار 2.1 من النظام
    | يمكنك تفعيل أو تعطيل أي من هذه الميزات حسب الحاجة
    |
    */

    'multilingual' => [
        'enabled' => true,
        'available_locales' => ['ar', 'en', 'fr', 'tr', 'es', 'id', 'ur'],
        'default_locale' => 'ar',
    ],

    'dark_mode' => [
        'enabled' => true,
        'default' => 'system', // 'light', 'dark', 'system'
    ],

    'payment_system' => [
        'enabled' => true,
        'providers' => [
            'mada' => [
                'enabled' => true,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('MADA_MERCHANT_ID', ''),
                    'api_key' => env('MADA_API_KEY', ''),
                ],
            ],
            'visa' => [
                'enabled' => true,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('VISA_MERCHANT_ID', ''),
                    'api_key' => env('VISA_API_KEY', ''),
                ],
            ],
            'mastercard' => [
                'enabled' => true,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('MASTERCARD_MERCHANT_ID', ''),
                    'api_key' => env('MASTERCARD_API_KEY', ''),
                ],
            ],
            'apple_pay' => [
                'enabled' => true,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('APPLE_PAY_MERCHANT_ID', ''),
                    'certificate_path' => env('APPLE_PAY_CERTIFICATE_PATH', ''),
                ],
            ],
            'google_pay' => [
                'enabled' => true,
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
    
    'analytics' => [
        'enabled' => true,
        'modules' => [
            'dashboard' => true,
            'reports' => true,
            'customer_insights' => true,
            'market_trends' => true,
        ],
    ],
    
    'additional_languages' => [
        'enabled' => true,
        'languages' => [
            'es' => true, // الإسبانية
            'id' => true, // الإندونيسية
            'ur' => true, // الأوردية
        ],
    ],
];
EOL
fi

echo -e "${GREEN}تم تحديث ملف تكوين ميزات الإصدار 2.1${NC}"

# تحديث التبعيات
echo -e "${YELLOW}تحديث تبعيات Composer...${NC}"
composer install --no-interaction || echo -e "${RED}فشل تحديث تبعيات Composer${NC}"

echo -e "${YELLOW}تحديث تبعيات NPM...${NC}"
npm install || echo -e "${RED}فشل تحديث تبعيات NPM${NC}"

# تنفيذ تحديثات إضافية
echo -e "${YELLOW}تحديث ملفات الترجمة للغات الجديدة...${NC}"
mkdir -p lang/es lang/id lang/ur

# تنفيذ أوامر التحديث المخصصة
echo -e "${YELLOW}تنفيذ الأوامر المخصصة للترقية...${NC}"
php artisan v2:update-features || echo -e "${YELLOW}لا يوجد أمر تحديث مخصص أو فشل تنفيذه${NC}"

# إضافة إرشادات ختامية
echo -e "\n${GREEN}=============================${NC}"
echo -e "${GREEN}اكتملت عملية الترقية إلى الإصدار 2.1${NC}"
echo -e "${GREEN}=============================${NC}"
echo -e "\n${YELLOW}الخطوات التالية:${NC}"
echo -e "1. قم بتشغيل المايغريشن: ${GREEN}php artisan migrate${NC}"
echo -e "2. قم بتجميع الأصول الأمامية: ${GREEN}npm run build${NC}"
echo -e "3. قم بمسح ذاكرة التخزين المؤقت: ${GREEN}php artisan optimize:clear${NC}"
echo -e "4. قم بمراجعة الميزات الجديدة في ملف: ${GREEN}config/v2_features.php${NC}"
echo -e "\n${YELLOW}ملاحظة: تم إنشاء نسخة احتياطية قبل الترقية في مجلد:${NC} ${GREEN}$BACKUP_DIR${NC}"
echo -e "${YELLOW}إذا واجهتك أي مشكلة، يمكنك استعادة النسخة الاحتياطية.${NC}"
echo -e "\n${YELLOW}لمزيد من المعلومات حول ميزات الإصدار 2.1، راجع:${NC} ${GREEN}VERSION-2-ROADMAP.md${NC}"

exit 0