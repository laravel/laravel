@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subagent.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subagent.quotes.index') }}">عروض الأسعار</a></li>
    <li class="breadcrumb-item active">تفاصيل العرض #{{ $quote->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-tag me-2"></i> تفاصيل عرض السعر #{{ $quote->id }}</h2>
        </div>
        <div class="col-md-4 text-md-end">
            @if($quote->status == 'pending' || $quote->status == 'agency_rejected')
                <a href="{{ route('subagent.quotes.edit', $quote) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> تعديل
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="fas fa-trash me-1"></i> إلغاء
                </button>
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
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> معلومات العرض</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>معلومات الطلب</h6>
                            <table class="table table-striped">
                                <tr>
                                    <th>رقم الطلب</th>
                                    <td>#{{ $quote->request_id }}</td>
                                </tr>
                                <tr>
                                    <th>الخدمة</th>
                                    <td>{{ $quote->request->service->name }}</td>
                                </tr>
                                <tr>
                                    <th>العميل</th>
                                    <td>{{ $quote->request->customer->name }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ الطلب</th>
                                    <td>{{ $quote->request->created_at->format('Y-m-d') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>معلومات العرض</h6>
                            <table class="table table-striped">
                                <tr>
                                    <th>السعر</th>
                                    <td>{{ $quote->price }} ر.س</td>
                                </tr>
                                <tr>
                                    <th>نسبة العمولة</th>
                                    <td>{{ $quote->commission_rate }}%</td>
                                </tr>
                                <tr>
                                    <th>مبلغ العمولة</th>
                                    <td>{{ $quote->commission_amount }} ر.س</td>
                                </tr>
                                <tr>
                                    <th>الحالة</th>
                                    <td>
                                        @if($quote->status == 'pending')
                                            <span class="badge bg-warning">بانتظار الموافقة</span>
                                        @elseif($quote->status == 'agency_approved')
                                            <span class="badge bg-info">معتمد من الوكالة</span>
                                        @elseif($quote->status == 'agency_rejected')
                                            <span class="badge bg-danger">مرفوض من الوكالة</span>
                                        @elseif($quote->status == 'customer_approved')
                                            <span class="badge bg-success">مقبول من العميل</span>
                                        @elseif($quote->status == 'customer_rejected')
                                            <span class="badge bg-danger">مرفوض من العميل</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <h6>تفاصيل العرض</h6>
                    <div class="p-3 mb-3 bg-light rounded">
                        {{ $quote->details }}
                    </div>
                    
                    <h6>تفاصيل الطلب الأصلي</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $quote->request->details }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-1"></i> تاريخ العرض</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-badge bg-success"><i class="fas fa-check"></i></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">تم تقديم العرض</h6>
                                <p class="mb-0">{{ $quote->created_at->format('Y-m-d H:i') }}</p>
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
                                            تم اعتماد العرض من الوكالة
                                        @elseif($quote->status == 'agency_rejected')
                                            تم رفض العرض من الوكالة
                                        @elseif($quote->status == 'customer_approved')
                                            تم قبول العرض من العميل
                                        @elseif($quote->status == 'customer_rejected')
                                            تم رفض العرض من العميل
                                        @endif
                                    </h6>
                                    <p class="mb-0">{{ $quote->updated_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-calculator me-1"></i> تفاصيل مالية</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>سعر العرض</th>
                            <td class="text-end">{{ $quote->price }} ر.س</td>
                        </tr>
                        <tr>
                            <th>نسبة العمولة</th>
                            <td class="text-end">{{ $quote->commission_rate }}%</td>
                        </tr>
                        <tr>
                            <th>مبلغ العمولة</th>
                            <td class="text-end text-danger">{{ $quote->commission_amount }} ر.س</td>
                        </tr>
                        <tr class="table-active">
                            <th>صافي المبلغ</th>
                            <td class="text-end fw-bold text-success">{{ $quote->price - $quote->commission_amount }} ر.س</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إلغاء العرض -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد إلغاء عرض السعر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في إلغاء عرض السعر هذا؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('subagent.quotes.cancel', $quote) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
    list-style: none;
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
</style>
@endsection
