@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">الإعدادات</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-cog me-2"></i> إعدادات الوكالة</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">أقسام الإعدادات</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush settings-menu">
                        <a href="#agency-settings" class="list-group-item list-group-item-action active">
                            <i class="fas fa-building me-2"></i> معلومات الوكالة
                        </a>
                        <a href="#currency-settings" class="list-group-item list-group-item-action">
                            <i class="fas fa-money-bill-wave me-2"></i> إعدادات العملات
                        </a>
                        <a href="#notification-settings" class="list-group-item list-group-item-action">
                            <i class="fas fa-bell me-2"></i> إعدادات الإشعارات
                        </a>
                        <a href="#email-settings" class="list-group-item list-group-item-action">
                            <i class="fas fa-envelope me-2"></i> إعدادات البريد الإلكتروني
                        </a>
                        <a href="#commission-settings" class="list-group-item list-group-item-action">
                            <i class="fas fa-percentage me-2"></i> إعدادات العمولات
                        </a>
                        <a href="#integration-settings" class="list-group-item list-group-item-action">
                            <i class="fas fa-plug me-2"></i> إعدادات التكامل
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="d-grid">
                <a href="{{ route('agency.settings.currencies') }}" class="btn btn-success mb-4">
                    <i class="fas fa-money-bill-wave me-1"></i> إدارة العملات
                </a>
            </div>
        </div>
        
        <div class="col-md-9">
            <!-- إعدادات الوكالة -->
            <div id="agency-settings" class="card shadow mb-4 settings-section active">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-1"></i> معلومات الوكالة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="settings_type" value="agency_info">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">اسم الوكالة</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $agency->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="default_currency" class="form-label">العملة الافتراضية</label>
                                    <select class="form-select @error('default_currency') is-invalid @enderror" id="default_currency" name="default_currency" required>
                                        @foreach(\App\Models\Currency::where('is_active', true)->get() as $currency)
                                            <option value="{{ $currency->code }}" {{ ($agency->default_currency ?? '') == $currency->code || $currency->is_default ? 'selected' : '' }}>
                                                {{ $currency->name }} ({{ $currency->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('default_currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">هذه العملة ستكون الافتراضية في جميع معاملات الوكالة</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $agency->phone ?? '') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني للتواصل</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $agency->contact_email ?? '') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="website" class="form-label">الموقع الإلكتروني</label>
                                    <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $agency->website ?? '') }}" placeholder="https://example.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_number" class="form-label">الرقم الضريبي</label>
                                    <input type="text" class="form-control" id="tax_number" name="tax_number" value="{{ old('tax_number', $agency->tax_number ?? '') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="commercial_register" class="form-label">السجل التجاري</label>
                                    <input type="text" class="form-control" id="commercial_register" name="commercial_register" value="{{ old('commercial_register', $agency->commercial_register ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="social_media" class="form-label">حسابات التواصل الاجتماعي</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                        <input type="text" class="form-control" id="social_media_instagram" name="social_media_instagram" value="{{ old('social_media_instagram', $agency->social_media_instagram ?? '') }}" placeholder="@username">
                                    </div>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                        <input type="text" class="form-control" id="social_media_twitter" name="social_media_twitter" value="{{ old('social_media_twitter', $agency->social_media_twitter ?? '') }}" placeholder="@username">
                                    </div>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                        <input type="text" class="form-control" id="social_media_facebook" name="social_media_facebook" value="{{ old('social_media_facebook', $agency->social_media_facebook ?? '') }}" placeholder="username أو رابط الصفحة">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                        <input type="text" class="form-control" id="social_media_linkedin" name="social_media_linkedin" value="{{ old('social_media_linkedin', $agency->social_media_linkedin ?? '') }}" placeholder="username أو رابط الصفحة">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $agency->address ?? '') }}</textarea>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="logo" class="form-label">شعار الوكالة</label>
                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                    <small class="form-text text-muted">الحد الأقصى لحجم الملف: 2 ميجابايت. الصيغ المدعومة: JPG, PNG, GIF</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                @if(isset($agency->logo_path) && $agency->logo_path)
                                    <div class="text-center">
                                        <img src="{{ Storage::url($agency->logo_path) }}" alt="شعار الوكالة" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="theme_color" class="form-label">لون الواجهة الرئيسي</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="theme_color" name="theme_color" value="{{ old('theme_color', $agency->theme_color ?? '#007bff') }}">
                                        <input type="text" class="form-control" value="{{ old('theme_color', $agency->theme_color ?? '#007bff') }}" readonly>
                                    </div>
                                    <small class="form-text text-muted">اللون الرئيسي للواجهة والأزرار</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="agency_language" class="form-label">اللغة الافتراضية</label>
                                    <select class="form-select" id="agency_language" name="agency_language">
                                        <option value="ar" {{ ($agency->default_language ?? 'ar') == 'ar' ? 'selected' : '' }}>العربية</option>
                                        <option value="en" {{ ($agency->default_language ?? 'ar') == 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- إعدادات العملات -->
            <div id="currency-settings" class="card shadow mb-4 settings-section">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-1"></i> إعدادات العملات</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-1"></i> إدارة العملات</h6>
                        <p>يمكنك إدارة العملات المستخدمة في النظام وتحديث أسعار الصرف من خلال صفحة إدارة العملات.</p>
                        <a href="{{ route('agency.settings.currencies') }}" class="btn btn-info">
                            <i class="fas fa-money-bill-wave me-1"></i> الانتقال إلى إدارة العملات
                        </a>
                    </div>
                    
                    <form action="{{ route('agency.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="settings_type" value="currency_settings">
                        
                        <div class="mb-3">
                            <label for="price_decimals" class="form-label">عدد الخانات العشرية للأسعار</label>
                            <select class="form-select" id="price_decimals" name="price_decimals">
                                <option value="0" {{ ($agency->price_decimals ?? 2) == 0 ? 'selected' : '' }}>بدون أرقام عشرية</option>
                                <option value="1" {{ ($agency->price_decimals ?? 2) == 1 ? 'selected' : '' }}>رقم عشري واحد</option>
                                <option value="2" {{ ($agency->price_decimals ?? 2) == 2 ? 'selected' : '' }}>رقمين عشريين</option>
                                <option value="3" {{ ($agency->price_decimals ?? 2) == 3 ? 'selected' : '' }}>ثلاثة أرقام عشرية</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price_display_format" class="form-label">تنسيق عرض الأسعار</label>
                            <select class="form-select" id="price_display_format" name="price_display_format">
                                <option value="symbol_first" {{ ($agency->price_display_format ?? 'symbol_first') == 'symbol_first' ? 'selected' : '' }}>رمز العملة قبل السعر (مثال: ر.س 100)</option>
                                <option value="symbol_last" {{ ($agency->price_display_format ?? 'symbol_first') == 'symbol_last' ? 'selected' : '' }}>رمز العملة بعد السعر (مثال: 100 ر.س)</option>
                                <option value="code_first" {{ ($agency->price_display_format ?? 'symbol_first') == 'code_first' ? 'selected' : '' }}>كود العملة قبل السعر (مثال: SAR 100)</option>
                                <option value="code_last" {{ ($agency->price_display_format ?? 'symbol_first') == 'code_last' ? 'selected' : '' }}>كود العملة بعد السعر (مثال: 100 SAR)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="auto_convert_prices" name="auto_convert_prices" value="1" {{ ($agency->auto_convert_prices ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_convert_prices">تحويل الأسعار تلقائياً للعملة المفضلة للعميل</label>
                            <small class="d-block text-muted">عند تفعيل هذا الخيار، سيتم تحويل الأسعار تلقائياً للعملة المفضلة للعميل عند عرضها</small>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- إعدادات الإشعارات -->
            <div id="notification-settings" class="card shadow mb-4 settings-section">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bell me-1"></i> إعدادات الإشعارات</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="settings_type" value="notification_settings">
                        
                        <h6 class="fw-bold mb-3">إعدادات عامة للإشعارات</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="enable_system_notifications" name="enable_system_notifications" value="1" 
                                        {{ isset($agency->notification_settings['enable_system_notifications']) && $agency->notification_settings['enable_system_notifications'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_system_notifications">تفعيل إشعارات النظام</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="enable_email_notifications" name="enable_email_notifications" value="1" 
                                        {{ isset($agency->notification_settings['enable_email_notifications']) && $agency->notification_settings['enable_email_notifications'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_email_notifications">تفعيل إشعارات البريد الإلكتروني</label>
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold mb-3">أنواع الإشعارات</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="notify_on_new_request" name="notify_on_new_request" value="1" 
                                        {{ isset($agency->notification_settings['notify_on_new_request']) && $agency->notification_settings['notify_on_new_request'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_on_new_request">إشعار عند إنشاء طلب جديد</label>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="notify_on_new_quote" name="notify_on_new_quote" value="1" 
                                        {{ isset($agency->notification_settings['notify_on_new_quote']) && $agency->notification_settings['notify_on_new_quote'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_on_new_quote">إشعار عند تقديم عرض سعر جديد</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="notify_on_status_change" name="notify_on_status_change" value="1" 
                                        {{ isset($agency->notification_settings['notify_on_status_change']) && $agency->notification_settings['notify_on_status_change'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_on_status_change">إشعار عند تغيير حالة الطلب</label>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="daily_summary" name="daily_summary" value="1" 
                                        {{ isset($agency->notification_settings['daily_summary']) && $agency->notification_settings['daily_summary'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="daily_summary">ملخص يومي للنشاطات</label>
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold mb-3">إعدادات متقدمة</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="notify_customers" name="notify_customers" value="1" 
                                        {{ isset($agency->notification_settings['notify_customers']) && $agency->notification_settings['notify_customers'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_customers">إرسال إشعارات للعملاء</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="notify_subagents" name="notify_subagents" value="1" 
                                        {{ isset($agency->notification_settings['notify_subagents']) && $agency->notification_settings['notify_subagents'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_subagents">إرسال إشعارات للسبوكلاء</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- إعدادات البريد الإلكتروني -->
            <div id="email-settings" class="card shadow mb-4 settings-section">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-envelope me-1"></i> إعدادات البريد الإلكتروني</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="settings_type" value="email_settings">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email_sender_name" class="form-label">اسم المرسل</label>
                                    <input type="text" class="form-control" id="email_sender_name" name="email_sender_name" 
                                        value="{{ old('email_sender_name', $agency->email_settings['sender_name'] ?? $agency->name ?? '') }}" required>
                                    <small class="form-text text-muted">الاسم الذي سيظهر كمرسل في البريد الإلكتروني</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email_sender_address" class="form-label">عنوان البريد الإلكتروني للمرسل</label>
                                    <input type="email" class="form-control" id="email_sender_address" name="email_sender_address" 
                                        value="{{ old('email_sender_address', $agency->email_settings['sender_address'] ?? $agency->contact_email ?? '') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email_template" class="form-label">قالب البريد الإلكتروني</label>
                            <select class="form-select" id="email_template" name="email_template">
                                <option value="default" {{ isset($agency->email_settings['template']) && $agency->email_settings['template'] == 'default' ? 'selected' : '' }}>القالب الافتراضي</option>
                                <option value="minimal" {{ isset($agency->email_settings['template']) && $agency->email_settings['template'] == 'minimal' ? 'selected' : '' }}>قالب بسيط</option>
                                <option value="branded" {{ isset($agency->email_settings['template']) && $agency->email_settings['template'] == 'branded' ? 'selected' : '' }}>قالب مخصص مع الشعار</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email_signature" class="form-label">توقيع البريد الإلكتروني</label>
                            <textarea class="form-control" id="email_signature" name="email_signature" rows="3">{{ old('email_signature', $agency->email_settings['signature'] ?? '') }}</textarea>
                            <small class="form-text text-muted">يمكنك استخدام وسوم HTML بسيطة</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email_footer_text" class="form-label">نص تذييل البريد الإلكتروني</label>
                            <textarea class="form-control" id="email_footer_text" name="email_footer_text" rows="2">{{ old('email_footer_text', $agency->email_settings['footer_text'] ?? '') }}</textarea>
                            <small class="form-text text-muted">النص الذي سيظهر في نهاية جميع رسائل البريد الإلكتروني</small>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- إعدادات العمولات -->
            <div id="commission-settings" class="card shadow mb-4 settings-section">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-percentage me-1"></i> إعدادات العمولات</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="settings_type" value="commission_settings">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="default_commission_rate" class="form-label">نسبة العمولة الافتراضية</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="default_commission_rate" name="default_commission_rate" 
                                            value="{{ old('default_commission_rate', $agency->default_commission_rate ?? 10) }}" min="0" max="100" step="0.1" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="form-text text-muted">هذه النسبة ستطبق على جميع الخدمات والسبوكلاء ما لم يتم تحديد نسبة خاصة.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="minimum_commission_amount" class="form-label">الحد الأدنى للعمولة</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="minimum_commission_amount" name="minimum_commission_amount" 
                                            value="{{ old('minimum_commission_amount', $agency->commission_settings['minimum_amount'] ?? 0) }}" min="0" step="0.01">
                                        <span class="input-group-text">{{ \App\Models\Currency::where('is_default', true)->first()->code ?? 'SAR' }}</span>
                                    </div>
                                    <small class="form-text text-muted">العمولة لن تقل عن هذا المبلغ بغض النظر عن النسبة المطبقة.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="commission_calculation_method" class="form-label">طريقة حساب العمولة</label>
                            <select class="form-select" id="commission_calculation_method" name="commission_calculation_method">
                                <option value="percentage" {{ isset($agency->commission_settings['calculation_method']) && $agency->commission_settings['calculation_method'] == 'percentage' ? 'selected' : '' }}>نسبة مئوية من إجمالي المبلغ</option>
                                <option value="fixed" {{ isset($agency->commission_settings['calculation_method']) && $agency->commission_settings['calculation_method'] == 'fixed' ? 'selected' : '' }}>مبلغ ثابت لكل عملية</option>
                                <option value="tiered" {{ isset($agency->commission_settings['calculation_method']) && $agency->commission_settings['calculation_method'] == 'tiered' ? 'selected' : '' }}>نسبة متدرجة حسب المبلغ</option>
                            </select>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="auto_calculate_commission" name="auto_calculate_commission" value="1" 
                                {{ isset($agency->commission_settings['auto_calculate']) && $agency->commission_settings['auto_calculate'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_calculate_commission">حساب العمولة تلقائياً</label>
                            <small class="d-block text-muted">عند تفعيل هذا الخيار، سيتم حساب العمولة تلقائياً عند إنشاء عرض سعر جديد.</small>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6 class="fw-bold mb-3">إعدادات الضرائب</h6>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="apply_commission_tax" name="apply_commission_tax" value="1" 
                                {{ isset($agency->commission_settings['apply_tax']) && $agency->commission_settings['apply_tax'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="apply_commission_tax">تطبيق ضريبة على العمولة</label>
                        </div>
                        
                        <div class="mb-3" id="tax_rate_container" style="{{ isset($agency->commission_settings['apply_tax']) && $agency->commission_settings['apply_tax'] ? '' : 'display: none;' }}">
                            <label for="commission_tax_rate" class="form-label">نسبة الضريبة</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="commission_tax_rate" name="commission_tax_rate" 
                                    value="{{ old('commission_tax_rate', $agency->commission_settings['tax_rate'] ?? 15) }}" min="0" max="100" step="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- إعدادات التكامل مع الخدمات -->
            <div id="integration-settings" class="card shadow mb-4 settings-section">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plug me-1"></i> إعدادات التكامل</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="settings_type" value="integration_settings">
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-1"></i> التكامل مع خدمات خارجية</h6>
                            <p>يمكنك ربط النظام مع خدمات خارجية لتوسيع إمكانيات وكالتك</p>
                        </div>
                        
                        <h6 class="fw-bold mb-3">بوابات الدفع</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enable_paypal" name="enable_paypal" value="1" {{ isset($agency->integration_settings['enable_paypal']) && $agency->integration_settings['enable_paypal'] ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_paypal">تفعيل PayPal</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="paypal_client_id" class="form-label">معرف العميل (Client ID)</label>
                                            <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" value="{{ old('paypal_client_id', $agency->integration_settings['paypal_client_id'] ?? '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="paypal_secret" class="form-label">المفتاح السري (Secret)</label>
                                            <input type="password" class="form-control" id="paypal_secret" name="paypal_secret" value="{{ old('paypal_secret', $agency->integration_settings['paypal_secret'] ?? '') }}">
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="paypal_sandbox" name="paypal_sandbox" value="1" {{ isset($agency->integration_settings['paypal_sandbox']) && $agency->integration_settings['paypal_sandbox'] ? 'checked' : '' }}>
                                            <label class="form-check-label" for="paypal_sandbox">وضع الاختبار (Sandbox)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enable_stripe" name="enable_stripe" value="1" {{ isset($agency->integration_settings['enable_stripe']) && $agency->integration_settings['enable_stripe'] ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_stripe">تفعيل Stripe</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stripe_publishable_key" class="form-label">المفتاح العام (Publishable Key)</label>
                                            <input type="text" class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" value="{{ old('stripe_publishable_key', $agency->integration_settings['stripe_publishable_key'] ?? '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="stripe_secret_key" class="form-label">المفتاح السري (Secret Key)</label>
                                            <input type="password" class="form-control" id="stripe_secret_key" name="stripe_secret_key" value="{{ old('stripe_secret_key', $agency->integration_settings['stripe_secret_key'] ?? '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="stripe_webhook_secret" class="form-label">مفتاح الويب هوك (Webhook Secret)</label>
                                            <input type="password" class="form-control" id="stripe_webhook_secret" name="stripe_webhook_secret" value="{{ old('stripe_webhook_secret', $agency->integration_settings['stripe_webhook_secret'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold mb-3">واجهات برمجة التطبيقات (APIs)</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enable_google_maps" name="enable_google_maps" value="1" {{ isset($agency->integration_settings['enable_google_maps']) && $agency->integration_settings['enable_google_maps'] ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_google_maps">تفعيل خرائط جوجل</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="google_maps_api_key" class="form-label">مفتاح API</label>
                                            <input type="text" class="form-control" id="google_maps_api_key" name="google_maps_api_key" value="{{ old('google_maps_api_key', $agency->integration_settings['google_maps_api_key'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enable_currency_api" name="enable_currency_api" value="1" {{ isset($agency->integration_settings['enable_currency_api']) && $agency->integration_settings['enable_currency_api'] ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_currency_api">تحديث أسعار العملات تلقائياً</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="currency_api_source" class="form-label">مصدر بيانات العملات</label>
                                            <select class="form-select" id="currency_api_source" name="currency_api_source">
                                                <option value="openexchangerates" {{ isset($agency->integration_settings['currency_api_source']) && $agency->integration_settings['currency_api_source'] == 'openexchangerates' ? 'selected' : '' }}>Open Exchange Rates</option>
                                                <option value="currencylayer" {{ isset($agency->integration_settings['currency_api_source']) && $agency->integration_settings['currency_api_source'] == 'currencylayer' ? 'selected' : '' }}>Currency Layer</option>
                                                <option value="fixer" {{ isset($agency->integration_settings['currency_api_source']) && $agency->integration_settings['currency_api_source'] == 'fixer' ? 'selected' : '' }}>Fixer.io</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="currency_api_key" class="form-label">مفتاح API</label>
                                            <input type="text" class="form-control" id="currency_api_key" name="currency_api_key" value="{{ old('currency_api_key', $agency->integration_settings['currency_api_key'] ?? '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="currency_update_frequency" class="form-label">معدل التحديث</label>
                                            <select class="form-select" id="currency_update_frequency" name="currency_update_frequency">
                                                <option value="daily" {{ isset($agency->integration_settings['currency_update_frequency']) && $agency->integration_settings['currency_update_frequency'] == 'daily' ? 'selected' : '' }}>يومياً</option>
                                                <option value="weekly" {{ isset($agency->integration_settings['currency_update_frequency']) && $agency->integration_settings['currency_update_frequency'] == 'weekly' ? 'selected' : '' }}>أسبوعياً</option>
                                                <option value="monthly" {{ isset($agency->integration_settings['currency_update_frequency']) && $agency->integration_settings['currency_update_frequency'] == 'monthly' ? 'selected' : '' }}>شهرياً</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold mb-3">مزامنة البيانات</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enable_api_access" name="enable_api_access" value="1" {{ isset($agency->integration_settings['enable_api_access']) && $agency->integration_settings['enable_api_access'] ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_api_access">تفعيل الوصول للـ API</label>
                                            </div>
                                        </div>
                                        <p class="text-muted">تفعيل هذا الخيار يتيح الوصول إلى بيانات وكالتك عبر واجهة برمجة التطبيقات (API).</p>
                                        <div class="mb-3">
                                            <label class="form-label">مفتاح API الخاص بك</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="{{ $agency->api_key ?? 'لم يتم إنشاء مفتاح بعد' }}" readonly>
                                                <button class="btn btn-outline-secondary" type="button" id="generate_api_key">تحديث</button>
                                            </div>
                                            <small class="form-text text-muted">استخدم هذا المفتاح للوصول إلى بيانات وكالتك من تطبيقات خارجية.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // التبديل بين أقسام الإعدادات
        const settingsLinks = document.querySelectorAll('.settings-menu a');
        const settingsSections = document.querySelectorAll('.settings-section');
        
        settingsLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // إزالة الكلاس النشط من جميع الروابط
                settingsLinks.forEach(item => item.classList.remove('active'));
                
                // إضافة الكلاس النشط للرابط المحدد
                this.classList.add('active');
                
                // إخفاء جميع الأقسام
                settingsSections.forEach(section => section.classList.remove('active'));
                
                // إظهار القسم المطلوب
                const targetId = this.getAttribute('href').substring(1);
                document.getElementById(targetId).classList.add('active');
            });
        });
        
        // إظهار/إخفاء حقل نسبة الضريبة
        const applyTaxCheckbox = document.getElementById('apply_commission_tax');
        const taxRateContainer = document.getElementById('tax_rate_container');
        
        if (applyTaxCheckbox && taxRateContainer) {
            applyTaxCheckbox.addEventListener('change', function() {
                taxRateContainer.style.display = this.checked ? 'block' : 'none';
            });
        }
    });
</script>
@endpush

<style>
    .settings-section {
        display: none;
    }
    .settings-section.active {
        display: block;
    }
</style>
@endsection
