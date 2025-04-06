@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">طلباتي</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-file-alt me-2"></i> طلباتي</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('customer.requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> طلب جديد
            </a>
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

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search me-1"></i> بحث وتصفية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('customer.requests.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="service_id" class="form-label">الخدمة</label>
                    <select class="form-select" id="service_id" name="service_id">
                        <option value="">كل الخدمات</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @if($requests->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لا توجد طلبات حتى الآن.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">الخدمة</th>
                                <th scope="col">التفاصيل</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">التاريخ</th>
                                <th scope="col">عروض الأسعار</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->service->name }}</td>
                                    <td>{{ Str::limit($request->details, 50) }}</td>
                                    <td>
                                        @if($request->status == 'pending')
                                            <span class="badge bg-warning">قيد الانتظار</span>
                                        @elseif($request->status == 'in_progress')
                                            <span class="badge bg-info">قيد التنفيذ</span>
                                        @elseif($request->status == 'completed')
                                            <span class="badge bg-success">مكتمل</span>
                                        @elseif($request->status == 'cancelled')
                                            <span class="badge bg-danger">ملغي</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $request->quotes->count() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.requests.show', $request) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($request->status == 'pending' || $request->status == 'in_progress')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $request->id }}">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            
                                            <!-- Modal الإلغاء -->
                                            <div class="modal fade" id="cancelModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">تأكيد إلغاء الطلب</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            هل أنت متأكد من رغبتك في إلغاء هذا الطلب؟
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                            <form action="{{ route('customer.requests.cancel', $request) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- الترقيم -->
                <div class="mt-4">
                    {{ $requests->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
