<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agency;
use App\Models\User;
use App\Models\Service;
use App\Models\Currency;
use App\Models\Request as ServiceRequest;
use App\Models\Quote;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إضافة بيانات تجريبية للنظام';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('بدء إضافة البيانات التجريبية...');

        // التحقق من وجود بيانات
        if (Agency::count() > 0) {
            if (!$this->confirm('توجد بيانات بالفعل في النظام. هل تريد المتابعة وإضافة المزيد من البيانات التجريبية؟', true)) {
                $this->info('تم إلغاء العملية.');
                return 0;
            }
        }

        // إنشاء بيانات مستخدم التجربة السريعة
        $this->createQuickTestUser();

        // إنشاء الوكالات
        $yemen = $this->createYemenAgency();
        $gulf = $this->createGulfAgency();

        // إنشاء العملات إذا لم تكن موجودة
        $this->createCurrenciesIfNeeded();

        // إنشاء الخدمات
        $this->createServicesForAgency($yemen);
        $this->createServicesForAgency($gulf);

        // إنشاء طلبات وعروض أسعار لوكالة اليمن
        $this->createRequestsAndQuotes($yemen);

        $this->info('تم إضافة البيانات التجريبية بنجاح!');
        $this->info('يمكنك الآن تسجيل الدخول باستخدام:');
        $this->info('- حساب التجربة السريعة: test@example.com / 123456');
        $this->info('- مدير وكالة اليمن: admin@yemen-travel.com / password123');
        $this->info('- مدير وكالة الخليج: admin@gulf-travel.com / password123');

        return 0;
    }

    /**
     * إنشاء مستخدم للتجربة السريعة
     */
    private function createQuickTestUser()
    {
        $this->info('إنشاء حساب التجربة السريعة...');
        
        // إنشاء وكالة للتجربة
        $agency = Agency::firstOrCreate(
            ['name' => 'وكالة التجربة السريعة'],
            [
                'phone' => '+9665xxxxxxxx', // Add default phone number
                'email' => 'agency@example.com', // Add email field
                'default_currency' => 'SAR',
                'default_commission_rate' => 10,
                'price_decimals' => 2,
                'price_display_format' => 'symbol_first'
            ]
        );

        // إنشاء مستخدم للتجربة
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'مستخدم التجربة',
                'password' => Hash::make('123456'),
                'agency_id' => $agency->id,
                'user_type' => 'agency',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
    }

    /**
     * إنشاء وكالة اليمن للسفر
     */
    private function createYemenAgency()
    {
        $this->info('إنشاء وكالة اليمن للسفر والسياحة...');
        
        $agency = Agency::firstOrCreate(
            ['name' => 'وكالة اليمن للسفر والسياحة'],
            [
                'phone' => '+9677xxxxxxxx', // Add default phone number
                'email' => 'info@yemen-travel.com', // Add email field
                'default_currency' => 'SAR',
                'default_commission_rate' => 15,
                'price_decimals' => 2,
                'price_display_format' => 'symbol_first'
            ]
        );

        // إنشاء مدير الوكالة
        $admin = User::firstOrCreate(
            ['email' => 'admin@yemen-travel.com'],
            [
                'name' => 'مدير وكالة اليمن',
                'password' => Hash::make('password123'),
                'agency_id' => $agency->id,
                'user_type' => 'agency',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // إنشاء السبوكلاء
        $ahmed = User::firstOrCreate(
            ['email' => 'ahmed@yemen-travel.com'],
            [
                'name' => 'أحمد محمد',
                'password' => Hash::make('password123'),
                'agency_id' => $agency->id,
                'user_type' => 'subagent',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        $mohammed = User::firstOrCreate(
            ['email' => 'mohammed@yemen-travel.com'],
            [
                'name' => 'محمد علي',
                'password' => Hash::make('password123'),
                'agency_id' => $agency->id,
                'user_type' => 'subagent',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // إنشاء العملاء
        $salem = User::firstOrCreate(
            ['email' => 'salem@example.com'],
            [
                'name' => 'سالم علي',
                'password' => Hash::make('password123'),
                'agency_id' => $agency->id,
                'user_type' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        $fatima = User::firstOrCreate(
            ['email' => 'fatima@example.com'],
            [
                'name' => 'فاطمة أحمد',
                'password' => Hash::make('password123'),
                'agency_id' => $agency->id,
                'user_type' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        return $agency;
    }

    /**
     * إنشاء وكالة الخليج للسفريات
     */
    private function createGulfAgency()
    {
        $this->info('إنشاء وكالة الخليج للسفريات...');
        
        $agency = Agency::firstOrCreate(
            ['name' => 'وكالة الخليج للسفريات'],
            [
                'phone' => '+9665xxxxxxxx', // Add default phone number
                'email' => 'info@gulf-travel.com', // Add email field
                'default_currency' => 'SAR',
                'default_commission_rate' => 12,
                'price_decimals' => 2,
                'price_display_format' => 'symbol_first'
            ]
        );

        // إنشاء مدير الوكالة
        $admin = User::firstOrCreate(
            ['email' => 'admin@gulf-travel.com'],
            [
                'name' => 'مدير وكالة الخليج',
                'password' => Hash::make('password123'),
                'agency_id' => $agency->id,
                'user_type' => 'agency',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // إنشاء السبوكيل
        $khaled = User::firstOrCreate(
            ['email' => 'khaled@gulf-travel.com'],
            [
                'name' => 'خالد حسن',
                'password' => Hash::make('password123'),
                'agency_id' => $agency->id,
                'user_type' => 'subagent',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // إنشاء العميل
        $abdullah = User::firstOrCreate(
            ['email' => 'abdullah@example.com'],
            [
                'name' => 'عبد الله محمد',
                'password' => Hash::make('password123'),
                'agency_id' => $agency->id,
                'user_type' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        return $agency;
    }

    /**
     * إنشاء العملات إذا لم تكن موجودة
     */
    private function createCurrenciesIfNeeded()
    {
        if (Currency::count() == 0) {
            $this->info('إنشاء العملات...');
            
            Currency::create([
                'code' => 'SAR',
                'name' => 'الريال السعودي',
                'symbol' => 'ر.س',
                'is_default' => true,
                'exchange_rate' => 1.0000,
                'is_active' => true,
            ]);

            Currency::create([
                'code' => 'USD',
                'name' => 'الدولار الأمريكي',
                'symbol' => '$',
                'is_default' => false,
                'exchange_rate' => 0.2667,
                'is_active' => true,
            ]);

            Currency::create([
                'code' => 'EUR',
                'name' => 'اليورو',
                'symbol' => '€',
                'is_default' => false,
                'exchange_rate' => 0.2453,
                'is_active' => true,
            ]);

            Currency::create([
                'code' => 'YER',
                'name' => 'الريال اليمني',
                'symbol' => 'ر.ي',
                'is_default' => false,
                'exchange_rate' => 66.7500,
                'is_active' => true,
            ]);
        }
    }

    /**
     * إنشاء خدمات للوكالة
     */
    private function createServicesForAgency(Agency $agency)
    {
        $this->info("إنشاء الخدمات لوكالة {$agency->name}...");
        
        $services = [
            [
                'name' => 'موافقات أمنية',
                'type' => 'security_approval',
                'description' => 'خدمة إصدار الموافقات الأمنية للمسافرين من وإلى اليمن',
                'base_price' => 500,
                'currency_code' => 'SAR',
                'commission_rate' => $agency->default_commission_rate,
                'status' => 'active',
            ],
            [
                'name' => 'نقل بري',
                'type' => 'transportation',
                'description' => 'خدمة النقل البري بين المدن والمحافظات والدول المجاورة',
                'base_price' => 300,
                'currency_code' => 'SAR',
                'commission_rate' => $agency->default_commission_rate,
                'status' => 'active',
            ],
            [
                'name' => 'حج وعمرة',
                'type' => 'hajj_umrah',
                'description' => 'خدمات الحج والعمرة الشاملة مع الإقامة والنقل',
                'base_price' => 2500,
                'currency_code' => 'SAR',
                'commission_rate' => $agency->default_commission_rate,
                'status' => 'active',
            ],
            [
                'name' => 'تذاكر طيران',
                'type' => 'flight',
                'description' => 'حجز تذاكر الطيران لجميع شركات الطيران المحلية والعالمية',
                'base_price' => 1000,
                'currency_code' => 'SAR',
                'commission_rate' => $agency->default_commission_rate,
                'status' => 'active',
            ],
            [
                'name' => 'إصدار جوازات',
                'type' => 'passport',
                'description' => 'خدمة تجديد وإصدار جوازات السفر والتأشيرات',
                'base_price' => 800,
                'currency_code' => 'SAR',
                'commission_rate' => $agency->default_commission_rate,
                'status' => 'active',
            ],
        ];

        foreach ($services as $serviceData) {
            $service = Service::firstOrCreate(
                [
                    'name' => $serviceData['name'],
                    'agency_id' => $agency->id
                ],
                array_merge($serviceData, ['agency_id' => $agency->id])
            );

            // ربط الخدمة بالسبوكلاء
            $subagents = User::where('agency_id', $agency->id)
                ->where('user_type', 'subagent')
                ->where('is_active', true)
                ->get();
            
            $service->subagents()->syncWithoutDetaching($subagents->pluck('id')->toArray());
        }
    }

    /**
     * إنشاء طلبات وعروض أسعار
     */
    private function createRequestsAndQuotes(Agency $agency)
    {
        $this->info("إنشاء الطلبات وعروض الأسعار لوكالة {$agency->name}...");
        
        $services = Service::where('agency_id', $agency->id)->get();
        $customers = User::where('agency_id', $agency->id)
            ->where('user_type', 'customer')
            ->where('is_active', true)
            ->get();
        $subagents = User::where('agency_id', $agency->id)
            ->where('user_type', 'subagent')
            ->where('is_active', true)
            ->get();
        
        if ($services->isEmpty() || $customers->isEmpty() || $subagents->isEmpty()) {
            $this->warn("لا توجد خدمات أو عملاء أو سبوكلاء كافية لإنشاء الطلبات وعروض الأسعار.");
            return;
        }

        $statuses = ['pending', 'in_progress', 'completed'];
        $priorities = ['normal', 'urgent', 'emergency'];
        
        // إنشاء طلبات
        foreach ($customers as $customer) {
            foreach ($services->random(3) as $service) {
                $request = ServiceRequest::create([
                    'service_id' => $service->id,
                    'customer_id' => $customer->id,
                    'agency_id' => $agency->id,
                    'details' => "طلب خدمة {$service->name} للعميل {$customer->name}. تفاصيل إضافية: " . Str::random(50),
                    'priority' => $priorities[array_rand($priorities)],
                    'status' => $statuses[array_rand($statuses)],
                    'requested_date' => now()->addDays(rand(1, 30)),
                    'created_at' => now()->subDays(rand(1, 10)),
                    'updated_at' => now(),
                ]);
                
                // إنشاء عروض أسعار للطلب
                foreach ($subagents->random(rand(1, 2)) as $subagent) {
                    Quote::create([
                        'request_id' => $request->id,
                        'subagent_id' => $subagent->id,
                        'price' => $service->base_price * (1 + (rand(-20, 20) / 100)), // سعر مع هامش تغيير عشوائي
                        'commission_amount' => $service->base_price * ($service->commission_rate / 100),
                        'currency_code' => $service->currency_code,
                        'details' => "عرض سعر لخدمة {$service->name} من السبوكيل {$subagent->name}. " . Str::random(30),
                        'status' => $request->status == 'completed' ? 'customer_approved' : 
                                    ($request->status == 'in_progress' ? 'agency_approved' : 'pending'),
                        'created_at' => $request->created_at->addHours(rand(1, 5)),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
