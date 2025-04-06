@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">الخدمات المتاحة</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-cogs me-2"></i> الخدمات المتاحة</h2>
            <p class="text-muted">استعرض الخدمات التي تقدمها الوكالة واطلب الخدمة التي تحتاجها</p>
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

    @if($services->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> لا توجد خدمات متاحة حالياً. يرجى التواصل مع الوكالة للحصول على المزيد من المعلومات.
        </div>
    @else
        @foreach($services as $type => $typeServices)
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        @if($type == 'security_approval')
                            <i class="fas fa-shield-alt me-1"></i> خدمات الموافقات الأمنية
                        @elseif($type == 'transportation')
                            <i class="fas fa-bus me-1"></i> خدمات النقل البري
                        @elseif($type == 'hajj_umrah')
                            <i class="fas fa-kaaba me-1"></i> خدمات الحج والعمرة
                        @elseif($type == 'flight')
                            <i class="fas fa-plane me-1"></i> خدمات تذاكر الطيران
                        @elseif($type == 'passport')
                            <i class="fas fa-passport me-1"></i> خدمات الجوازات
                        @else
                            <i class="fas fa-cog me-1"></i> خدمات أخرى
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($typeServices as $service)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if($service->image_path)
                                        <img src="{{ asset('storage/' . $service->image_path) }}" class="card-img-top" alt="{{ $service->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="bg-light text-center py-5">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $service->name }}</h5>
                                        <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="{{ route('customer.services.show', $service) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-info-circle me-1"></i> التفاصيل
                                            </a>
                                            <span class="badge bg-secondary">{{ $service->base_price }} ر.س</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
