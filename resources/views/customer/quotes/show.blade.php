@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.quotes.index') }}">عروض الأسعار</a></li>
    <li class="breadcrumb-item active">عرض سعر #{{ $quote->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="mb-1">عرض سعر #{{ $quote->id }}</h2>
            <div class="text-muted">
                <span class="badge bg-{{ $quote->status_badge }}">{{ $quote->status_text }}</span>
                <span class="ms-2">{{ $quote->created_at->format('Y-m-d') }}</span>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            @if($quote->status === 'pending')
            <form action="{{ route('customer.quotes.approve', $quote) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success me-2" onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟')">
                    <i class="fas fa-check me-1"></i> قبول العرض
                </button>
            </form>
            <form action="{{ route('customer.quotes.reject', $quote) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('هل أنت متأكد من رفض هذا العرض؟')">
                    <i class="fas fa-times me-1"></i> رفض
                </button>
            </form>
            @elseif($quote->status === 'customer_approved')
            <span class="badge bg-success p-2">تم قبول هذا العرض</span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> تفاصيل عرض السعر</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">الخدمة المطلوبة</h6>
                            <p class="mb-0 fw-bold">{{ $quote->request->service->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">رقم الطلب</h6>
                            <p class="mb-0 fw-bold">
                                <a href="{{ route('customer.requests.show', $quote->request) }}">#{{ $quote->request->id }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-2">تفاصيل العرض</h6>
                        <p>{{ $quote->details ?: 'لا توجد تفاصيل إضافية' }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">تاريخ العرض</h6>
                            <p class="mb-0">{{ $quote->created_at->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">صلاحية العرض</h6>
                            <p class="mb-0">{{ $quote->expires_at ? $quote->expires_at->format('Y-m-d') : 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($quote->terms)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-file-contract me-1"></i> شروط وأحكام</h5>
                </div>
                <div class="card-body">
                    <p>{{ $quote->terms }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-tag me-1"></i> تفاصيل السعر</h5>
                </div>
                <div class="card-body">
                    <div class="price-tag mb-3 text-center">
                        <span class="display-6 text-primary fw-bold">
                            {{ number_format($quote->price, 2) }}
                        </span>
                        <span class="h5 text-muted">{{ $quote->currency_code }}</span>
                    </div>
                    
                    @if(isset($quote->price_details) && is_array($quote->price_details) && count($quote->price_details) > 0)
                    <div class="price-breakdown mb-3">
                        <h6 class="mb-2">تفاصيل التكلفة:</h6>
                        <ul class="list-group list-group-flush">
                            @foreach($quote->price_details as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $item['name'] }}</span>
                                <span>{{ number_format($item['amount'], 2) }} {{ $quote->currency_code }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <p class="text-muted text-center mb-4">
                        السعر شامل جميع الرسوم والضرائب.
                    </p>
                    @endif
                    
                    @if($quote->status === 'pending')
                    <div class="d-grid">
                        <form action="{{ route('customer.quotes.approve', $quote) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟')">
                                <i class="fas fa-check me-1"></i> قبول العرض
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-1"></i> مقدم العرض</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $quote->subagent->name }}</h6>
                    <div class="text-muted mb-3">
                        <i class="fas fa-building me-1"></i> {{ $quote->subagent->agency->name }}
                    </div>
                    
                    @if($quote->subagent->verified)
                    <div class="bg-light p-2 rounded mb-3">
                        <i class="fas fa-check-circle text-success me-1"></i>
                        <span>سبوكيل معتمد</span>
                    </div>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-comment me-1"></i> التواصل مع مقدم العرض
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
