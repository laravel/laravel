@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subagent.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subagent.services.index') }}">الخدمات المتاحة</a></li>
    <li class="breadcrumb-item active">{{ $service->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-cog me-2"></i> {{ $service->name }}</h2>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('subagent.requests.index', ['service_id' => $service->id]) }}" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> عرض الطلبات المرتبطة
            </a>
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
                            <ul class="list-group list-group-flush mb-4">
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
                                    <span>نسبة العمولة المخصصة:</span>
                                    <strong>{{ $serviceSubagent->pivot->custom_commission_rate ?? $service->commission_rate }}%</strong>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            @if($service->image_path)
                                <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->name }}" class="img-fluid rounded">
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>وصف الخدمة:</h5>
                        <div class="p-3 border rounded bg-light">
                            {{ $service->description }}
                        </div>
                    </div>
                </div>
            </div>

            @if(!$requestsHistory->isEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-1"></i> تاريخ عروض الأسعار</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>العميل</th>
                                        <th>السعر المقدم</th>
                                        <th>العمولة</th>
                                        <th>الحالة</th>
                                        <th>تاريخ التقديم</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requestsHistory as $request)
                                        @php
                                            $quote = $request->quotes->where('subagent_id', auth()->id())->first();
                                        @endphp
                                        <tr>
                                            <td>#{{ $request->id }}</td>
                                            <td>{{ $request->customer->name }}</td>
                                            <td>{{ $quote->price ?? 'غير متوفر' }} ر.س</td>
                                            <td>{{ $quote->commission_amount ?? 'غير متوفر' }} ر.س</td>
                                            <td>
                                                @if(!$quote)
                                                    <span class="badge bg-secondary">غير متوفر</span>
                                                @elseif($quote->status == 'pending')
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
                                            <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-1"></i> الإحصائيات</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-4">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-primary">{{ $requestsHistory->count() }}</h3>
                                <p class="mb-0">إجمالي الطلبات</p>
                            </div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-success">{{ $requestsHistory->sum(function($req) { return $req->quotes->where('subagent_id', auth()->id())->where('status', 'customer_approved')->count(); }) }}</h3>
                                <p class="mb-0">العروض المقبولة</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> معدل العمولة المخصص لك: <strong>{{ $serviceSubagent->pivot->custom_commission_rate ?? $service->commission_rate }}%</strong>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-1"></i> إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('subagent.requests.index', ['service_id' => $service->id]) }}" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-search me-1"></i> استعراض الطلبات المتاحة
                    </a>
                    <a href="{{ route('subagent.quotes.index', ['service_id' => $service->id]) }}" class="btn btn-info w-100">
                        <i class="fas fa-tag me-1"></i> عروض الأسعار المقدمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
