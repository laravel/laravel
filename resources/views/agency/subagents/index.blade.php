@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة السبوكلاء</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-users me-2"></i> إدارة السبوكلاء</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubagentModal">
                <i class="fas fa-plus-circle me-1"></i> إضافة سبوكيل جديد
            </button>
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
            <form action="{{ route('agency.subagents.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">بحث</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="اسم أو بريد إلكتروني">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ request('status') == '0' && request('status') !== null ? 'selected' : '' }}>معطل</option>
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

    <div class="card shadow mt-4">
        <div class="card-body">
            @if($subagents->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لا يوجد سبوكلاء حتى الآن.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">الاسم</th>
                                <th scope="col">البريد الإلكتروني</th>
                                <th scope="col">رقم الهاتف</th>
                                <th scope="col">الخدمات المتاحة</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">تاريخ الإضافة</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subagents as $subagent)
                                <tr>
                                    <td>{{ $subagent->id }}</td>
                                    <td>{{ $subagent->name }}</td>
                                    <td>{{ $subagent->email }}</td>
                                    <td>{{ $subagent->phone }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $subagent->services->count() }}</span>
                                    </td>
                                    <td>
                                        @if($subagent->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">معطل</span>
                                        @endif
                                    </td>
                                    <td>{{ $subagent->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('agency.subagents.show', $subagent) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('agency.subagents.edit', $subagent) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-{{ $subagent->is_active ? 'danger' : 'success' }}" data-bs-toggle="modal" data-bs-target="#statusModal{{ $subagent->id }}">
                                                <i class="fas fa-{{ $subagent->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Modal تغيير الحالة -->
                                        <div class="modal fade" id="statusModal{{ $subagent->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $subagent->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="statusModalLabel{{ $subagent->id }}">تأكيد تغيير الحالة</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        هل أنت متأكد من تغيير حالة السبوكيل "{{ $subagent->name }}" إلى {{ $subagent->is_active ? 'معطل' : 'نشط' }}؟
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <form action="{{ route('agency.subagents.toggle-status', $subagent) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-{{ $subagent->is_active ? 'danger' : 'success' }}">
                                                                {{ $subagent->is_active ? 'تعطيل' : 'تنشيط' }} السبوكيل
                                                            </button>
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
                    {{ $subagents->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal إضافة سبوكيل جديد -->
<div class="modal fade" id="addSubagentModal" tabindex="-1" aria-labelledby="addSubagentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('agency.subagents.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addSubagentModalLabel">إضافة سبوكيل جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">الاسم الكامل*</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">البريد الإلكتروني*</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">رقم الهاتف*</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">كلمة المرور*</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الخدمات المتاحة (اختياري)</label>
                        <div class="row">
                            @foreach(\App\Models\Service::where('agency_id', auth()->user()->agency_id)
                                        ->where('status', 'active')
                                        ->get() as $service)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="services[]" value="{{ $service->id }}" id="service{{ $service->id }}">
                                        <label class="form-check-label" for="service{{ $service->id }}">
                                            {{ $service->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة السبوكيل</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
