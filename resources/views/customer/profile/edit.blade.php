@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">الملف الشخصي</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-user-edit me-2"></i> الملف الشخصي</h2>
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
                    <h5 class="mb-0">أقسام الملف الشخصي</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush profile-menu">
                        <a href="#personal-info" class="list-group-item list-group-item-action active">
                            <i class="fas fa-user me-2"></i> المعلومات الشخصية
                        </a>
                        <a href="#contact-info" class="list-group-item list-group-item-action">
                            <i class="fas fa-address-card me-2"></i> معلومات الاتصال
                        </a>
                        <a href="#password" class="list-group-item list-group-item-action">
                            <i class="fas fa-lock me-2"></i> تغيير كلمة المرور
                        </a>
                        <a href="#preferences" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog me-2"></i> التفضيلات
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <!-- المعلومات الشخصية -->
            <div id="personal-info" class="card shadow mb-4 profile-section active">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-1"></i> المعلومات الشخصية</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="update_type" value="personal_info">
                        
                        <div class="row">
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم الكامل</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="img-thumbnail rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="avatar-placeholder rounded-circle mb-2 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background-color: #e9ecef; margin: 0 auto;">
                                            <span class="display-4 text-muted">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <input type="file" class="form-control form-control-sm" id="avatar" name="avatar" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_number" class="form-label">رقم الهوية/الإقامة</label>
                                    <input type="text" class="form-control @error('id_number') is-invalid @enderror" id="id_number" name="id_number" value="{{ old('id_number', auth()->user()->id_number) }}">
                                    @error('id_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="passport_number" class="form-label">رقم جواز السفر</label>
                                    <input type="text" class="form-control @error('passport_number') is-invalid @enderror" id="passport_number" name="passport_number" value="{{ old('passport_number', auth()->user()->passport_number) }}">
                                    @error('passport_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nationality" class="form-label">الجنسية</label>
                            <select class="form-select @error('nationality') is-invalid @enderror" id="nationality" name="nationality">
                                <option value="">-- اختر الجنسية --</option>
                                <option value="saudi" {{ auth()->user()->nationality == 'saudi' ? 'selected' : '' }}>سعودي</option>
                                <option value="yemeni" {{ auth()->user()->nationality == 'yemeni' ? 'selected' : '' }}>يمني</option>
                                <option value="egyptian" {{ auth()->user()->nationality == 'egyptian' ? 'selected' : '' }}>مصري</option>
                                <option value="jordanian" {{ auth()->user()->nationality == 'jordanian' ? 'selected' : '' }}>أردني</option>
                                <option value="other" {{ auth()->user()->nationality == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('nationality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- معلومات الاتصال -->
            <div id="contact-info" class="card shadow mb-4 profile-section">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-address-card me-1"></i> معلومات الاتصال</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="update_type" value="contact_info">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', auth()->user()->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">المدينة</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', auth()->user()->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country" class="form-label">الدولة</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', auth()->user()->country) }}">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- تغيير كلمة المرور -->
            <div id="password" class="card shadow mb-4 profile-section">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-lock me-1"></i> تغيير كلمة المرور</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="update_type" value="password">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- التفضيلات -->
            <div id="preferences" class="card shadow mb-4 profile-section">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-1"></i> التفضيلات</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="update_type" value="preferences">
                        
                        <div class="mb-3">
                            <label for="preferred_currency" class="form-label">العملة المفضلة</label>
                            <select class="form-select @error('preferred_currency') is-invalid @enderror" id="preferred_currency" name="preferred_currency">
                                @foreach(\App\Models\Currency::where('is_active', true)->get() as $currency)
                                    <option value="{{ $currency->code }}" {{ auth()->user()->preferred_currency == $currency->code ? 'selected' : '' }}>
                                        {{ $currency->name }} ({{ $currency->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('preferred_currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">تفضيلات الإشعارات</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="email_notifications" name="notification_preferences[]" value="email" {{ in_array('email', auth()->user()->notification_preferences ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">إشعارات البريد الإلكتروني</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sms_notifications" name="notification_preferences[]" value="sms" {{ in_array('sms', auth()->user()->notification_preferences ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="sms_notifications">الرسائل النصية (SMS)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="push_notifications" name="notification_preferences[]" value="push" {{ in_array('push', auth()->user()->notification_preferences ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="push_notifications">الإشعارات الفورية</label>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">حفظ التفضيلات</button>
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
        // التبديل بين أقسام الملف الشخصي
        const profileLinks = document.querySelectorAll('.profile-menu a');
        const profileSections = document.querySelectorAll('.profile-section');
        
        profileLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // إزالة الكلاس النشط من جميع الروابط
                profileLinks.forEach(item => item.classList.remove('active'));
                
                // إضافة الكلاس النشط للرابط المحدد
                this.classList.add('active');
                
                // إخفاء جميع الأقسام
                profileSections.forEach(section => section.classList.remove('active'));
                
                // إظهار القسم المطلوب
                const targetId = this.getAttribute('href').substring(1);
                document.getElementById(targetId).classList.add('active');
            });
        });
    });
</script>
@endpush

<style>
    .profile-section {
        display: none;
    }
    .profile-section.active {
        display: block;
    }
</style>
@endsection
