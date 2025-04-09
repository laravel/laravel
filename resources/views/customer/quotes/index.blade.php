@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">عروض الأسعار</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-file-invoice-dollar me-2"></i> عروض الأسعار</h2>
            <p class="text-muted">استعرض عروض الأسعار المقدمة لطلباتك</p>
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

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search me-1"></i> بحث وتصفية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('customer.quotes.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="agency_approved" {{ request('status') == 'agency_approved' ? 'selected' : '' }}>متاح للقبول</option>
                        <option value="customer_approved" {{ request('status') == 'customer_approved' ? 'selected' : '' }}>تم القبول</option>
                        <option value="customer_rejected" {{ request('status') == 'customer_rejected' ? 'selected' : '' }}>تم الرفض</option>
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
                <div class="col-md-4">
                    <label class="d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($quotes->isEmpty())
                <div class="text-center p-5">
                    <img src="{{ asset('img/no-data.svg') }}" alt="لا توجد بيانات" width="120" class="mb-3">
                    <h5>لا توجد عروض أسعار متاحة</h5>
                    <p class="text-muted">لم يتم تقديم أي عروض أسعار لطلباتك حتى الآن.</p>
                    <a href="{{ route('customer.services.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> استعرض الخدمات وقدم طلباً
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">الخدمة</th>
                                <th scope="col">مقدم العرض</th>
                                <th scope="col">السعر</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">تاريخ العرض</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotes as $quote)
                                <tr>
                                    <td>{{ $quote->id }}</td>
                                    <td>{{ $quote->request->service->name }}</td>
                                    <td>{{ $quote->subagent->name }}</td>
                                    <td><strong class="text-primary">{{ number_format($quote->price, 2) }} {{ $quote->currency_code }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $quote->status_badge }}">{{ $quote->status_text }}</span>
                                    </td>
                                    <td>{{ $quote->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('customer.quotes.show', $quote) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i> عرض
                                        </a>
                                        
                                        @if($quote->status == 'agency_approved')
                                            <div class="btn-group">
                                                <form action="{{ route('customer.quotes.approve', $quote) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟')">
                                                        <i class="fas fa-check me-1"></i> قبول
                                                    </button>
                                                </form>
                                                <form action="{{ route('customer.quotes.reject', $quote) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من رفض هذا العرض؟')">
                                                        <i class="fas fa-times me-1"></i> رفض
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center p-3">
                    {{ $quotes->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
