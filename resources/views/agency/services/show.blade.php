@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.services.index') }}">إدارة الخدمات</a></li>
    <li class="breadcrumb-item active">{{ $service->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-cog me-2"></i> {{ $service->name }}</h2>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('agency.services.edit', $service) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> تعديل
            </a>
            <form action="{{ route('agency.services.toggle-status', $service) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-{{ $service->status == 'active' ? 'danger' : 'success' }}">
                    <i class="fas fa-{{ $service->status == 'active' ? 'ban' : 'check' }} me-1"></i> {{ $service->status == 'active' ? 'تعطيل' : 'تفعيل' }}
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> معلومات الخدمة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>اسم الخدمة:</span>
                                    <strong>{{ $service->name }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>نوع الخدمة:</span>
                                    <strong>
                                        @if($service->type == 'security_approval')
                                            موافقة أمنية
                                        @elseif($service->type == 'transportation')
                                            نقل بري
                                        @elseif($service->type == 'hajj_umrah')
                                            حج وعمرة
                                        @elseif($service->type == 'flight')
                                            تذاكر طيران
                                        @elseif($service->type == 'passport')
                                            إصدار جوازات
                                        @else
                                            أخرى
                                        @endif
                                    </strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>تاريخ الإنشاء:</span>
                                    <strong>{{ $service->created_at->format('Y-m-d') }}</strong>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>السعر الأساسي:</span>
                                    <strong>{{ $service->base_price }} ر.س</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>نسبة العمولة:</span>
                                    <strong>{{ $service->commission_rate }}%</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>الحالة:</span>
                                    @if($service->status == 'active')
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>وصف الخدمة:</h5>
                        <div class="p-3 border rounded bg-light">
                            {{ $service->description }}
                        </div>
                    </div>
                    
                    @if($service->image_path)
                        <div class="mt-4 text-center">
                            <h5>صورة الخدمة:</h5>
                            <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->name }}" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-1"></i> إحصائيات الخدمة</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h5>الطلبات</h5>
                            <h2 class="text-primary">{{ $service->requests->count() }}</h2>
                        </div>
                        <div class="col-6 mb-3">
                            <h5>السبوكلاء</h5>
                            <h2 class="text-success">{{ $service->subagents->count() }}</h2>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5 class="mb-3">توزيع الطلبات حسب الحالة:</h5>
                    @php
                        $pendingCount = $service->requests->where('status', 'pending')->count();
                        $inProgressCount = $service->requests->where('status', 'in_progress')->count();
                        $completedCount = $service->requests->where('status', 'completed')->count();
                        $cancelledCount = $service->requests->where('status', 'cancelled')->count();
                        $totalCount = $service->requests->count() > 0 ? $service->requests->count() : 1;
                    @endphp
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <small>قيد الانتظار ({{ $pendingCount }})</small>
                            <small>{{ number_format(($pendingCount / $totalCount) * 100, 0) }}%</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($pendingCount / $totalCount) * 100 }}%" aria-valuenow="{{ $pendingCount }}" aria-valuemin="0" aria-valuemax="{{ $totalCount }}"></div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <small>قيد التنفيذ ({{ $inProgressCount }})</small>
                            <small>{{ number_format(($inProgressCount / $totalCount) * 100, 0) }}%</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($inProgressCount / $totalCount) * 100 }}%" aria-valuenow="{{ $inProgressCount }}" aria-valuemin="0" aria-valuemax="{{ $totalCount }}"></div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <small>مكتمل ({{ $completedCount }})</small>
                            <small>{{ number_format(($completedCount / $totalCount) * 100, 0) }}%</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($completedCount / $totalCount) * 100 }}%" aria-valuenow="{{ $completedCount }}" aria-valuemin="0" aria-valuemax="{{ $totalCount }}"></div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <small>ملغي ({{ $cancelledCount }})</small>
                            <small>{{ number_format(($cancelledCount / $totalCount) * 100, 0) }}%</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($cancelledCount / $totalCount) * 100 }}%" aria-valuenow="{{ $cancelledCount }}" aria-valuemin="0" aria-valuemax="{{ $totalCount }}"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-1"></i> السبوكلاء المرتبطون</h5>
                </div>
                <div class="card-body">
                    @if($service->subagents->isEmpty())
                        <div class="alert alert-info">
                            لا يوجد سبوكلاء مرتبطون بهذه الخدمة حتى الآن.
                        </div>
                    @else
                        <ul class="list-group">
                            @foreach($service->subagents as $subagent)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-user me-1"></i> {{ $subagent->name }}
                                    </div>
                                    <div>
                                        <span class="badge bg-{{ $subagent->pivot->is_active ? 'success' : 'danger' }}">
                                            {{ $subagent->pivot->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
