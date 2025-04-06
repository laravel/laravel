<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedDemoData extends Command
{
    protected $signature = 'app:seed-demo';
    protected $description = 'إضافة بيانات افتراضية للتجربة والعرض التوضيحي';

    public function handle()
    {
        if ($this->confirm('هل تريد إضافة بيانات افتراضية للتجربة؟ سيتم إنشاء وكالات ومستخدمين وخدمات وطلبات')) {
            $this->info('جارِ إضافة البيانات الافتراضية...');
            
            // تشغيل البذور المطلوبة
            $this->call('db:seed', [
                '--class' => 'AgencySeeder'
            ]);
            
            $this->call('db:seed', [
                '--class' => 'UserSeeder'
            ]);
            
            $this->call('db:seed', [
                '--class' => 'ServiceSeeder'
            ]);
            
            $this->call('db:seed', [
                '--class' => 'RequestSeeder'
            ]);
            
            $this->call('db:seed', [
                '--class' => 'QuoteSeeder'
            ]);
            
            $this->info('تم إضافة البيانات الافتراضية بنجاح!');
            $this->info('يمكنك الآن تسجيل الدخول باستخدام:');
            $this->info('البريد الإلكتروني: test@example.com');
            $this->info('كلمة المرور: 123456');
        } else {
            $this->info('تم إلغاء العملية.');
        }
    }
}
