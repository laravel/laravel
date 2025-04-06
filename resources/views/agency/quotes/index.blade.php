@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة عروض الأسعار</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-tags me-2"></i> إدارة عروض الأسعار</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('agency.quotes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> إنشاء عرض سعر
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
            <form action="{{ route('agency.quotes.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار الموافقة</option>
                        <option value="agency_approved" {{ request('status') == 'agency_approved' ? 'selected' : '' }}>معتمد من الوكيل</option>
                        <option value="agency_rejected" {{ request('status') == 'agency_rejected' ? 'selected' : '' }}>مرفوض من الوكيل</option>
                        <option value="customer_approved" {{ request('status') == 'customer_approved' ? 'selected' : '' }}>معتمد من العميل</option>
                        <option value="customer_rejected" {{ request('status') == 'customer_rejected' ? 'selected' : '' }}>مرفوض من العميل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="subagent_id" class="form-label">السبوكيل</label>
                    <select class="form-select" id="subagent_id" name="subagent_id">
                        <option value="">كل السبوكلاء</option>
                        @foreach($subagents as $subagent)
                            <option value="{{ $subagent->id }}" {{ request('subagent_id') == $subagent->id ? 'selected' : '' }}>{{ $subagent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="service_id" class="form-label">الخدمة</label>
                    <select class="form-select" id="service_id" name="service_id">
                        <option value="">كل الخدمات</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-body">
            @if($quotes->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لا توجد عروض أسعار متاحة حالياً.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">رقم الطلب</th>
                                <th scope="col">الخدمة</th>
                                <th scope="col">السبوكيل</th>
                                <th scope="col">السعر</th>
                                <th scope="col">العمولة</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">تاريخ العرض</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotes as $quote)
                                <tr>
                                    <td>{{ $quote->id }}</td>
                                    <td>{{ $quote->request->id }}</td>
                                    <td>{{ $quote->request->service->name }}</td>
                                    <td>{{ $quote->subagent->name }}</td>
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
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('agency.quotes.show', $quote) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('agency.quotes.edit', $quote) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            @if($quote->status == 'pending')
                                                <form action="{{ route('agency.quotes.approve', $quote) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('agency.quotes.reject', $quote) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- الترقيم -->
                <div class="mt-4">
                    {{ $quotes->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
