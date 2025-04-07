@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.requests.index') }}">الطلبات</a></li>
    <li class="breadcrumb-item active">تعديل الطلب #{{ $request->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-edit me-2"></i> تعديل الطلب #{{ $request->id }}</h2>
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
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-1"></i> بيانات الطلب</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.requests.update', $request) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_id" class="form-label">الخدمة</label>
                                    <select class="form-select @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required {{ $request->status == 'completed' ? 'disabled' : '' }}>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ $request->service_id == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($request->status == 'completed')
                                        <small class="text-muted">لا يمكن تغيير الخدمة لطلب مكتمل.</small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">العميل</label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required {{ $request->status == 'completed' ? 'disabled' : '' }}>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ $request->customer_id == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($request->status == 'completed')
                                        <small class="text-muted">لا يمكن تغيير العميل لطلب مكتمل.</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="details" class="form-label">تفاصيل الطلب</label>
                            <textarea class="form-control @error('details') is-invalid @enderror" id="details" name="details" rows="5" required>{{ old('details', $request->details) }}</textarea>
                            @error('details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">الأولوية</label>
                                    <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                        <option value="normal" {{ $request->priority == 'normal' ? 'selected' : '' }}>عادي</option>
                                        <option value="urgent" {{ $request->priority == 'urgent' ? 'selected' : '' }}>عاجل</option>
                                        <option value="emergency" {{ $request->priority == 'emergency' ? 'selected' : '' }}>طارئ</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">الحالة</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="in_progress" {{ $request->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                        <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                        <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="requested_date" class="form-label">التاريخ المطلوب</label>
                                    <input type="datetime-local" class="form-control @error('requested_date') is-invalid @enderror" id="requested_date" name="requested_date" value="{{ old('requested_date', date('Y-m-d\TH:i', strtotime($request->requested_date))) }}">
                                    @error('requested_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="agency_notes" class="form-label">ملاحظات الوكالة (داخلية فقط)</label>
                            <textarea class="form-control @error('agency_notes') is-invalid @enderror" id="agency_notes" name="agency_notes" rows="3">{{ old('agency_notes', $request->agency_notes ?? '') }}</textarea>
                            @error('agency_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">هذه الملاحظات داخلية فقط ولن تظهر للعميل.</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('agency.requests.show', $request) }}" class="btn btn-secondary me-md-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> معلومات إضافية</h5>
                </div>
                <div class="card-body">
                    <p><strong>تاريخ إنشاء الطلب:</strong> {{ $request->created_at->format('Y-m-d H:i') }}</p>
                    <p><strong>آخر تحديث:</strong> {{ $request->updated_at->format('Y-m-d H:i') }}</p>
                    
                    @if($request->quotes->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i> يوجد {{ $request->quotes->count() }} عرض سعر مرتبط بهذا الطلب. التغييرات قد تؤثر على عروض الأسعار المقدمة.
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('agency.requests.show', $request) }}" class="btn btn-info w-100">
                            <i class="fas fa-eye me-1"></i> عرض الطلب
                        </a>
                    </div>
                </div>
            </div>
            
            @if($request->quotes->count() > 0)
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-tags me-1"></i> عروض الأسعار</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($request->quotes as $quote)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $quote->subagent->name }}</strong><br>
                                        <small>{{ $quote->price }} {{ $quote->currency_code }}</small>
                                    </div>
                                    <span class="badge 
                                        @if($quote->status == 'pending') bg-warning 
                                        @elseif($quote->status == 'agency_approved') bg-info 
                                        @elseif($quote->status == 'agency_rejected') bg-danger 
                                        @elseif($quote->status == 'customer_approved') bg-success 
                                        @elseif($quote->status == 'customer_rejected') bg-danger 
                                        @endif">
                                        @if($quote->status == 'pending')
                                            بانتظار المراجعة
                                        @elseif($quote->status == 'agency_approved')
                                            معتمد من الوكالة
                                        @elseif($quote->status == 'agency_rejected')
                                            مرفوض من الوكالة
                                        @elseif($quote->status == 'customer_approved')
                                            مقبول من العميل
                                        @elseif($quote->status == 'customer_rejected')
                                            مرفوض من العميل
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        
                        <div class="mt-3">
                            <a href="{{ route('agency.quotes.index', ['request_id' => $request->id]) }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-search me-1"></i> عرض كل العروض
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
