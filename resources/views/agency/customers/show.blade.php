@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.customers.index') }}">إدارة العملاء</a></li>
    <li class="breadcrumb-item active">{{ $customer->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-user me-2"></i> بيانات العميل: {{ $customer->name }}</h2>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('agency.customers.edit', $customer) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> تعديل
            </a>
            <form action="{{ route('agency.customers.toggle-status', $customer) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-{{ $customer->is_active ? 'danger' : 'success' }}">
                    <i class="fas fa-{{ $customer->is_active ? 'ban' : 'check' }} me-1"></i> 
                    {{ $customer->is_active ? 'تعطيل' : 'تفعيل' }}
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
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th>البريد الإلكتروني</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <th>رقم الهاتف</th>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                @if($customer->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">معطل</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنضمام</th>
                            <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-1"></i> إحصائيات العميل</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-4">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-primary">{{ $customer->customerRequests->count() }}</h3>
                                <p class="mb-0">إجمالي الطلبات</p>
                            </div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-success">{{ $customer->customerRequests->where('status', 'completed')->count() }}</h3>
                                <p class="mb-0">الطلبات المكتملة</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6>توزيع الطلبات حسب الحالة:</h6>
                        @php
                            $pendingCount = $customer->customerRequests->where('status', 'pending')->count();
                            $inProgressCount = $customer->customerRequests->where('status', 'in_progress')->count();
                            $completedCount = $customer->customerRequests->where('status', 'completed')->count();
                            $cancelledCount = $customer->customerRequests->where('status', 'cancelled')->count();
                            $totalCount = $customer->customerRequests->count() > 0 ? $customer->customerRequests->count() : 1;
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
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-1"></i> آخر طلبات العميل</h5>
                </div>
                <div class="card-body">
                    @if($customer->customerRequests->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> لا توجد طلبات مسجلة لهذا العميل حتى الآن.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>الخدمة</th>
                                        <th>التفاصيل</th>
                                        <th>الأولوية</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الطلب</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->customerRequests->sortByDesc('created_at')->take(5) as $request)
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>{{ $request->service->name }}</td>
                                            <td>{{ Str::limit($request->details, 50) }}</td>
                                            <td>
                                                @if($request->priority == 'normal')
                                                    <span class="badge bg-info">عادي</span>
                                                @elseif($request->priority == 'urgent')
                                                    <span class="badge bg-warning">مستعجل</span>
                                                @else
                                                    <span class="badge bg-danger">طارئ</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($request->status == 'pending')
                                                    <span class="badge bg-warning">قيد الانتظار</span>
                                                @elseif($request->status == 'in_progress')
                                                    <span class="badge bg-info">قيد التنفيذ</span>
                                                @elseif($request->status == 'completed')
                                                    <span class="badge bg-success">مكتمل</span>
                                                @else
                                                    <span class="badge bg-danger">ملغي</span>
                                                @endif
                                            </td>
                                            <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('agency.requests.show', $request) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($customer->customerRequests->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('agency.requests.index', ['customer_id' => $customer->id]) }}" class="btn btn-outline-primary">
                                    عرض كل الطلبات ({{ $customer->customerRequests->count() }})
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
