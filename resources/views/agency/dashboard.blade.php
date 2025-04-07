@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">لوحة التحكم</h1>
        <div>
            @if(Route::has('agency.reports.export'))
                <a href="{{ route('agency.reports.export') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50 ml-2"></i> تصدير التقرير
                </a>
            @else
                <a href="{{ route('agency.reports.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-chart-bar fa-sm text-white-50 ml-2"></i> التقارير
                </a>
            @endif
            <a href="{{ route('agency.requests.create') }}" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 ml-2"></i> إنشاء طلب جديد
            </a>
        </div>
    </div>

    <!-- Content Row - Main Stats -->
    <div class="row">
        <!-- إجمالي الطلبات -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الطلبات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['requests_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الطلبات قيد التنفيذ -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">قيد التنفيذ</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['in_progress_requests_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- عدد العملاء -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">عدد العملاء</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['customers_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- عدد السبوكلاء -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">عدد السبوكلاء</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['subagents_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Charts -->
    <div class="row">
        <!-- الطلبات حسب الشهور -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">الطلبات حسب الشهور</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="requestsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- توزيع حالات الطلبات -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع حالات الطلبات</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="requestStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> قيد الانتظار
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> قيد التنفيذ
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> مكتملة
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Top Services & Subagents -->
    <div class="row">
        <!-- الخدمات الأكثر طلباً -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الخدمات الأكثر طلباً</h6>
                </div>
                <div class="card-body">
                    @if($topServices->isEmpty())
                        <div class="text-center text-muted">
                            <p>لا توجد بيانات كافية</p>
                        </div>
                    @else
                        @foreach($topServices as $service)
                            <h4 class="small font-weight-bold">
                                {{ $service->service->name }}
                                <span class="float-right">{{ $service->count }} طلب</span>
                            </h4>
                            <div class="progress mb-4">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($service->count / $topServices->max('count')) * 100 }}%" 
                                     aria-valuenow="{{ $service->count }}" aria-valuemin="0" aria-valuemax="{{ $topServices->max('count') }}"></div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- السبوكلاء الأكثر نشاطاً -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">السبوكلاء الأكثر نشاطاً</h6>
                </div>
                <div class="card-body">
                    @if($topSubagents->isEmpty())
                        <div class="text-center text-muted">
                            <p>لا توجد بيانات كافية</p>
                        </div>
                    @else
                        @foreach($topSubagents as $subagent)
                            <h4 class="small font-weight-bold">
                                {{ $subagent->subagent->name }}
                                <span class="float-right">{{ $subagent->count }} عرض</span>
                            </h4>
                            <div class="progress mb-4">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($subagent->count / $topSubagents->max('count')) * 100 }}%" 
                                     aria-valuenow="{{ $subagent->count }}" aria-valuemin="0" aria-valuemax="{{ $topSubagents->max('count') }}"></div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Latest Activities -->
    <div class="row">
        <!-- آخر الطلبات -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">آخر الطلبات</h6>
                </div>
                <div class="card-body">
                    @if($latestRequests->isEmpty())
                        <div class="text-center text-muted">
                            <p>لا توجد طلبات حتى الآن</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>العميل</th>
                                        <th>الخدمة</th>
                                        <th>الحالة</th>
                                        <th>التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latestRequests as $request)
                                        <tr>
                                            <td><a href="{{ route('agency.requests.show', $request) }}">#{{ $request->id }}</a></td>
                                            <td>{{ $request->customer->name }}</td>
                                            <td>{{ $request->service->name }}</td>
                                            <td>
                                                @if($request->status == 'pending')
                                                    <span class="badge badge-warning">قيد الانتظار</span>
                                                @elseif($request->status == 'in_progress')
                                                    <span class="badge badge-info">قيد التنفيذ</span>
                                                @elseif($request->status == 'completed')
                                                    <span class="badge badge-success">مكتمل</span>
                                                @elseif($request->status == 'cancelled')
                                                    <span class="badge badge-danger">ملغي</span>
                                                @endif
                                            </td>
                                            <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('agency.requests.index') }}" class="btn btn-sm btn-primary">عرض كل الطلبات</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- آخر عروض الأسعار -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">آخر عروض الأسعار</h6>
                </div>
                <div class="card-body">
                    @if($latestQuotes->isEmpty())
                        <div class="text-center text-muted">
                            <p>لا توجد عروض أسعار حتى الآن</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>السبوكيل</th>
                                        <th>الخدمة</th>
                                        <th>السعر</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latestQuotes as $quote)
                                        <tr>
                                            <td><a href="{{ route('agency.quotes.show', $quote) }}">#{{ $quote->id }}</a></td>
                                            <td>{{ $quote->subagent->name }}</td>
                                            <td>{{ $quote->request->service->name }}</td>
                                            <td>@formatPrice($quote->price, $quote->currency_code)</td>
                                            <td>
                                                @if($quote->status == 'pending')
                                                    <span class="badge badge-warning">قيد الانتظار</span>
                                                @elseif($quote->status == 'agency_approved')
                                                    <span class="badge badge-info">معتمد من الوكالة</span>
                                                @elseif($quote->status == 'agency_rejected')
                                                    <span class="badge badge-danger">مرفوض من الوكالة</span>
                                                @elseif($quote->status == 'customer_approved')
                                                    <span class="badge badge-success">مقبول من العميل</span>
                                                @elseif($quote->status == 'customer_rejected')
                                                    <span class="badge badge-danger">مرفوض من العميل</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('agency.quotes.index') }}" class="btn btn-sm btn-primary">عرض كل العروض</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // الطلبات حسب الشهور
        var requestsCtx = document.getElementById('requestsChart');
        var requestsChart = new Chart(requestsCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($requestsByMonth as $item)
                        "{{ $item['month'] }}",
                    @endforeach
                ],
                datasets: [{
                    label: 'عدد الطلبات',
                    data: [
                        @foreach($requestsByMonth as $item)
                            {{ $item['count'] }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // توزيع حالات الطلبات
        var statusCtx = document.getElementById('requestStatusChart');
        var statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['قيد الانتظار', 'قيد التنفيذ', 'مكتمل'],
                datasets: [{
                    data: [
                        {{ $stats['pending_requests_count'] }},
                        {{ $stats['in_progress_requests_count'] }},
                        {{ $stats['completed_requests_count'] }}
                    ],
                    backgroundColor: ['#f6c23e', '#36b9cc', '#1cc88a'],
                    hoverBackgroundColor: ['#e0b033', '#2ca8bb', '#17a673'],
                    hoverBorderColor: 'rgba(234, 236, 244, 1)',
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
