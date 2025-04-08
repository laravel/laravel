@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة الطلبات</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-file-alt me-2"></i> إدارة الطلبات</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('agency.requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> إضافة طلب جديد
            </a>
        </div>
    </div>

    <!-- رسائل النجاح والخطأ -->
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

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search me-1"></i> بحث وتصفية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('agency.requests.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">بحث</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="رقم الطلب أو التفاصيل">
                </div>
                <div class="col-md-3">
                    <label for="service" class="form-label">الخدمة</label>
                    <select class="form-select" id="service" name="service">
                        <option value="">كل الخدمات</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ request('service') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="priority" class="form-label">الأولوية</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">كل الأولويات</option>
                        <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>عادي</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>مستعجل</option>
                        <option value="emergency" {{ request('priority') == 'emergency' ? 'selected' : '' }}>طارئ</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mt-4">
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
                                <th scope="col">العميل</th>
                                <th scope="col">الخدمة</th>
                                <th scope="col">الأولوية</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">تاريخ الطلب</th>
                                <th scope="col">عروض الأسعار</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->customer->name }}</td>
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
                                        <span class="badge bg-primary">{{ $request->quotes->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('agency.requests.show', $request) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('agency.requests.edit', $request) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $request->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Modal حذف -->
                                        <div class="modal fade" id="deleteModal{{ $request->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $request->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $request->id }}">تأكيد الحذف</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        هل أنت متأكد من رغبتك في حذف هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <form action="{{ route('agency.requests.destroy', $request) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
