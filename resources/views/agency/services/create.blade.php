@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.services.index') }}">إدارة الخدمات</a></li>
    <li class="breadcrumb-item active">إضافة خدمة جديدة</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-plus-circle me-2"></i> إضافة خدمة جديدة</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> معلومات الخدمة</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('agency.services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">اسم الخدمة*</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="type" class="form-label">نوع الخدمة*</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="" selected disabled>-- اختر نوع الخدمة --</option>
                            <option value="security_approval" {{ old('type') == 'security_approval' ? 'selected' : '' }}>موافقة أمنية</option>
                            <option value="transportation" {{ old('type') == 'transportation' ? 'selected' : '' }}>نقل بري</option>
                            <option value="hajj_umrah" {{ old('type') == 'hajj_umrah' ? 'selected' : '' }}>حج وعمرة</option>
                            <option value="flight" {{ old('type') == 'flight' ? 'selected' : '' }}>تذاكر طيران</option>
                            <option value="passport" {{ old('type') == 'passport' ? 'selected' : '' }}>إصدار جوازات</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="base_price" class="form-label">السعر الأساسي</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control @error('base_price') is-invalid @enderror" id="base_price" name="base_price" value="{{ old('base_price') }}" required>
                                <select class="form-select" name="currency_code" id="currency_code">
                                    @foreach(\App\Models\Currency::where('is_active', true)->get() as $currency)
                                        <option value="{{ $currency->code }}" {{ $currency->is_default ? 'selected' : '' }}>
                                            {{ $currency->code }} ({{ $currency->symbol }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('base_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="commission_rate" class="form-label">نسبة العمولة (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" min="0" max="100" class="form-control @error('commission_rate') is-invalid @enderror" id="commission_rate" name="commission_rate" value="{{ old('commission_rate', auth()->user()->agency->default_commission_rate ?? 10) }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('commission_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">وصف الخدمة*</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">الحالة*</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label">صورة الخدمة (اختياري)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                        @error('image')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 offset-md-3 mt-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> حفظ الخدمة
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // توضيح نسبة العمولة عند تغيير السعر الأساسي
        const basePrice = document.getElementById('base_price');
        const commissionRate = document.getElementById('commission_rate');
        
        function updateCommissionExample() {
            const price = parseFloat(basePrice.value) || 0;
            const rate = parseFloat(commissionRate.value) || 0;
            const commission = (price * rate / 100).toFixed(2);
            
            const example = document.getElementById('commission-example');
            if (!example) {
                const newExample = document.createElement('div');
                newExample.id = 'commission-example';
                newExample.className = 'form-text text-muted';
                newExample.innerHTML = `مثال: العمولة على خدمة بقيمة ${price} ر.س ستكون ${commission} ر.س (${rate}%)`;
                commissionRate.parentNode.appendChild(newExample);
            } else {
                example.innerHTML = `مثال: العمولة على خدمة بقيمة ${price} ر.س ستكون ${commission} ر.س (${rate}%)`;
            }
        }
        
        basePrice.addEventListener('input', updateCommissionExample);
        commissionRate.addEventListener('input', updateCommissionExample);
        
        // تنفيذ عند تحميل الصفحة إذا كانت هناك قيم موجودة
        if (basePrice.value && commissionRate.value) {
            updateCommissionExample();
        }
    });
</script>
@endsection
