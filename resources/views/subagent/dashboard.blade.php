@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item active">لوحة تحكم السبوكيل</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">لوحة تحكم السبوكيل</h1>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-4">
                        <div class="card-body">
                            <h5 class="card-title">الخدمات المتاحة</h5>
                            <p class="card-text display-4">{{ auth()->user()->services->count() }}</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('subagent.services.index') }}">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-4">
                        <div class="card-body">
                            <h5 class="card-title">طلبات عروض الأسعار</h5>
                            <p class="card-text display-4">{{ \App\Models\Request::whereHas('service', function($query) {
                                $query->whereHas('subagents', function($q) {
                                    $q->where('users.id', auth()->id());
                                });
                            })->count() }}</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('subagent.requests.index') }}">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-4">
                        <div class="card-body">
                            <h5 class="card-title">عروضي المقبولة</h5>
                            <p class="card-text display-4">{{ \App\Models\Quote::where('subagent_id', auth()->id())->whereIn('status', ['agency_approved', 'customer_approved'])->count() }}</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('subagent.quotes.index') }}">عرض التفاصيل</a>
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
                            أحدث طلبات عروض الأسعار
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>الخدمة</th>
                                            <th>الأولوية</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الطلب</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $requests = \App\Models\Request::whereHas('service', function($query) {
                                                $query->whereHas('subagents', function($q) {
                                                    $q->where('users.id', auth()->id());
                                                });
                                            })->latest()->take(5)->get();
                                        @endphp
                                        
                                        @forelse($requests as $request)
                                            <tr>
                                                <td>{{ $request->id }}</td>
                                                <td>{{ $request->service->name }}</td>
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
                                                    <a href="{{ route('subagent.requests.show', $request) }}" class="btn btn-sm btn-primary">عرض</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">لا توجد طلبات عروض أسعار حتى الآن</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('subagent.requests.index') }}" class="btn btn-sm btn-primary">عرض كل الطلبات</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            عروضي حسب الحالة
                        </div>
                        <div class="card-body">
                            <canvas id="quotesChart" width="100%" height="200"></canvas>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-money-bill me-1"></i>
                            ملخص المستحقات المالية
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-6 mb-3">
                                    <div class="h5">المستحقات</div>
                                    <div class="h3 text-success">
                                        {{ \App\Models\Transaction::where('user_id', auth()->id())->where('type', 'commission')->where('status', 'completed')->sum('amount') }} ر.س
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="h5">قيد التسوية</div>
                                    <div class="h3 text-warning">
                                        {{ \App\Models\Transaction::where('user_id', auth()->id())->where('type', 'commission')->where('status', 'pending')->sum('amount') }} ر.س
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('subagent.transactions.index') }}" class="btn btn-primary btn-sm d-block mt-2">تفاصيل الحساب</a>
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
        var ctx = document.getElementById('quotesChart').getContext('2d');
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['قيد الانتظار', 'معتمد (وكيل)', 'معتمد (عميل)', 'مرفوض'],
                datasets: [{
                    label: 'عدد العروض',
                    data: [
                        {{ \App\Models\Quote::where('subagent_id', auth()->id())->where('status', 'pending')->count() }},
                        {{ \App\Models\Quote::where('subagent_id', auth()->id())->where('status', 'agency_approved')->count() }},
                        {{ \App\Models\Quote::where('subagent_id', auth()->id())->where('status', 'customer_approved')->count() }},
                        {{ \App\Models\Quote::where('subagent_id', auth()->id())->whereIn('status', ['agency_rejected', 'customer_rejected'])->count() }}
                    ],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
