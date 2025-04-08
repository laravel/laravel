<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckApplicationStatus extends Command
{
    protected $signature = 'app:check-status';
    protected $description = 'التحقق من حالة التطبيق وإصلاح المشاكل الشائعة';

    public function handle()
    {
        $this->info('جاري التحقق من حالة التطبيق...');
        
        // التحقق من اتصال قاعدة البيانات
        $this->info('التحقق من اتصال قاعدة البيانات...');
        try {
            \DB::connection()->getPdo();
            $this->info('✓ الاتصال بقاعدة البيانات ناجح: ' . \DB::connection()->getDatabaseName());
        } catch (\Exception $e) {
            $this->error('✗ خطأ في الاتصال بقاعدة البيانات: ' . $e->getMessage());
            $this->line('- تأكد من تكوين ملف .env بشكل صحيح');
            $this->line('- تأكد من تشغيل خدمة قاعدة البيانات');
        }

        // التحقق من التصاريح
        $this->info('التحقق من تصاريح المجلدات...');
        $paths = [
            storage_path(),
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            public_path('storage'),
        ];

        foreach ($paths as $path) {
            if (!file_exists($path)) {
                $this->error("✗ المجلد غير موجود: {$path}");
                continue;
            }

            if (!is_writable($path)) {
                $this->error("✗ المجلد غير قابل للكتابة: {$path}");
                $this->line("- قم بتنفيذ: chmod -R 775 {$path}");
            } else {
                $this->info("✓ تصاريح المجلد صحيحة: {$path}");
            }
        }

        // التحقق من وجود الترجمات العربية
        $this->info('التحقق من ملفات الترجمة العربية...');
        if (!file_exists(resource_path('lang/ar'))) {
            $this->error('✗ ملفات الترجمة العربية غير موجودة');
            $this->line('- قم بتنفيذ: php artisan lang:publish');
            $this->line('- ثم قم بإنشاء مجلد ar مع ملفات الترجمة');
        } else {
            $this->info('✓ ملفات الترجمة العربية موجودة');
        }

        // تنظيف الكاش والتأكد من تحميل التغييرات
        $this->info('تنظيف الكاش...');
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        
        $this->info('تم الانتهاء من فحص حالة التطبيق!');
    }
}
