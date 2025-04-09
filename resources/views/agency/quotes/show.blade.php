@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.quotes.index') }}">عروض الأسعار</a></li>
    <li class="breadcrumb-item active">عرض سعر #{{ $quote->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-1">عرض سعر #{{ $quote->id }}</h2>
            <div class="d-flex align-items-center">
                <span class="badge bg-{{ $quote->status_badge }} me-2">{{ $quote->status_text }}</span>
                <span class="text-muted">تاريخ التقديم: {{ $quote->created_at->format('Y-m-d') }}</span>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            @if($quote->status == 'pending')
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="fas fa-check me-1"></i> الموافقة على العرض
                </button>
                <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times me-1"></i> رفض العرض
                </button>
                
                <!-- Modal تأكيد الموافقة -->
                <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">تأكيد الموافقة على عرض السعر</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>هل أنت متأكد من الموافقة على عرض السعر هذا؟</p>
                                <p>سيتم عرض هذا العرض للعميل للموافقة عليه.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                <form action="{{ route('agency.quotes.approve', $quote) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">تأكيد الموافقة</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal تأكيد الرفض -->
                <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">رفض عرض السعر</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('agency.quotes.reject', $quote) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="rejection_reason" class="form-label">سبب الرفض</label>
                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                        <small class="form-text text-muted">سيتم إرسال هذا السبب إلى السبوكيل</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
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
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">تفاصيل عرض السعر</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">السعر</h6>
                            <p class="h4 text-primary">{{ number_format($quote->price, 2) }} {{ $quote->currency_code }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">العمولة</h6>
                            <p>{{ number_format($quote->commission_amount, 2) }} {{ $quote->currency_code }} 
                               ({{ number_format(($quote->commission_amount / $quote->price) * 100, 1) }}%)</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">تفاصيل العرض</h6>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($quote->details)) !!}
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">الحالة</h6>
                        <div>
                            @if($quote->status == 'pending')
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-1"></i> عرض السعر بانتظار مراجعتك والموافقة عليه.
                                </div>
                            @elseif($quote->status == 'agency_approved')
                                <div class="alert alert-info">
                                    <i class="fas fa-check me-1"></i> تمت الموافقة على العرض من قبل الوكالة وهو معروض للعميل حالياً.
                                </div>
                            @elseif($quote->status == 'customer_approved')
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-1"></i> تم قبول العرض من قبل العميل!
                                </div>
                            @elseif($quote->status == 'agency_rejected')
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle me-1"></i> تم رفض العرض من قبل الوكالة.
                                    @if($quote->rejection_reason)
                                        <p class="mb-0 mt-2">
                                            <strong>سبب الرفض:</strong> {{ $quote->rejection_reason }}
                                        </p>
                                    @endif
                                </div>
                            @elseif($quote->status == 'customer_rejected')
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle me-1"></i> تم رفض العرض من قبل العميل.
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if(isset($quote->attachments) && method_exists($quote->attachments, 'count') && $quote->attachments->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">المرفقات</h6>
                            <div class="list-group">
                                @foreach($quote->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment->file_path) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank">
                                        <div>
                                            <i class="fas fa-file me-2"></i>
                                            {{ $attachment->name }}
                                        </div>
                                        <span class="badge bg-primary rounded-pill">
                                            <i class="fas fa-download"></i>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">معلومات السبوكيل</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title mb-3">{{ $quote->subagent->name }}</h6>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">البريد الإلكتروني</h6>
                        <p class="mb-0">{{ $quote->subagent->email }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">رقم الهاتف</h6>
                        <p class="mb-0">{{ $quote->subagent->phone ?? 'غير متوفر' }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">نسبة العمولة</h6>
                        <p class="mb-0">{{ $quote->subagent->commission_rate ?? 10 }}%</p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">معلومات الطلب</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title mb-3">{{ $quote->request->service->name }}</h6>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">رقم الطلب</h6>
                        <p class="mb-0">#{{ $quote->request_id }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">العميل</h6>
                        <p class="mb-0">{{ $quote->request->customer->name }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">تفاصيل الطلب</h6>
                        <p>{{ $quote->request->details }}</p>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('agency.requests.show', $quote->request_id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> عرض الطلب الكامل
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
