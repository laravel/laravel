@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item active">الملف الشخصي</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-user-circle me-2"></i> الملف الشخصي</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- بطاقة المعلومات الشخصية -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> معلوماتك الشخصية</h5>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto mb-3">
                        <span class="avatar-text">{{ substr(auth()->user()->name, 0, 2) }}</span>
                    </div>
                    <h3>{{ auth()->user()->name }}</h3>
                    <p class="text-muted">
                        @if(auth()->user()->isAgency())
                            وكيل أساسي
                        @elseif(auth()->user()->isSubagent())
                            سبوكيل
                        @else
                            عميل
                        @endif
                    </p>
                    <hr>
                    <div class="row text-start">
                        <div class="col-12 mb-2">
                            <strong>البريد الإلكتروني:</strong> {{ auth()->user()->email }}
                        </div>
                        <div class="col-12 mb-2">
                            <strong>رقم الهاتف:</strong> {{ auth()->user()->phone }}
                        </div>
                        <div class="col-12 mb-2">
                            <strong>تاريخ الإنضمام:</strong> {{ auth()->user()->created_at->format('Y-m-d') }}
                        </div>
                        @if(auth()->user()->isAgency())
                            <div class="col-12 mb-2">
                                <strong>اسم الوكالة:</strong> {{ auth()->user()->agency->name }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            @if(auth()->user()->isAgency())
                <!-- بطاقة معلومات الوكالة -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-building me-1"></i> معلومات الوكالة</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>اسم الوكالة:</span>
                                <strong>{{ auth()->user()->agency->name }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>البريد الإلكتروني:</span>
                                <strong>{{ auth()->user()->agency->email }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>رقم الهاتف:</span>
                                <strong>{{ auth()->user()->agency->phone }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>العنوان:</span>
                                <strong>{{ auth()->user()->agency->address ?? 'غير محدد' }}</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <!-- بطاقة تعديل المعلومات الشخصية -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-1"></i> تعديل المعلومات الشخصية</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">الاسم الكامل</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i> حفظ التغييرات
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- بطاقة تغيير كلمة المرور -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-key me-1"></i> تغيير كلمة المرور</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-key me-1"></i> تغيير كلمة المرور
                        </button>
                    </form>
                </div>
            </div>
            
            @if(auth()->user()->isAgency())
                <!-- بطاقة تعديل معلومات الوكالة -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-building me-1"></i> تعديل معلومات الوكالة</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('agency.update-info') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="agency_name" class="form-label">اسم الوكالة</label>
                                    <input type="text" class="form-control @error('agency_name') is-invalid @enderror" id="agency_name" name="agency_name" value="{{ old('agency_name', auth()->user()->agency->name) }}" required>
                                    @error('agency_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="agency_email" class="form-label">البريد الإلكتروني للوكالة</label>
                                    <input type="email" class="form-control @error('agency_email') is-invalid @enderror" id="agency_email" name="agency_email" value="{{ old('agency_email', auth()->user()->agency->email) }}" required>
                                    @error('agency_email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="agency_phone" class="form-label">رقم هاتف الوكالة</label>
                                    <input type="text" class="form-control @error('agency_phone') is-invalid @enderror" id="agency_phone" name="agency_phone" value="{{ old('agency_phone', auth()->user()->agency->phone) }}" required>
                                    @error('agency_phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="agency_logo" class="form-label">شعار الوكالة (اختياري)</label>
                                    <input type="file" class="form-control @error('agency_logo') is-invalid @enderror" id="agency_logo" name="agency_logo">
                                    @error('agency_logo')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="agency_address" class="form-label">عنوان الوكالة</label>
                                <textarea class="form-control @error('agency_address') is-invalid @enderror" id="agency_address" name="agency_address" rows="2">{{ old('agency_address', auth()->user()->agency->address) }}</textarea>
                                @error('agency_address')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> حفظ معلومات الوكالة
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 100px;
    height: 100px;
    background-color: #0d6efd;
    text-align: center;
    border-radius: 50%;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
}

.avatar-text {
    position: relative;
    top: 25px;
    font-size: 40px;
    line-height: 50px;
    color: #fff;
    text-transform: uppercase;
    font-weight: bold;
}
</style>
@endsection
