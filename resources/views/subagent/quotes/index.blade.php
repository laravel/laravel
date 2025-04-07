@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subagent.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">عروض الأسعار</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-tag me-2"></i> عروض الأسعار</h2>
            <p class="text-muted">استعرض عروض الأسعار التي قمت بتقديمها وتتبع حالتها</p>
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

    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search me-1"></i> بحث وتصفية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('subagent.quotes.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="request_id" class="form-label">الطلب</label>
                    <select class="form-select" id="request_id" name="request_id">
                        <option value="">كل الطلبات</option>
                        @foreach($requests as $req)
                            <option value="{{ $req->id }}" {{ request('request_id') == $req->id ? 'selected' : '' }}>
                                #{{ $req->id }} - {{ $req->service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار الموافقة</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليها</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="service_id" class="form-label">الخدمة</label>
                    <select class="form-select" id="service_id" name="service_id">
                        <option value="">كل الخدمات</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('subagent.quotes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @if($quotes->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لم تقم بتقديم أي عروض أسعار حتى الآن.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">الطلب</th>
                                <th scope="col">الخدمة</th>
                                <th scope="col">العميل</th>
                                <th scope="col">السعر</th>
                                <th scope="col">العمولة</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">تاريخ التقديم</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotes as $quote)
                                <tr>
                                    <td>{{ $quote->id }}</td>
                                    <td>#{{ $quote->request_id }}</td>
                                    <td>{{ $quote->request->service->name }}</td>
                                    <td>{{ $quote->request->customer->name }}</td>
                                    <td>{{ $quote->price }} ر.س</td>
                                    <td>{{ $quote->commission_amount }} ر.س</td>
                                    <td>
                                        @if($quote->status == 'pending')
                                            <span class="badge bg-warning">بانتظار الموافقة</span>
                                        @elseif($quote->status == 'agency_approved')
                                            <span class="badge bg-info">معتمد من الوكالة</span>
                                        @elseif($quote->status == 'agency_rejected')
                                            <span class="badge bg-danger">مرفوض من الوكالة</span>
                                        @elseif($quote->status == 'customer_approved')
                                            <span class="badge bg-success">مقبول من العميل</span>
                                        @elseif($quote->status == 'customer_rejected')
                                            <span class="badge bg-danger">مرفوض من العميل</span>
                                        @endif
                                    </td>
                                    <td>{{ $quote->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('subagent.quotes.show', $quote) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($quote->status == 'pending' || $quote->status == 'agency_rejected')
                                                <a href="{{ route('subagent.quotes.edit', $quote) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $quote->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                
                                                <!-- Modal إلغاء العرض -->
                                                <div class="modal fade" id="cancelModal{{ $quote->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">تأكيد إلغاء عرض السعر</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                هل أنت متأكد من رغبتك في إلغاء عرض السعر هذا؟
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                <form action="{{ route('subagent.quotes.destroy', $quote) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
