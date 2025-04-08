@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.requests.index') }}">طلباتي</a></li>
    <li class="breadcrumb-item active">طلب جديد</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-plus-circle me-2"></i> تقديم طلب جديد</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-alt me-1"></i> نموذج الطلب</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('customer.requests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="service_id" class="form-label">الخدمة المطلوبة*</label>
                        <select id="service_id" name="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                            <option value="">-- اختر الخدمة --</option>
                            @foreach(\App\Models\Service::where('agency_id', auth()->user()->agency_id)
                                    ->where('status', 'active')
                                    ->get() as $service)
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
                </div>
                
                <div class="mb-3">
                    <label for="requested_date" class="form-label">التاريخ المطلوب*</label>
                    <input type="date" id="requested_date" name="requested_date" class="form-control @error('requested_date') is-invalid @enderror" value="{{ old('requested_date') ?? date('Y-m-d', strtotime('+7 days')) }}" required>
                    @error('requested_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
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
                
                <!-- قسم الخدمات المحددة -->
                <div id="securityApprovalFields" class="service-fields border p-3 rounded mb-3 d-none">
                    <h5 class="mb-3">معلومات الموافقة الأمنية</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">البلد*</label>
                            <select name="country" class="form-select">
                                <option value="egypt">مصر</option>
                                <option value="jordan">الأردن</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">عدد الأشخاص*</label>
                            <input type="number" name="persons_count" class="form-control" min="1" value="1">
                        </div>
                    </div>
                </div>
                
                <div id="transportationFields" class="service-fields border p-3 rounded mb-3 d-none">
                    <h5 class="mb-3">معلومات النقل البري</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">مكان الانطلاق*</label>
                            <input type="text" name="departure" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الوجهة*</label>
                            <input type="text" name="destination" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">نوع النقل*</label>
                            <select name="transport_type" class="form-select">
                                <option value="vip">VIP</option>
                                <option value="normal">عادي</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">عدد الركاب*</label>
                            <input type="number" name="passengers_count" class="form-control" min="1" value="1">
                        </div>
                    </div>
                </div>
                
                <div id="flightFields" class="service-fields border p-3 rounded mb-3 d-none">
                    <h5 class="mb-3">معلومات تذاكر الطيران</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">مطار المغادرة*</label>
                            <input type="text" name="departure_airport" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">مطار الوصول*</label>
                            <input type="text" name="arrival_airport" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ المغادرة*</label>
                            <input type="date" name="departure_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ العودة (اختياري)</label>
                            <input type="date" name="return_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">درجة الحجز*</label>
                            <select name="flight_class" class="form-select">
                                <option value="economy">اقتصادية</option>
                                <option value="business">رجال الأعمال</option>
                                <option value="first">الدرجة الأولى</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">عدد المسافرين*</label>
                            <input type="number" name="passengers_count" class="form-control" min="1" value="1">
                        </div>
                    </div>
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
                                <input type="file" name="document_files[]" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger w-100 remove-document" disabled>
                                    <i class="fas fa-trash"></i>
                                </button>
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
                            <i class="fas fa-paper-plane me-1"></i> تقديم الطلب
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // إظهار/إخفاء حقول الخدمة المحددة
        const serviceSelect = document.getElementById('service_id');
        const serviceFields = document.querySelectorAll('.service-fields');
        
        serviceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const serviceType = selectedOption.textContent.toLowerCase();
            
            // إخفاء جميع حقول الخدمات
            serviceFields.forEach(field => {
                field.classList.add('d-none');
            });
            
            // إظهار الحقول المناسبة بناءً على نوع الخدمة
            if (serviceType.includes('موافقة') || serviceType.includes('أمنية')) {
                document.getElementById('securityApprovalFields').classList.remove('d-none');
            } else if (serviceType.includes('نقل') || serviceType.includes('بري')) {
                document.getElementById('transportationFields').classList.remove('d-none');
            } else if (serviceType.includes('طيران') || serviceType.includes('تذاكر')) {
                document.getElementById('flightFields').classList.remove('d-none');
            }
        });
        
        // إضافة مستند جديد
        const addDocumentBtn = document.getElementById('addDocument');
        const documentsContainer = document.getElementById('documentsContainer');
        
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
                    <input type="file" name="document_files[]" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger w-100 remove-document">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            documentsContainer.appendChild(documentItem);
            
            // تفعيل زر الحذف للعنصر الأول
            if (documentsContainer.children.length > 1) {
                documentsContainer.querySelector('.remove-document[disabled]').removeAttribute('disabled');
            }
            
            // إضافة حدث لزر الحذف الجديد
            documentItem.querySelector('.remove-document').addEventListener('click', function() {
                documentItem.remove();
                
                // إذا تبقى عنصر واحد فقط، تعطيل زر الحذف
                if (documentsContainer.children.length === 1) {
                    documentsContainer.querySelector('.remove-document').setAttribute('disabled', 'disabled');
                }
            });
        });
    });
</script>
@endsection
