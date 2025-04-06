@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item active">لوحة تحكم الوكيل</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">لوحة تحكم الوكيل</h1>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-4">
                        <div class="card-body">
                            <h5 class="card-title">إجمالي السبوكلاء</h5>
                            <p class="card-text display-4">{{ \App\Models\User::where('agency_id', auth()->user()->agency_id)->where('user_type', 'subagent')->count() }}</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('agency.subagents.index') }}">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-4">
                        <div class="card-body">
                            <h5 class="card-title">إجمالي العملاء</h5>
                            <p class="card-text display-4">{{ \App\Models\User::where('agency_id', auth()->user()->agency_id)->where('user_type', 'customer')->count() }}</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('agency.customers.index') }}">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-4">
                        <div class="card-body">
                            <h5 class="card-title">إجمالي الطلبات</h5>
                            <p class="card-text display-4">{{ \App\Models\Request::where('agency_id', auth()->user()->agency_id)->count() }}</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('agency.requests.index') }}">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger mb-4">
                        <div class="card-body">
                            <h5 class="card-title">إجمالي عروض الأسعار</h5>
                            <p class="card-text display-4">{{ \App\Models\Quote::whereHas('request', function($query) {
                                $query->where('agency_id', auth()->user()->agency_id);
                            })->count() }}</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('agency.quotes.index') }}">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            أحدث الطلبات
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>العميل</th>
                                            <th>الخدمة</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الطلب</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(\App\Models\Request::where('agency_id', auth()->user()->agency_id)->latest()->take(5)->get() as $request)
                                            <tr>
                                                <td>{{ $request->id }}</td>
                                                <td>{{ $request->customer->name }}</td>
                                                <td>{{ $request->service->name }}</td>
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
                                                    <a href="{{ route('agency.requests.show', $request) }}" class="btn btn-sm btn-primary">عرض</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">لا توجد طلبات حتى الآن</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('agency.requests.index') }}" class="btn btn-sm btn-primary">عرض كل الطلبات</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-1"></i>
                            توزيع الخدمات
                        </div>
                        <div class="card-body">
                            <canvas id="servicesChart" width="100%" height="200"></canvas>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-bullhorn me-1"></i>
                            إشعارات سريعة
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    طلبات جديدة تحتاج مراجعة
                                    <span class="badge bg-primary rounded-pill">{{ \App\Models\Request::where('agency_id', auth()->user()->agency_id)->where('status', 'pending')->count() }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    عروض أسعار بانتظار الموافقة
                                    <span class="badge bg-warning rounded-pill">{{ \App\Models\Quote::whereHas('request', function($query) {
                                        $query->where('agency_id', auth()->user()->agency_id);
                                    })->where('status', 'pending')->count() }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    طلبات تم إكمالها هذا الأسبوع
                                    <span class="badge bg-success rounded-pill">{{ \App\Models\Request::where('agency_id', auth()->user()->agency_id)->where('status', 'completed')->where('created_at', '>=', now()->subWeek())->count() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // بيانات وهمية للرسم البياني
        var ctx = document.getElementById('servicesChart').getContext('2d');
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['موافقات أمنية', 'نقل بري', 'حج وعمرة', 'تذاكر طيران', 'إصدار جوازات'],
                datasets: [{
                    data: [25, 20, 15, 30, 10],
                    backgroundColor: [
                        'rgba(0, 123, 255, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)'
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    });
</script>
@endsection
