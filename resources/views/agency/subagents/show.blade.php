@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.subagents.index') }}">إدارة السبوكلاء</a></li>
    <li class="breadcrumb-item active">{{ $subagent->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-user me-2"></i> بيانات السبوكيل: {{ $subagent->name }}</h2>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('agency.subagents.edit', $subagent) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> تعديل
            </a>
            <form action="{{ route('agency.subagents.toggle-status', $subagent) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-{{ $subagent->is_active ? 'danger' : 'success' }}">
                    <i class="fas fa-{{ $subagent->is_active ? 'ban' : 'check' }} me-1"></i> 
                    {{ $subagent->is_active ? 'تعطيل' : 'تفعيل' }}
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> المعلومات الأساسية</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th style="width: 40%">الاسم الكامل</th>
                            <td>{{ $subagent->name }}</td>
                        </tr>
                        <tr>
                            <th>البريد الإلكتروني</th>
                            <td>{{ $subagent->email }}</td>
                        </tr>
                        <tr>
                            <th>رقم الهاتف</th>
                            <td>{{ $subagent->phone }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                @if($subagent->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">معطل</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنضمام</th>
                            <td>{{ $subagent->created_at->format('Y-m-d') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-1"></i> إحصائيات السبوكيل</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-4">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-primary">{{ $subagent->services->count() }}</h3>
                                <p class="mb-0">الخدمات المتاحة</p>
                            </div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-success">{{ $subagent->quotes->count() }}</h3>
                                <p class="mb-0">عروض الأسعار المقدمة</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6>توزيع عروض الأسعار حسب الحالة:</h6>
                        @php
                            $pendingCount = $subagent->quotes->where('status', 'pending')->count();
                            $agencyApprovedCount = $subagent->quotes->where('status', 'agency_approved')->count();
                            $agencyRejectedCount = $subagent->quotes->where('status', 'agency_rejected')->count();
                            $customerApprovedCount = $subagent->quotes->where('status', 'customer_approved')->count();
                            $customerRejectedCount = $subagent->quotes->where('status', 'customer_rejected')->count();
                            $totalCount = $subagent->quotes->count() > 0 ? $subagent->quotes->count() : 1;
                        @endphp
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small>بانتظار الموافقة ({{ $pendingCount }})</small>
                                <small>{{ number_format(($pendingCount / $totalCount) * 100, 0) }}%</small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($pendingCount / $totalCount) * 100 }}%" aria-valuenow="{{ $pendingCount }}" aria-valuemin="0" aria-valuemax="{{ $totalCount }}"></div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small>موافق عليها من الوكالة ({{ $agencyApprovedCount }})</small>
                                <small>{{ number_format(($agencyApprovedCount / $totalCount) * 100, 0) }}%</small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($agencyApprovedCount / $totalCount) * 100 }}%" aria-valuenow="{{ $agencyApprovedCount }}" aria-valuemin="0" aria-valuemax="{{ $totalCount }}"></div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small>موافق عليها من العميل ({{ $customerApprovedCount }})</small>
                                <small>{{ number_format(($customerApprovedCount / $totalCount) * 100, 0) }}%</small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($customerApprovedCount / $totalCount) * 100 }}%" aria-valuenow="{{ $customerApprovedCount }}" aria-valuemin="0" aria-valuemax="{{ $totalCount }}"></div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small>مرفوضة ({{ $agencyRejectedCount + $customerRejectedCount }})</small>
                                <small>{{ number_format((($agencyRejectedCount + $customerRejectedCount) / $totalCount) * 100, 0) }}%</small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ (($agencyRejectedCount + $customerRejectedCount) / $totalCount) * 100 }}%" aria-valuenow="{{ $agencyRejectedCount + $customerRejectedCount }}" aria-valuemin="0" aria-valuemax="{{ $totalCount }}"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs me-1"></i> الخدمات المتاحة</h5>
                </div>
                <div class="card-body">
                    @if($subagent->services->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> لا توجد خدمات متاحة لهذا السبوكيل حتى الآن.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>الخدمة</th>
                                        <th>النوع</th>
                                        <th>السعر الأساسي</th>
                                        <th>نسبة العمولة</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subagent->services as $service)
                                        <tr>
                                            <td>{{ $service->name }}</td>
                                            <td>
                                                @if($service->type == 'security_approval')
                                                    <span class="badge bg-secondary">موافقة أمنية</span>
                                                @elseif($service->type == 'transportation')
                                                    <span class="badge bg-info">نقل بري</span>
                                                @elseif($service->type == 'hajj_umrah')
                                                    <span class="badge bg-success">حج وعمرة</span>
                                                @elseif($service->type == 'flight')
                                                    <span class="badge bg-primary">تذاكر طيران</span>
                                                @elseif($service->type == 'passport')
                                                    <span class="badge bg-warning">إصدار جوازات</span>
                                                @else
                                                    <span class="badge bg-dark">أخرى</span>
                                                @endif
                                            </td>
                                            <td>{{ $service->base_price }} ر.س</td>
                                            <td>{{ $service->pivot->custom_commission_rate }}%</td>
                                            <td>
                                                @if($service->pivot->is_active)
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">معطل</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-tag me-1"></i> آخر عروض الأسعار المقدمة</h5>
                </div>
                <div class="card-body">
                    @if($subagent->quotes->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> لم يقم هذا السبوكيل بتقديم أي عروض أسعار حتى الآن.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>الخدمة</th>
                                        <th>تفاصيل الطلب</th>
                                        <th>السعر</th>
                                        <th>العمولة</th>
                                        <th>الحالة</th>
                                        <th>تاريخ التقديم</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subagent->quotes->sortByDesc('created_at')->take(5) as $quote)
                                        <tr>
                                            <td>{{ $quote->id }}</td>
                                            <td>{{ $quote->request->service->name }}</td>
                                            <td>{{ Str::limit($quote->request->details, 30) }}</td>
                                            <td>{{ $quote->price }} ر.س</td>
                                            <td>{{ $quote->commission_amount }} ر.س</td>
                                            <td>
                                                @if($quote->status == 'pending')
                                                    <span class="badge bg-warning">بانتظار الموافقة</span>
                                                @elseif($quote->status == 'agency_approved')
                                                    <span class="badge bg-info">موافق عليه من الوكالة</span>
                                                @elseif($quote->status == 'agency_rejected')
                                                    <span class="badge bg-danger">مرفوض من الوكالة</span>
                                                @elseif($quote->status == 'customer_approved')
                                                    <span class="badge bg-success">موافق عليه من العميل</span>
                                                @else
                                                    <span class="badge bg-danger">مرفوض من العميل</span>
                                                @endif
                                            </td>
                                            <td>{{ $quote->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('agency.quotes.show', $quote) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($subagent->quotes->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('agency.quotes.index', ['subagent_id' => $subagent->id]) }}" class="btn btn-outline-primary">
                                    عرض كل عروض الأسعار ({{ $subagent->quotes->count() }})
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
