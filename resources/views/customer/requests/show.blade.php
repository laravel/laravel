@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.requests.index') }}">طلبات الخدمة</a></li>
    <li class="breadcrumb-item active">طلب #{{ $request->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="mb-1">طلب خدمة #{{ $request->id }}</h2>
            <div class="d-flex align-items-center">
                <span class="badge bg-{{ $statusBadge ?? 'secondary' }} me-2">{{ $statusText ?? $request->status }}</span>
                <span class="text-muted">{{ $request->created_at->format('Y-m-d') }}</span>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            @if($request->status === 'pending')
                <form action="{{ route('customer.requests.cancel', $request) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟')">
                        <i class="fas fa-times me-1"></i> إلغاء الطلب
                    </button>
                </form>
            @elseif($request->status === 'completed')
                <button class="btn btn-primary" disabled>
                    <i class="fas fa-check-circle me-1"></i> مكتمل
                </button>
            @endif

            @if($request->status !== 'cancelled')
                <a href="{{ route('customer.requests.create', ['service_id' => $request->service_id]) }}" class="btn btn-outline-primary ms-2">
                    <i class="fas fa-copy me-1"></i> طلب مشابه
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> تفاصيل الطلب</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">الخدمة المطلوبة</h6>
                            <p class="mb-0 fw-bold">{{ $request->service->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">الأولوية</h6>
                            <p class="mb-0">
                                @if($request->priority === 'high')
                                    <span class="badge bg-danger">عالية</span>
                                @elseif($request->priority === 'medium')
                                    <span class="badge bg-warning">متوسطة</span>
                                @else
                                    <span class="badge bg-info">عادية</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">تفاصيل الطلب</h6>
                        <p>{{ $request->details ?: 'لا توجد تفاصيل إضافية' }}</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">تاريخ الإنشاء</h6>
                            <p class="mb-0">{{ $request->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">التاريخ المطلوب</h6>
                            <p class="mb-0">{{ $request->requested_date ? date('Y-m-d', strtotime($request->requested_date)) : 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- عروض الأسعار -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-1"></i> عروض الأسعار المقدمة</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($request->quotes ?? []) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($request->quotes as $quote)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">عرض سعر من: {{ $quote->subagent->name }}</h6>
                                            <p class="text-primary mb-1">{{ number_format($quote->price, 2) }} {{ $quote->currency_code }}</p>
                                            <small class="text-muted">{{ $quote->created_at->format('Y-m-d') }}</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $quoteStatusBadge[$quote->status] ?? 'secondary' }} mb-2 d-block">
                                                {{ $quoteStatusText[$quote->status] ?? $quote->status }}
                                            </span>
                                            <a href="{{ route('customer.quotes.show', $quote) }}" class="btn btn-sm btn-outline-primary">
                                                عرض التفاصيل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-4">
                            <p class="text-muted">لم يتم تقديم عروض أسعار بعد</p>
                            @if($request->status === 'pending' || $request->status === 'in_progress')
                                <p class="small">سيتم إخطارك عند استلام عروض جديدة</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- سجل التحديثات -->
            @if(isset($request->status_history) && count($request->status_history) > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-history me-1"></i> سجل التحديثات</h5>
                </div>
                <div class="card-body p-0">
                    <div class="timeline p-3">
                        @foreach($request->status_history as $history)
                            <div class="timeline-item pb-3">
                                <div class="timeline-marker bg-{{ $statusColors[$history['status']] ?? 'secondary' }}"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ $statusLabels[$history['status']] ?? $history['status'] }}</h6>
                                    <p class="small text-muted mb-0">{{ \Carbon\Carbon::parse($history['date'])->format('Y-m-d H:i') }}</p>
                                    @if(!empty($history['note']))
                                        <p class="mt-2">{{ $history['note'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- معلومات الخدمة -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-cog me-1"></i> معلومات الخدمة</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title">{{ $request->service->name }}</h6>
                    <p class="text-muted small">{{ $request->service->description }}</p>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">النوع</h6>
                        <p>{{ \App\Helpers\ServiceTypeHelper::getLocalizedType($request->service->type) }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">السعر الأساسي</h6>
                        <p class="text-primary">{{ number_format($request->service->base_price, 2) }} {{ $request->service->currency_code }}</p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('customer.services.show', $request->service_id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> عرض الخدمة
                        </a>
                    </div>
                </div>
            </div>

            <!-- معلومات الوكالة -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-building me-1"></i> معلومات الوكالة</h5>
                </div>
                <div class="card-body text-center">
                    @if(isset($request->agency->logo_path) && $request->agency->logo_path)
                        <img src="{{ Storage::url($request->agency->logo_path) }}" alt="{{ $request->agency->name }}" class="img-fluid mb-3" style="max-height: 60px;">
                    @endif
                    <h6>{{ $request->agency->name ?? 'وكالة غير محددة' }}</h6>
                    @if(isset($request->agency->contact_email) && $request->agency->contact_email)
                        <p class="mb-1"><i class="fas fa-envelope me-1 text-muted"></i> {{ $request->agency->contact_email }}</p>
                    @endif
                    @if(isset($request->agency->phone) && $request->agency->phone)
                        <p class="mb-1"><i class="fas fa-phone me-1 text-muted"></i> {{ $request->agency->phone }}</p>
                    @endif
                </div>
            </div>

            <!-- المساعدة والدعم -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-1"></i> المساعدة والدعم</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">إذا كان لديك أي استفسارات حول هذا الطلب، يرجى التواصل معنا:</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.support') }}" class="btn btn-outline-primary">
                            <i class="fas fa-headset me-1"></i> الاتصال بالدعم
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        list-style: none;
        padding: 0;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 24px;
        margin-bottom: 20px;
    }
    
    .timeline-marker {
        position: absolute;
        top: 6px;
        left: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    
    .timeline-item:not(:last-child):before {
        content: '';
        position: absolute;
        top: 20px;
        left: 5px;
        height: 100%;
        width: 2px;
        background-color: #e9ecef;
    }
</style>
@endsection
