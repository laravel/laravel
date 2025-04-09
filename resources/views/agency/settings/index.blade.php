@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إعدادات الوكالة</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-cogs me-2"></i> إعدادات الوكالة</h2>
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
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="agency-info-tab" data-bs-toggle="pill" data-bs-target="#agency-info" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i> معلومات الوكالة
                        </button>
                        <button class="nav-link" id="payment-settings-tab" data-bs-toggle="pill" data-bs-target="#payment-settings" type="button" role="tab">
                            <i class="fas fa-credit-card me-1"></i> إعدادات الدفع
                        </button>
                        <button class="nav-link" id="notification-settings-tab" data-bs-toggle="pill" data-bs-target="#notification-settings" type="button" role="tab">
                            <i class="fas fa-bell me-1"></i> إعدادات الإشعارات
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="tab-content" id="v-pills-tabContent">
                <!-- معلومات الوكالة -->
                <div class="tab-pane fade show active" id="agency-info" role="tabpanel" aria-labelledby="agency-info-tab">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">معلومات الوكالة</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('agency.settings.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="settings_type" value="agency_info">
                                
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">اسم الوكالة <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $agency->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3 text-center">
                                            <label class="form-label">شعار الوكالة</label>
                                            <div class="logo-container mb-2">
                                                @if($agency->logo_path)
                                                    <img src="{{ Storage::url($agency->logo_path) }}" alt="{{ $agency->name }}" class="img-thumbnail" style="max-height: 100px;">
                                                @else
                                                    <div class="no-logo text-center p-3 bg-light">
                                                        <i class="fas fa-building fa-3x text-secondary"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo">
                                            @error('logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">رقم الهاتف</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $agency->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">البريد الإلكتروني</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $agency->contact_email) }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tax_number" class="form-label">الرقم الضريبي</label>
                                            <input type="text" class="form-control @error('tax_number') is-invalid @enderror" id="tax_number" name="tax_number" value="{{ old('tax_number', $agency->tax_number) }}">
                                            @error('tax_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="commercial_register" class="form-label">السجل التجاري</label>
                                            <input type="text" class="form-control @error('commercial_register') is-invalid @enderror" id="commercial_register" name="commercial_register" value="{{ old('commercial_register', $agency->commercial_register) }}">
                                            @error('commercial_register')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="website" class="form-label">الموقع الإلكتروني</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $agency->website) }}">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">العنوان</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $agency->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="default_currency" class="form-label">العملة الافتراضية</label>
                                            <select class="form-select @error('default_currency') is-invalid @enderror" id="default_currency" name="default_currency">
                                                @foreach($currencies as $currency)
                                                    <option value="{{ $currency->code }}" {{ $agency->default_currency == $currency->code ? 'selected' : '' }}>
                                                        {{ $currency->name }} ({{ $currency->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('default_currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="agency_language" class="form-label">لغة الواجهة</label>
                                            <select class="form-select @error('agency_language') is-invalid @enderror" id="agency_language" name="agency_language">
                                                <option value="ar" {{ $agency->agency_language == 'ar' ? 'selected' : '' }}>العربية</option>
                                                <option value="en" {{ $agency->agency_language == 'en' ? 'selected' : '' }}>English</option>
                                            </select>
                                            @error('agency_language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="theme_color" class="form-label">لون الثيم</label>
                                    <input type="color" class="form-control form-control-color @error('theme_color') is-invalid @enderror" id="theme_color" name="theme_color" value="{{ old('theme_color', $agency->theme_color ?? '#007bff') }}">
                                    @error('theme_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <h5 class="mt-4">مواقع التواصل الاجتماعي</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="social_media_facebook" class="form-label">فيسبوك</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                                <input type="url" class="form-control" id="social_media_facebook" name="social_media_facebook" value="{{ old('social_media_facebook', $agency->social_media_facebook) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="social_media_twitter" class="form-label">تويتر</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                                <input type="url" class="form-control" id="social_media_twitter" name="social_media_twitter" value="{{ old('social_media_twitter', $agency->social_media_twitter) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="social_media_instagram" class="form-label">انستغرام</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                                <input type="url" class="form-control" id="social_media_instagram" name="social_media_instagram" value="{{ old('social_media_instagram', $agency->social_media_instagram) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="social_media_linkedin" class="form-label">لينكد إن</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                                <input type="url" class="form-control" id="social_media_linkedin" name="social_media_linkedin" value="{{ old('social_media_linkedin', $agency->social_media_linkedin) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات الدفع -->
                <div class="tab-pane fade" id="payment-settings" role="tabpanel" aria-labelledby="payment-settings-tab">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">إعدادات الدفع</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('agency.settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="settings_type" value="payment_settings">
                                
                                <div class="mb-3">
                                    <label for="commission_rate" class="form-label">نسبة العمولة الافتراضية للسبوكلاء (%)</label>
                                    <input type="number" class="form-control @error('commission_rate') is-invalid @enderror" id="commission_rate" name="commission_rate" min="0" max="100" step="0.01" value="{{ old('commission_rate', $agency->payment_settings['commission_rate'] ?? 10) }}">
                                    @error('commission_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">هذه النسبة ستطبق على جميع السبوكلاء الجدد، ويمكن تعديلها لكل سبوكيل على حدة.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">طرق الدفع المتاحة</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_cash" name="payment_methods[]" value="cash" {{ in_array('cash', $agency->payment_settings['payment_methods'] ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_cash">الدفع النقدي</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_bank_transfer" name="payment_methods[]" value="bank_transfer" {{ in_array('bank_transfer', $agency->payment_settings['payment_methods'] ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_bank_transfer">تحويل بنكي</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_credit_card" name="payment_methods[]" value="credit_card" {{ in_array('credit_card', $agency->payment_settings['payment_methods'] ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_credit_card">بطاقة ائتمان</label>
                                    </div>
                                </div>
                                
                                <h5 class="mt-4">معلومات الحساب البنكي</h5>
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">اسم البنك</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $agency->payment_settings['bank_name'] ?? '') }}">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="bank_account_number" class="form-label">رقم الحساب</label>
                                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $agency->payment_settings['bank_account_number'] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="bank_iban" class="form-label">رقم الآيبان</label>
                                            <input type="text" class="form-control" id="bank_iban" name="bank_iban" value="{{ old('bank_iban', $agency->payment_settings['bank_iban'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات الإشعارات -->
                <div class="tab-pane fade" id="notification-settings" role="tabpanel" aria-labelledby="notification-settings-tab">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">إعدادات الإشعارات</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('agency.settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="settings_type" value="notification_settings">
                                
                                <div class="mb-3">
                                    <label class="form-label">وسائل الإشعارات</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" {{ ($agency->notification_settings['email_notifications'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_notifications">إشعارات البريد الإلكتروني</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" value="1" {{ ($agency->notification_settings['sms_notifications'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sms_notifications">إشعارات الرسائل القصيرة (SMS)</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="whatsapp_notifications" name="whatsapp_notifications" value="1" {{ ($agency->notification_settings['whatsapp_notifications'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="whatsapp_notifications">إشعارات واتساب</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">الأحداث التي تستلم إشعارات لها</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="new_request_notification" name="new_request_notification" value="1" {{ ($agency->notification_settings['new_request_notification'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="new_request_notification">طلب خدمة جديد</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="quote_status_notification" name="quote_status_notification" value="1" {{ ($agency->notification_settings['quote_status_notification'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="quote_status_notification">تغيير حالة عرض السعر</label>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```
</copilot-edited-file>
