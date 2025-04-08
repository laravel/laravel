@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customer.services.index') }}">الخدمات المتاحة</a></li>
    <li class="breadcrumb-item active">{{ $service->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-1">{{ $service->name }}</h2>
            <div class="text-muted">
                <span class="badge bg-{{ $service->status == 'active' ? 'success' : 'warning' }}">
                    {{ $service->status == 'active' ? 'متاح' : 'غير متاح' }}
                </span>
                <span class="ms-2">{{ \App\Helpers\ServiceTypeHelper::getLocalizedType($service->type) }}</span>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('customer.requests.create', ['service_id' => $service->id]) }}" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1"></i> طلب الخدمة
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> تفاصيل الخدمة</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">الوصف</h6>
                        <p>{{ $service->description ?: 'لا يوجد وصف متاح' }}</p>
                    </div>

                    @if($service->features)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">المميزات</h6>
                        <ul class="list-group list-group-flush">
                            @foreach($service->features as $feature)
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($service->requirements)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">المتطلبات</h6>
                        <ul class="list-group list-group-flush">
                            @foreach($service->requirements as $requirement)
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                {{ $requirement }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mb-4">
                        <h6 class="text-muted mb-2">مدة التنفيذ</h6>
                        <p>{{ $service->execution_time ?? 'حسب طبيعة الطلب' }}</p>
                    </div>
                </div>
            </div>

            @if($service->additional_info)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-1"></i> معلومات إضافية</h5>
                </div>
                <div class="card-body">
                    <p>{{ $service->additional_info }}</p>
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
                            {{ number_format($service->base_price, 2) }}
                        </span>
                        <span class="h5 text-muted">{{ $service->currency_code }}</span>
                    </div>
                    <p class="text-muted text-center mb-4">
                        السعر الأساسي. قد تختلف التكلفة النهائية حسب متطلبات الخدمة الخاصة بك.
                    </p>
                    <div class="d-grid">
                        <a href="{{ route('customer.requests.create', ['service_id' => $service->id]) }}" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> طلب الخدمة الآن
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <small class="text-muted">يمكنك مقارنة عدة عروض أسعار من مختلف السبوكلاء</small>
                </div>
            </div>

            @if(isset($service->agency) && $service->agency)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-building me-1"></i> مقدم الخدمة</h5>
                </div>
                <div class="card-body text-center">
                    @if($service->agency->logo_path)
                    <img src="{{ Storage::url($service->agency->logo_path) }}" alt="{{ $service->agency->name }}" class="img-fluid mb-3" style="max-height: 80px;">
                    @endif
                    <h6>{{ $service->agency->name }}</h6>
                    @if($service->agency->contact_email)
                    <p class="mb-1"><i class="fas fa-envelope me-1 text-muted"></i> {{ $service->agency->contact_email }}</p>
                    @endif
                    @if($service->agency->phone)
                    <p class="mb-1"><i class="fas fa-phone me-1 text-muted"></i> {{ $service->agency->phone }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
