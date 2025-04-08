# دليل الترقية

## الترقية من الإصدار 0.x إلى 1.0

### الخطوات العامة للترقية

1. **عمل نسخة احتياطية كاملة**:
   ```bash
   php artisan app:database-backup
   ```

2. **تحديث ملفات المشروع**:
   ```bash
   git fetch origin
   git checkout v1.0.0
   ```

3. **تحديث الاعتماديات**:
   ```bash
   composer update
   npm update
   ```

4. **تطبيق التحديثات على قاعدة البيانات**:
   ```bash
   php artisan migrate
   ```

5. **تنظيف الذاكرة المؤقتة**:
   ```bash
   php artisan optimize:clear
   ```

6. **إعادة تجميع الأصول**:
   ```bash
   npm run build
   ```

### التغييرات الرئيسية في الإصدار 1.0

#### تغييرات قاعدة البيانات
- تم إضافة نظام العملات المتعددة
- تم تحسين نظام إدارة المستندات
- تمت إضافة جداول جديدة للإشعارات

#### تغييرات الواجهة
- تحسينات على لوحة التحكم
- تصميم جديد للمستخدمين
- لوحة إشعارات متطورة

#### تغييرات في وظائف النظام
- نظام جديد للتقارير والإحصائيات
- تحسينات في أمان النظام
- دعم كامل للعملات المتعددة

## الترقية من نسخة 1.0.x إلى 1.0.y

لترقية النظام بين الإصدارات الفرعية، اتبع الخطوات التالية:

1. تحديث الملفات:
   ```bash
   git pull
   ```

2. تحديث الاعتماديات إذا لزم الأمر:
   ```bash
   composer update
   ```

3. تطبيق أي تعديلات على قاعدة البيانات:
   ```bash
   php artisan migrate
   ```

4. تنظيف الذاكرة المؤقتة:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

## الترقية من نسخة 2.0 إلى 2.1

### الخطوات العامة للترقية

1. **عمل نسخة احتياطية كاملة**:
   ```bash
   php artisan app:database-backup
   ```

2. **تحديث ملفات المشروع**:
   ```bash
   # تأكد من وجودك على فرع version-2.0 وتحديثه
   git checkout version-2.0
   git pull origin version-2.0
   
   # إنشاء فرع version-2.1 جديد
   git checkout -b version-2.1
   
   # تنزيل سكريبت الإعداد
   curl -O https://raw.githubusercontent.com/jaksws/laravel-v2-1/main/VERSION-2-1-SETUP.sh
   chmod +x VERSION-2-1-SETUP.sh
   
   # تشغيل سكريبت إعداد النسخة 2.1
   bash VERSION-2-1-SETUP.sh
   ```

3. **تحديث الاعتماديات**:
   ```bash
   composer update
   npm update
   ```

4. **تطبيق التحديثات على قاعدة البيانات**:
   ```bash
   php artisan migrate
   ```

5. **تنظيف الذاكرة المؤقتة**:
   ```bash
   php artisan optimize:clear
   ```

6. **إعادة تجميع الأصول**:
   ```bash
   npm run build
   ```

### معالجة تحذيرات Sass المهملة

تستخدم النسخة 2.1 صيغة Sass الحديثة لتجنب التحذيرات المتعلقة بالوظائف المهملة. إذا كنت تستخدم ملفات Sass مخصصة، يجب عليك تحديثها كما يلي:

1. **استبدال قواعد `@import` بقواعد `@use` و `@forward`**:
   ```scss
   // قبل
   @import 'variables';
   @import 'bootstrap/scss/bootstrap';

   // بعد
   @use 'variables';
   @use 'bootstrap/scss/bootstrap';
   ```

2. **استبدال دوال الألوان المهملة بنسختها الحديثة**:
   ```scss
   // قبل
   $color-rgb: red($color), green($color), blue($color);

   // بعد
   @use "sass:color";
   $color-rgb: color.channel($color, "red"), color.channel($color, "green"), color.channel($color, "blue");
   ```

3. **استبدال دوال الدمج والوحدات**:
   ```scss
   // قبل
   $mixed-color: mix(white, $color, 10%);
   $is-percent: unit($value) == "%";

   // بعد
   @use "sass:color";
   @use "sass:math";
   $mixed-color: color.mix(white, $color, 10%);
   $is-percent: math.unit($value) == "%";
   ```

### التغييرات الرئيسية في الإصدار 2.1

#### تحسينات على نظام الدفع
- دعم المزيد من بوابات الدفع المحلية والعالمية
- تحسين واجهة إدارة المدفوعات
- دعم تقارير المدفوعات المتقدمة

#### تحسينات متعددة اللغات
- إضافة لغات جديدة: الألمانية، الإسبانية
- تحسين آلية الترجمة الديناميكية للبيانات
- دعم تغيير اللغة في الوقت الفعلي دون إعادة تحميل الصفحة

#### تحليل البيانات المتقدم
- لوحات تحكم ذكية للتحليلات
- تقارير مرئية متقدمة
- مؤشرات الأداء الرئيسية للوكالات

## إصلاح المشاكل الشائعة بعد الترقية

### مشكلة: ظهور خطأ "Class not found"
الحل: قم بتنفيذ الأمر التالي:
```bash
composer dump-autoload
```

### مشكلة: عدم ظهور التغييرات في الواجهة
الحل: قم بمسح ذاكرة التخزين المؤقت للمتصفح، ثم نفذ:
```bash
php artisan view:clear
npm run build
```

### مشكلة: ظهور أخطاء في قاعدة البيانات
الحل: تأكد من تطبيق جميع الترحيلات:
```bash
php artisan migrate:status
```
إذا كانت هناك ترحيلات معلقة، قم بتشغيل:
```bash
php artisan migrate
```

للمزيد من المساعدة، يرجى فتح issue في صفحة المشروع على GitHub.
