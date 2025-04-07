@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.quotes.index') }}">عروض الأسعار</a></li>
    <li class="breadcrumb-item active">عرض #{{ $quote->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-tag me-2"></i> عرض سعر #{{ $quote->id }}</h2>
            <p class="text-muted">تم تقديمه بواسطة: {{ $quote->subagent->name }} في {{ $quote->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group" role="group">
                @if($quote->status == 'pending')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="fas fa-check me-1"></i> موافقة
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times me-1"></i> رفض
                    </button>
                @endif
                
                @if($quote->status != 'customer_approved' && $quote->status != 'customer_rejected')
                    <a href="{{ route('agency.quotes.edit', $quote) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> تعديل
                    </a>
                @endif
                
                <a href="{{ route('agency.quotes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> الرجوع
                </a>
            </div>
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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> تفاصيل العرض</h5>
                    <span class="badge 
                        @if($quote->status == 'pending') bg-warning 
                        @elseif($quote->status == 'agency_approved') bg-info 
                        @elseif($quote->status == 'agency_rejected') bg-danger 
                        @elseif($quote->status == 'customer_approved') bg-success 
                        @elseif($quote->status == 'customer_rejected') bg-danger 
                        @endif px-3 py-2">
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
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">معلومات العرض</h6>
                            <table class="table table-striped">
                                <tr>
                                    <th>رقم العرض</th>
                                    <td>#{{ $quote->id }}</td>
                                </tr>
                                <tr>
                                    <th>السبوكيل</th>
                                    <td>{{ $quote->subagent->name }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ التقديم</th>
                                    <td>{{ $quote->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>آخر تحديث</th>
                                    <td>{{ $quote->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="fw-bold">المعلومات المالية</h6>
                            <table class="table table-striped">
                                <tr>
                                    <th>السعر</th>
                                    <td>{{ $quote->price }} {{ $quote->currency_code }}</td>
                                </tr>
                                <tr>
                                    <th>نسبة العمولة</th>
                                    <td>{{ $quote->commission_rate ?? '10' }}%</td>
                                </tr>
                                <tr>
                                    <th>مبلغ العمولة</th>
                                    <td>{{ $quote->commission_amount }} {{ $quote->currency_code }}</td>
                                </tr>
                                <tr>
                                    <th>صافي للسبوكيل</th>
                                    <td>{{ $quote->price - $quote->commission_amount }} {{ $quote->currency_code }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold">تفاصيل العرض</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $quote->details }}
                        </div>
                    </div>
                    
                    @if(!empty($quote->agency_comment))
                        <div class="mb-4">
                            <h6 class="fw-bold">ملاحظات الوكالة (ظاهرة للسبوكيل فقط)</h6>
                            <div class="p-3 bg-light border-start border-5 border-warning rounded">
                                {{ $quote->agency_comment }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-1"></i> معلومات الطلب</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>رقم الطلب:</strong> <a href="{{ route('agency.requests.show', $quote->request) }}">#{{ $quote->request_id }}</a></p>
                            <p><strong>الخدمة:</strong> {{ $quote->request->service->name }}</p>
                            <p><strong>العميل:</strong> <a href="{{ route('agency.customers.show', $quote->request->customer) }}">{{ $quote->request->customer->name }}</a></p>
                            <p><strong>تاريخ الطلب:</strong> {{ $quote->request->created_at->format('Y-m-d') }}</p>
                            <p><strong>التاريخ المطلوب:</strong> {{ date('Y-m-d', strtotime($quote->request->requested_date)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>حالة الطلب:</strong> 
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
                            <p><strong>الأولوية:</strong> 
                                @if($quote->request->priority == 'normal')
                                    <span class="badge bg-secondary">عادي</span>
                                @elseif($quote->request->priority == 'urgent')
                                    <span class="badge bg-warning">عاجل</span>
                                @elseif($quote->request->priority == 'emergency')
                                    <span class="badge bg-danger">طارئ</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold">تفاصيل الطلب</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $quote->request->details }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-1"></i> سجل الحالة</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-badge bg-success"><i class="fas fa-plus"></i></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">تم تقديم العرض</h6>
                                <p class="mb-0 text-muted">{{ $quote->created_at->format('Y-m-d H:i') }}</p>
                                <small>قام {{ $quote->subagent->name }} بتقديم عرض سعر جديد.</small>
                            </div>
                        </li>
                        
                        @if($quote->status != 'pending')
                            <li class="timeline-item">
                                <div class="timeline-badge {{ in_array($quote->status, ['agency_approved', 'customer_approved']) ? 'bg-success' : 'bg-danger' }}">
                                    <i class="{{ in_array($quote->status, ['agency_approved', 'customer_approved']) ? 'fas fa-check' : 'fas fa-times' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">
                                        @if($quote->status == 'agency_approved')
                                            تمت الموافقة على العرض
                                        @elseif($quote->status == 'agency_rejected')
                                            تم رفض العرض
                                        @elseif($quote->status == 'customer_approved')
                                            تم قبول العرض من العميل
                                        @elseif($quote->status == 'customer_rejected')
                                            تم رفض العرض من العميل
                                        @endif
                                    </h6>
                                    <p class="mb-0 text-muted">{{ $quote->updated_at->format('Y-m-d H:i') }}</p>
                                    @if($quote->status == 'customer_approved')
                                        <small>قام العميل {{ $quote->request->customer->name }} بقبول هذا العرض.</small>
                                    @elseif($quote->status == 'customer_rejected')
                                        <small>قام العميل {{ $quote->request->customer->name }} برفض هذا العرض.</small>
                                    @endif
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-1"></i> معلومات السبوكيل</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="user-avatar me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            {{ substr($quote->subagent->name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $quote->subagent->name }}</h5>
                            <p class="text-muted mb-0">{{ $quote->subagent->email }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <p><strong>رقم الهاتف:</strong> {{ $quote->subagent->phone ?? 'غير متوفر' }}</p>
                        <p><strong>تاريخ الانضمام:</strong> {{ $quote->subagent->created_at->format('Y-m-d') }}</p>
                    </div>
                    
                    <a href="{{ route('agency.subagents.show', $quote->subagent) }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-user me-1"></i> عرض ملف السبوكيل
                    </a>
                </div>
            </div>
            
            @if($quote->request->quotes->count() > 1)
                <div class="card shadow">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-tags me-1"></i> عروض أخرى للطلب</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($quote->request->quotes as $otherQuote)
                                @if($otherQuote->id != $quote->id)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-0"><strong>{{ $otherQuote->subagent->name }}</strong></p>
                                            <small>{{ $otherQuote->price }} {{ $otherQuote->currency_code }}</small>
                                        </div>
                                        <div>
                                            <span class="badge 
                                                @if($otherQuote->status == 'pending') bg-warning 
                                                @elseif($otherQuote->status == 'agency_approved') bg-info 
                                                @elseif($otherQuote->status == 'agency_rejected') bg-danger 
                                                @elseif($otherQuote->status == 'customer_approved') bg-success 
                                                @elseif($otherQuote->status == 'customer_rejected') bg-danger 
                                                @endif">
                                                @if($otherQuote->status == 'pending')
                                                    معلق
                                                @elseif($otherQuote->status == 'agency_approved')
                                                    معتمد
                                                @elseif($otherQuote->status == 'agency_rejected')
                                                    مرفوض
                                                @elseif($otherQuote->status == 'customer_approved')
                                                    مقبول
                                                @elseif($otherQuote->status == 'customer_rejected')
                                                    مرفوض
                                                @endif
                                            </span>
                                            <a href="{{ route('agency.quotes.show', $otherQuote) }}" class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal تأكيد الموافقة -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الموافقة على العرض</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من الموافقة على عرض السعر هذا؟</p>
                <p>سيتم عرضه للعميل بعد الموافقة.</p>
                
                <form id="approveForm" action="{{ route('agency.quotes.approve', $quote) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="agency_comment" class="form-label">إضافة ملاحظة (اختياري)</label>
                        <textarea class="form-control" name="agency_comment" id="agency_comment" rows="3" placeholder="أدخل ملاحظاتك هنا...">{{ $quote->agency_comment }}</textarea>
                        <small class="form-text text-muted">هذه الملاحظة ستكون مرئية للسبوكيل فقط، ولن تظهر للعميل.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success" onclick="document.getElementById('approveForm').submit();">تأكيد الموافقة</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal تأكيد الرفض -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد رفض العرض</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رفض عرض السعر هذا؟</p>
                
                <form id="rejectForm" action="{{ route('agency.quotes.reject', $quote) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">سبب الرفض</label>
                        <textarea class="form-control" name="agency_comment" id="rejection_reason" rows="3" placeholder="أدخل سبب الرفض هنا..." required>{{ $quote->agency_comment }}</textarea>
                        <small class="form-text text-muted">هذا السبب سيكون مرئي للسبوكيل فقط، ولن يظهر للعميل.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('rejectForm').submit();">تأكيد الرفض</button>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
    list-style: none;
    margin-bottom: 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #ddd;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-badge {
    position: absolute;
    left: -30px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    text-align: center;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}

.timeline-content {
    padding: 10px 15px;
    border-radius: 5px;
    background: #f8f9fa;
    position: relative;
}

.timeline-item:last-child {
    margin-bottom: 0;
}
</style>
@endsection
