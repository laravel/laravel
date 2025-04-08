@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">عروض الأسعار</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-tag me-2"></i> عروض الأسعار</h2>
            <p class="text-muted">استعرض عروض الأسعار المقدمة لطلباتك واختر العرض المناسب</p>
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
            <form action="{{ route('customer.quotes.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="request_id" class="form-label">الطلب</label>
                    <select class="form-select" id="request_id" name="request_id">
                        <option value="">كل الطلبات</option>
                        @foreach($requests as $req)
                            <option value="{{ $req->id }}" {{ request('request_id') == $req->id ? 'selected' : '' }}>
                                #{{ $req->id }} - {{ $req->service->name }} ({{ $req->created_at->format('Y-m-d') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار الموافقة</option>
                        <option value="agency_approved" {{ request('status') == 'agency_approved' ? 'selected' : '' }}>معتمد من الوكالة</option>
                        <option value="agency_rejected" {{ request('status') == 'agency_rejected' ? 'selected' : '' }}>مرفوض من الوكالة</option>
                        <option value="customer_approved" {{ request('status') == 'customer_approved' ? 'selected' : '' }}>مقبول</option>
                        <option value="customer_rejected" {{ request('status') == 'customer_rejected' ? 'selected' : '' }}>مرفوض</option>
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
                                <th scope="col">الحالة</th>
                                <th scope="col">تاريخ العرض</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotes as $quote)
                                <tr>
                                    <td>{{ $quote->id }}</td>
                                    <td>
                                        <a href="{{ route('customer.requests.show', $quote->request) }}">
                                            #{{ $quote->request_id }}
                                        </a>
                                    </td>
                                    <td>{{ $quote->request->service->name }}</td>
                                    <td>{{ $quote->subagent->name }}</td>
                                    <td>{{ $quote->price }} ر.س</td>
                                    <td>
                                        @if($quote->status == 'pending')
                                            <span class="badge bg-warning">بانتظار الموافقة</span>
                                        @elseif($quote->status == 'agency_approved')
                                            <span class="badge bg-info">معتمد من الوكالة</span>
                                        @elseif($quote->status == 'agency_rejected')
                                            <span class="badge bg-danger">مرفوض من الوكالة</span>
                                        @elseif($quote->status == 'customer_approved')
                                            <span class="badge bg-success">مقبول</span>
                                        @elseif($quote->status == 'customer_rejected')
                                            <span class="badge bg-danger">مرفوض</span>
                                        @endif
                                    </td>
                                    <td>{{ $quote->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('customer.quotes.show', $quote) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($quote->status == 'agency_approved')
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $quote->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $quote->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                
                                                <!-- Modal القبول -->
                                                <div class="modal fade" id="approveModal{{ $quote->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">تأكيد قبول العرض</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>هل أنت متأكد من قبول عرض السعر هذا بقيمة {{ $quote->price }} ر.س؟</p>
                                                                <p class="text-muted">ملاحظة: عند قبول هذا العرض، سيتم رفض باقي العروض المقدمة لنفس الطلب تلقائياً.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                <form action="{{ route('customer.quotes.approve', $quote) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success">تأكيد القبول</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal الرفض -->
                                                <div class="modal fade" id="rejectModal{{ $quote->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">تأكيد رفض العرض</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>هل أنت متأكد من رفض عرض السعر هذا؟</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                <form action="{{ route('customer.quotes.reject', $quote) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
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
