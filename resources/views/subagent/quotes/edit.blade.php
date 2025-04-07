@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subagent.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subagent.quotes.index') }}">عروض الأسعار</a></li>
    <li class="breadcrumb-item active">تعديل عرض سعر</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-edit me-2"></i> تعديل عرض سعر</h2>
        </div>
    </div>
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">معلومات عرض السعر</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('subagent.quotes.update', $quote) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">السعر</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $quote->price) }}" required>
                                <select name="currency_code" class="form-select" style="max-width: 120px;">
                                    @foreach(\App\Models\Currency::where('is_active', true)->get() as $currency)
                                        <option value="{{ $currency->code }}" {{ $quote->currency_code == $currency->code ? 'selected' : '' }}>
                                            {{ $currency->code }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">أدخل السعر النهائي الذي يشمل جميع الرسوم والضرائب</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="details" class="form-label">تفاصيل العرض</label>
                            <textarea class="form-control @error('details') is-invalid @enderror" id="details" name="details" rows="5" required>{{ old('details', $quote->details) }}</textarea>
                            @error('details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">أضف جميع التفاصيل المهمة عن العرض، مثل الوقت، المميزات، الشروط، إلخ.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">إضافة مرفقات (اختياري)</label>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control" name="attachments[]">
                                <input type="text" class="form-control" name="attachment_names[]" placeholder="اسم المرفق">
                            </div>
                            <div id="more-attachments"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-attachment">
                                <i class="fas fa-plus-circle me-1"></i> إضافة مرفق آخر
                            </button>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">المرفقات الحالية</label>
                            <p class="text-muted">لا توجد مرفقات حالية</p>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('subagent.quotes.show', $quote) }}" class="btn btn-secondary me-md-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary">تحديث عرض السعر</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">معلومات الطلب</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title mb-3">{{ $quote->request->service->name }}</h6>
                    <p class="card-text text-muted small">{{ $quote->request->service->description }}</p>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">تفاصيل الطلب</h6>
                        <p>{{ $quote->request->details }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">حالة الطلب</h6>
                        <span class="badge bg-{{ $quote->request->status == 'pending' ? 'warning' : ($quote->request->status == 'in_progress' ? 'info' : 'success') }}">
                            {{ $quote->request->status == 'pending' ? 'قيد الانتظار' : ($quote->request->status == 'in_progress' ? 'قيد التنفيذ' : 'مكتمل') }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">تاريخ الطلب</h6>
                        <p>{{ $quote->request->created_at->format('Y-m-d') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">العميل</h6>
                        <p>{{ $quote->request->customer->name }}</p>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('subagent.requests.show', $quote->request_id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> عرض الطلب الكامل
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // إضافة حقول مرفقات إضافية
        const addAttachmentBtn = document.getElementById('add-attachment');
        const attachmentsContainer = document.getElementById('more-attachments');
        
        addAttachmentBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'input-group mb-2';
            newRow.innerHTML = `
                <input type="file" class="form-control" name="attachments[]">
                <input type="text" class="form-control" name="attachment_names[]" placeholder="اسم المرفق">
                <button type="button" class="btn btn-outline-danger remove-attachment">
                    <i class="fas fa-times"></i>
                </button>
            `;
            attachmentsContainer.appendChild(newRow);
            
            // إضافة وظيفة الإزالة للزر الجديد
            newRow.querySelector('.remove-attachment').addEventListener('click', function() {
                attachmentsContainer.removeChild(newRow);
            });
        });
    });
</script>
@endpush
@endsection
