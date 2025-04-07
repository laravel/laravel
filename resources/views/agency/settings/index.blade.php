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
                        
                        <!-- معلومات إضافية للوكالة -->
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
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $agency->address ?? '') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="logo" class="form-label">شعار الوكالة</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            <small class="form-text text-muted">الحد الأقصى لحجم الملف: 2 ميجابايت. الصيغ المدعومة: JPG, PNG, GIF</small>
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
            
            <!-- الأقسام الأخرى من الإعدادات -->
            <div id="notification-settings" class="card shadow mb-4 settings-section">
                <!-- محتوى إعدادات الإشعارات -->
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bell me-1"></i> إعدادات الإشعارات</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">سيتم توفير إعدادات الإشعارات في الإصدار القادم.</p>
                </div>
            </div>
            
            <div id="email-settings" class="card shadow mb-4 settings-section">
                <!-- محتوى إعدادات البريد الإلكتروني -->
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-envelope me-1"></i> إعدادات البريد الإلكتروني</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">سيتم توفير إعدادات البريد الإلكتروني في الإصدار القادم.</p>
                </div>
            </div>
            
            <div id="commission-settings" class="card shadow settings-section">
                <!-- محتوى إعدادات العمولات -->
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-percentage me-1"></i> إعدادات العمولات</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="settings_type" value="commission_settings">
                        
                        <div class="mb-3">
                            <label for="default_commission_rate" class="form-label">نسبة العمولة الافتراضية</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="default_commission_rate" name="default_commission_rate" value="{{ old('default_commission_rate', $agency->default_commission_rate ?? 10) }}" min="0" max="100" step="0.1" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="form-text text-muted">هذه النسبة ستطبق على جميع الخدمات والسبوكلاء ما لم يتم تحديد نسبة خاصة.</small>
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
