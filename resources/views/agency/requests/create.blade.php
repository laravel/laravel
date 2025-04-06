@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.requests.index') }}">إدارة الطلبات</a></li>
    <li class="breadcrumb-item active">إضافة طلب جديد</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-plus-circle me-2"></i> إضافة طلب جديد</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-alt me-1"></i> نموذج الطلب</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('agency.requests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="service_id" class="form-label">الخدمة المطلوبة*</label>
                        <select id="service_id" name="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                            <option value="">-- اختر الخدمة --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }} ({{ $service->base_price }} ر.س)
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">العميل*</label>
                        <select id="customer_id" name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">-- اختر العميل --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->phone }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="priority" class="form-label">الأولوية*</label>
                        <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>عادي</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>مستعجل</option>
                            <option value="emergency" {{ old('priority') == 'emergency' ? 'selected' : '' }}>طارئ</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="requested_date" class="form-label">التاريخ المطلوب*</label>
                        <input type="date" id="requested_date" name="requested_date" class="form-control @error('requested_date') is-invalid @enderror" value="{{ old('requested_date') ?? date('Y-m-d', strtotime('+7 days')) }}" required>
                        @error('requested_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="details" class="form-label">تفاصيل الطلب*</label>
                    <textarea id="details" name="details" class="form-control @error('details') is-invalid @enderror" rows="5" required>{{ old('details') }}</textarea>
                    @error('details')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <!-- حقل إضافة المستندات -->
                <div class="mb-3 border p-3 rounded">
                    <h5 class="mb-3">المستندات المرفقة (اختياري)</h5>
                    <div id="documentsContainer">
                        <div class="document-item row mb-3">
                            <div class="col-md-5">
                                <label class="form-label">اسم المستند</label>
                                <input type="text" name="document_names[]" class="form-control">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">الملف</label>
                                <input type="file" name="documents[]" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">الرؤية</label>
                                <select name="document_visibility[]" class="form-select">
                                    <option value="private">خاص</option>
                                    <option value="public">عام</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-info mt-2" id="addDocument">
                        <i class="fas fa-plus-circle me-1"></i> إضافة مستند آخر
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-save me-1"></i> حفظ الطلب
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // إضافة مستند جديد
        const addDocumentBtn = document.getElementById('addDocument');
        const documentsContainer = document.getElementById('documentsContainer');
        
        if (addDocumentBtn) {
            addDocumentBtn.addEventListener('click', function() {
                const documentItem = document.createElement('div');
                documentItem.className = 'document-item row mb-3';
                documentItem.innerHTML = `
                    <div class="col-md-5">
                        <label class="form-label">اسم المستند</label>
                        <input type="text" name="document_names[]" class="form-control">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">الملف</label>
                        <input type="file" name="documents[]" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الرؤية</label>
                        <select name="document_visibility[]" class="form-select">
                            <option value="private">خاص</option>
                            <option value="public">عام</option>
                        </select>
                    </div>
                    <div class="col-12 mt-2">
                        <button type="button" class="btn btn-sm btn-danger remove-document">
                            <i class="fas fa-trash me-1"></i> حذف
                        </button>
                    </div>
                `;
                
                documentsContainer.appendChild(documentItem);
                
                // إضافة حدث لزر الحذف
                const removeBtn = documentItem.querySelector('.remove-document');
                removeBtn.addEventListener('click', function() {
                    documentItem.remove();
                });
            });
        }
    });
</script>
@endsection
