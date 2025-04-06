@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subagent.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">الطلبات المتاحة</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-file-alt me-2"></i> الطلبات المتاحة</h2>
            <p class="text-muted">استعرض الطلبات المتاحة وقدم عروض أسعار للعملاء</p>
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
            <form action="{{ route('subagent.requests.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="service_id" class="form-label">الخدمة</label>
                    <select class="form-select" id="service_id" name="service_id">
                        <option value="">كل الخدمات</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
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
                    <i class="fas fa-info-circle me-1"></i> لا توجد طلبات متاحة حالياً.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">الخدمة</th>
                                <th scope="col">العميل</th>
                                <th scope="col">التفاصيل</th>
                                <th scope="col">التاريخ</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->service->name }}</td>
                                    <td>{{ $request->customer->name }}</td>
                                    <td>{{ Str::limit($request->details, 50) }}</td>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('subagent.requests.show', $request) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('subagent.requests.create-quote', $request) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-tag"></i> تقديم عرض
                                        </a>
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
