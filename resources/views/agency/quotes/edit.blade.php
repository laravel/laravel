@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.quotes.index') }}">عروض الأسعار</a></li>
    <li class="breadcrumb-item active">تعديل عرض سعر #{{ $quote->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-edit me-2"></i> تعديل عرض سعر #{{ $quote->id }}</h2>
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
                    <h5 class="mb-0"><i class="fas fa-tag me-1"></i> تفاصيل عرض السعر</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.quotes.update', $quote) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subagent_id" class="form-label">السبوكيل</label>
                                    <select class="form-select @error('subagent_id') is-invalid @enderror" id="subagent_id" name="subagent_id" required {{ $quote->status == 'customer_approved' ? 'disabled' : '' }}>
                                        @foreach($subagents as $subagent)
                                            <option value="{{ $subagent->id }}" {{ $quote->subagent_id == $subagent->id ? 'selected' : '' }}>
                                                {{ $subagent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subagent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($quote->status == 'customer_approved')
                                        <small class="text-muted">لا يمكن تغيير السبوكيل لأن العرض تم قبوله من قبل العميل.</small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">السعر</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $quote->price) }}" required {{ $quote->status == 'customer_approved' ? 'disabled' : '' }}>
                                        <span class="input-group-text">{{ $quote->currency_code }}</span>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($quote->status == 'customer_approved')
                                        <small class="text-muted">لا يمكن تغيير السعر لأن العرض تم قبوله من قبل العميل.</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="details" class="form-label">تفاصيل العرض</label>
                            <textarea class="form-control @error('details') is-invalid @enderror" id="details" name="details" rows="5" {{ $quote->status == 'customer_approved' ? 'disabled' : '' }}>{{ old('details', $quote->details) }}</textarea>
                            @error('details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($quote->status == 'customer_approved')
                                <small class="text-muted">لا يمكن تغيير التفاصيل لأن العرض تم قبوله من قبل العميل.</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">حالة العرض</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ $quote->status == 'pending' ? 'selected' : '' }}>بانتظار المراجعة</option>
                                <option value="agency_approved" {{ $quote->status == 'agency_approved' ? 'selected' : '' }}>موافقة الوكالة</option>
                                <option value="agency_rejected" {{ $quote->status == 'agency_rejected' ? 'selected' : '' }}>رفض الوكالة</option>
                                @if($quote->status == 'customer_approved')
                                    <option value="customer_approved" selected>موافق عليه من العميل</option>
                                @endif
                                @if($quote->status == 'customer_rejected')
                                    <option value="customer_rejected" selected>مرفوض من العميل</option>
                                @endif
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="agency_comment" class="form-label">ملاحظات الوكالة (مرئية فقط للسبوكيل)</label>
                            <textarea class="form-control @error('agency_comment') is-invalid @enderror" id="agency_comment" name="agency_comment" rows="3">{{ old('agency_comment', $quote->agency_comment ?? '') }}</textarea>
                            @error('agency_comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('agency.quotes.show', $quote) }}" class="btn btn-secondary me-md-2">إلغاء</a>
                            @if($quote->status != 'customer_approved' && $quote->status != 'customer_rejected')
                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> معلومات الطلب</h5>
                </div>
                <div class="card-body">
                    <p><strong>الخدمة:</strong> {{ $quote->request->service->name }}</p>
                    <p><strong>العميل:</strong> {{ $quote->request->customer->name }}</p>
                    <p><strong>تاريخ الطلب:</strong> {{ $quote->request->created_at->format('Y-m-d') }}</p>
                    <p><strong>الحالة:</strong> 
                        @if($quote->request->status == 'pending')
                            <span class="badge bg-warning">قيد الانتظار</span>
                        @elseif($quote->request->status == 'in_progress')
                            <span class="badge bg-info">قيد التنفيذ</span>
                        @elseif($quote->request->status == 'completed')
                            <span class="badge bg-success">مكتمل</span>
                        @elseif($quote->request->status == 'cancelled')
                            <span class="badge bg-danger">ملغي</span>
                        @endif
                    </p>
                    
                    <div class="mt-3">
                        <h6>تفاصيل الطلب:</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $quote->request->details }}
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('agency.requests.show', $quote->request) }}" class="btn btn-info w-100">
                            <i class="fas fa-eye me-1"></i> عرض الطلب
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-calculator me-1"></i> تفاصيل مالية</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">السعر المقدم:</label>
                        <p class="form-control-static">{{ $quote->price }} {{ $quote->currency_code }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">العمولة ({{ $quote->commission_rate ?? '15' }}%):</label>
                        <p class="form-control-static">{{ $quote->commission_amount }} {{ $quote->currency_code }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">صافي الربح للسبوكيل:</label>
                        <p class="form-control-static fw-bold">{{ $quote->price - $quote->commission_amount }} {{ $quote->currency_code }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
