@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">عروض الأسعار</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-tags me-2"></i> عروض الأسعار</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search me-1"></i> بحث وتصفية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('customer.quotes.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="service" class="form-label">الخدمة</label>
                    <select class="form-select" id="service" name="service">
                        <option value="">كل الخدمات</option>
                        @foreach(\App\Models\Service::whereHas('requests', function($query) {
                            $query->where('customer_id', auth()->id());
                        })->get() as $service)
                            <option value="{{ $service->id }}" {{ request('service') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="agency_approved" {{ request('status') == 'agency_approved' ? 'selected' : '' }}>بانتظار الرد</option>
                        <option value="customer_approved" {{ request('status') == 'customer_approved' ? 'selected' : '' }}>تمت الموافقة</option>
                        <option value="customer_rejected" {{ request('status') == 'customer_rejected' ? 'selected' : '' }}>تم الرفض</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-4">
        @forelse($quotes as $quote)
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header {{ $quote->status == 'agency_approved' ? 'bg-warning' : ($quote->status == 'customer_approved' ? 'bg-success' : 'bg-danger') }} text-white">
                        <h5 class="mb-0 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-tag me-1"></i> عرض سعر #{{ $quote->id }}</span>
                            @if($quote->status == 'agency_approved')
                                <span class="badge bg-light text-dark">بانتظار ردك</span>
                            @elseif($quote->status == 'customer_approved')
                                <span class="badge bg-light text-dark">تمت الموافقة</span>
                            @elseif($quote->status == 'customer_rejected')
                                <span class="badge bg-light text-dark">تم الرفض</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>تفاصيل الطلب:</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>رقم الطلب:</span>
                                        <span class="badge bg-primary">{{ $quote->request->id }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>الخدمة:</span>
                                        <span>{{ $quote->request->service->name }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>تاريخ الطلب:</span>
                                        <span>{{ $quote->request->created_at->format('Y-m-d') }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>تفاصيل العرض:</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>السعر:</span>
                                        <span class="fw-bold text-success">{{ $quote->price }} ر.س</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>تاريخ العرض:</span>
                                        <span>{{ $quote->created_at->format('Y-m-d') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <h6>تفاصيل إضافية:</h6>
                        <p class="border p-3 rounded">{{ $quote->details }}</p>
                        
                        @if($quote->status == 'agency_approved')
                            <div class="d-flex justify-content-center mt-3">
                                <form action="{{ route('customer.quotes.approve', $quote) }}" method="POST" class="mx-1">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check me-1"></i> موافقة على العرض
                                    </button>
                                </form>
                                <form action="{{ route('customer.quotes.reject', $quote) }}" method="POST" class="mx-1">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times me-1"></i> رفض العرض
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('customer.requests.show', $quote->request) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i> عرض الطلب
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لا توجد عروض أسعار متاحة حاليًا.
                </div>
            </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $quotes->appends(request()->query())->links() }}
    </div>
</div>
@endsection
