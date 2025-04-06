@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.subagents.index') }}">إدارة السبوكلاء</a></li>
    <li class="breadcrumb-item active">تفاصيل السبوكيل: {{ $subagent->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-user me-2"></i> تفاصيل السبوكيل: {{ $subagent->name }}</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('agency.subagents.edit', $subagent) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> تعديل
            </a>
            <form action="{{ route('agency.subagents.toggle-status', $subagent) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-{{ $subagent->is_active ? 'danger' : 'success' }}">
                    <i class="fas fa-{{ $subagent->is_active ? 'ban' : 'check' }} me-1"></i> {{ $subagent->is_active ? 'تعطيل' : 'تنشيط' }}
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- بطاقة المعلومات الشخصية -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card me-1"></i> المعلومات الشخصية</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3">
                            <span class="avatar-text">{{ substr($subagent->name, 0, 2) }}</span>
                        </div>
                        <h4>{{ $subagent->name }}</h4>
                        <p class="text-muted">{{ $subagent->is_active ? 'نشط' : 'معطل' }}</p>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>رقم الهاتف:</span>
                            <strong>{{ $subagent->phone }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>البريد الإلكتروني:</span>
                            <strong>{{ $subagent->email }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>تاريخ الإنضمام:</span>
                            <strong>{{ $subagent->created_at->format('Y-m-d') }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- بطاقة الإحصائيات -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-1"></i> الإحصائيات</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h5>الخدمات المتاحة</h5>
                            <h2 class="text-primary">{{ $subagent->services->count() }}</h2>
                        </div>
                        <div class="col-6 mb-3">
                            <h5>عروض الأسعار</h5>
                            <h2 class="text-info">{{ \App\Models\Quote::where('subagent_id', $subagent->id)->count() }}</h2>
                        </div>
                        <div class="col-6 mb-3">
                            <h5>العروض المقبولة</h5>
                            <h2 class="text-success">{{ \App\Models\Quote::where('subagent_id', $subagent->id)->whereIn('status', ['agency_approved', 'customer_approved'])->count() }}</h2>
                        </div>
                        <div class="col-6 mb-3">
                            <h5>العروض المرفوضة</h5>
                            <h2 class="text-danger">{{ \App\Models\Quote::where('subagent_id', $subagent->id)->whereIn('status', ['agency_rejected', 'customer_rejected'])->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <!-- بطاقة الخدمات المتاحة -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs me-1"></i> الخدمات المتاحة</h5>
                </div>
                <div class="card-body">
                    @if($subagent->services->isNotEmpty())
                        <form action="{{ route('agency.subagents.update-services', $subagent) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>متاح</th>
                                            <th>الخدمة</th>
                                            <th>النوع</th>
                                            <th>نسبة العمولة (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(\App\Models\Service::where('agency_id', auth()->user()->agency_id)->get() as $service)
                                            @php
                                                $serviceSubagent = $subagent->services->where('id', $service->id)->first();
                                                $isActive = $serviceSubagent ? $serviceSubagent->pivot->is_active : false;
                                                $commissionRate = $serviceSubagent ? $serviceSubagent->pivot->custom_commission_rate : $service->commission_rate;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="services[{{ $service->id }}][active]" value="1" id="service{{ $service->id }}" {{ $serviceSubagent ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td>{{ $service->name }}</td>
                                                <td>
                                                    @if($service->type == 'security_approval')
                                                        <span class="badge bg-primary">موافقة أمنية</span>
                                                    @elseif($service->type == 'transportation')
                                                        <span class="badge bg-success">نقل بري</span>
                                                    @elseif($service->type == 'hajj_umrah')
                                                        <span class="badge bg-info">حج وعمرة</span>
                                                    @elseif($service->type == 'flight')
                                                        <span class="badge bg-warning">تذاكر طيران</span>
                                                    @elseif($service->type == 'passport')
                                                        <span class="badge bg-danger">إصدار جوازات</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" name="services[{{ $service->id }}][commission_rate]" value="{{ $commissionRate }}" min="0" max="100" step="0.01">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> حفظ التغييرات
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            لا توجد خدمات متاحة لهذا السبوكيل. <a href="{{ route('agency.subagents.edit', $subagent) }}">إضافة خدمات</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- بطاقة آخر العروض -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-tags me-1"></i> آخر عروض الأسعار</h5>
                </div>
                <div class="card-body">
                    @php
                        $quotes = \App\Models\Quote::where('subagent_id', $subagent->id)
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($quotes->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>الطلب</th>
                                        <th>الخدمة</th>
                                        <th>السعر</th>
                                        <th>العمولة</th>
                                        <th>الحالة</th>
                                        <th>التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotes as $quote)
                                        <tr>
                                            <td>{{ $quote->id }}</td>
                                            <td>{{ $quote->request->id }}</td>
                                            <td>{{ $quote->request->service->name }}</td>
                                            <td>{{ $quote->price }} ر.س</td>
                                            <td>{{ $quote->commission_amount }} ر.س</td>
                                            <td>
                                                @if($quote->status == 'pending')
                                                    <span class="badge bg-warning">بانتظار الموافقة</span>
                                                @elseif($quote->status == 'agency_approved')
                                                    <span class="badge bg-info">معتمد من الوكيل</span>
                                                @elseif($quote->status == 'agency_rejected')
                                                    <span class="badge bg-danger">مرفوض من الوكيل</span>
                                                @elseif($quote->status == 'customer_approved')
                                                    <span class="badge bg-success">معتمد من العميل</span>
                                                @elseif($quote->status == 'customer_rejected')
                                                    <span class="badge bg-danger">مرفوض من العميل</span>
                                                @endif
                                            </td>
                                            <td>{{ $quote->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('agency.quotes.index', ['subagent_id' => $subagent->id]) }}" class="btn btn-info">
                                <i class="fas fa-list me-1"></i> عرض جميع العروض
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info">
                            لم يقدم هذا السبوكيل أي عروض أسعار حتى الآن.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 100px;
    height: 100px;
    background-color: #0d6efd;
    text-align: center;
    border-radius: 50%;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
}

.avatar-text {
    position: relative;
    top: 25px;
    font-size: 40px;
    line-height: 50px;
    color: #fff;
    text-transform: uppercase;
    font-weight: bold;
}
</style>
@endsection
