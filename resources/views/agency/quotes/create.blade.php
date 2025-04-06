@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.quotes.index') }}">إدارة عروض الأسعار</a></li>
    <li class="breadcrumb-item active">إضافة عرض سعر جديد</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-plus-circle me-2"></i> إضافة عرض سعر جديد</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-tag me-1"></i> نموذج عرض السعر</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('agency.quotes.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="request_id" class="form-label">الطلب*</label>
                        <select id="request_id" name="request_id" class="form-select @error('request_id') is-invalid @enderror" required>
                            <option value="">-- اختر الطلب --</option>
                            @foreach($requests as $req)
                                <option value="{{ $req->id }}" {{ (old('request_id') == $req->id || (isset($serviceRequest) && $serviceRequest->id == $req->id)) ? 'selected' : '' }}>
                                    #{{ $req->id }} - {{ $req->service->name }} - {{ $req->customer->name }} - {{ $req->created_at->format('Y-m-d') }}
                                </option>
                            @endforeach
                        </select>
                        @error('request_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="subagent_id" class="form-label">السبوكيل*</label>
                        <select id="subagent_id" name="subagent_id" class="form-select @error('subagent_id') is-invalid @enderror" required>
                            <option value="">-- اختر السبوكيل --</option>
                            @foreach($subagents as $subagent)
                                <option value="{{ $subagent->id }}" {{ old('subagent_id') == $subagent->id ? 'selected' : '' }}>
                                    {{ $subagent->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subagent_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="price" class="form-label">السعر (ر.س)*</label>
                        <input type="number" step="0.01" min="0" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                        @error('price')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="commission_amount" class="form-label">مبلغ العمولة (ر.س)*</label>
                        <input type="number" step="0.01" min="0" id="commission_amount" name="commission_amount" class="form-control @error('commission_amount') is-invalid @enderror" value="{{ old('commission_amount') }}" required>
                        @error('commission_amount')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="details" class="form-label">تفاصيل العرض*</label>
                    <textarea id="details" name="details" class="form-control @error('details') is-invalid @enderror" rows="5" required>{{ old('details') }}</textarea>
                    @error('details')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-save me-1"></i> حفظ عرض السعر
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // حساب العمولة تلقائيًا عند تغيير السعر
        const priceInput = document.getElementById('price');
        const commissionInput = document.getElementById('commission_amount');
        const requestSelect = document.getElementById('request_id');
        
        // تخزين نسب العمولة للخدمات
        const commissionRates = {};
        
        // لو كانت لدينا معلومات الخدمات مسبقًا
        @foreach($requests as $req)
            commissionRates[{{ $req->id }}] = {{ $req->service->commission_rate }};
        @endforeach
        
        // تحديث العمولة بناءً على السعر والخدمة
        function updateCommission() {
            const requestId = requestSelect.value;
            if (requestId && priceInput.value) {
                const price = parseFloat(priceInput.value);
                const rate = commissionRates[requestId] || 0;
                
                // حساب العمولة
                const commission = (price * rate / 100).toFixed(2);
                commissionInput.value = commission;
            }
        }
        
        // ربط الأحداث
        priceInput.addEventListener('input', updateCommission);
        requestSelect.addEventListener('change', updateCommission);
        
        // تنفيذ عند تحميل الصفحة إذا كانت هناك قيم موجودة
        if (priceInput.value && requestSelect.value) {
            updateCommission();
        }
    });
</script>
@endsection
