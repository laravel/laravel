@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">عروض الأسعار</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-file-invoice-dollar me-2"></i> إدارة عروض الأسعار</h2>
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
            <form action="{{ route('agency.quotes.index') }}" method="GET" class="row">
                <div class="col-md-3">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار الموافقة</option>
                        <option value="agency_approved" {{ request('status') == 'agency_approved' ? 'selected' : '' }}>معتمد من الوكالة</option>
                        <option value="customer_approved" {{ request('status') == 'customer_approved' ? 'selected' : '' }}>مقبول من العميل</option>
                        <option value="agency_rejected" {{ request('status') == 'agency_rejected' ? 'selected' : '' }}>مرفوض من الوكالة</option>
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

    <div class="card shadow-sm">
        <div class="card-body">
            @if($quotes->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لا توجد عروض أسعار متطابقة مع معايير البحث.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">الطلب</th>
                                <th scope="col">الخدمة</th>
                                <th scope="col">السبوكيل</th>
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
                                    <td><a href="{{ route('agency.requests.show', $quote->request_id) }}">#{{ $quote->request_id }}</a></td>
                                    <td>{{ $quote->request->service->name }}</td>
                                    <td>{{ $quote->subagent->name }}</td>
                                    <td>{{ $quote->request->customer->name }}</td>
                                    <td>{{ number_format($quote->price, 2) }} {{ $quote->currency_code }}</td>
                                    <td>{{ number_format($quote->commission_amount, 2) }} {{ $quote->currency_code }}</td>
                                    <td>
                                        <span class="badge bg-{{ $quote->status_badge }}">{{ $quote->status_text }}</span>
                                    </td>
                                    <td>{{ $quote->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('agency.quotes.show', $quote) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($quote->status == 'pending')
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $quote->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $quote->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                
                                                <!-- Modal تأكيد الموافقة -->
                                                <div class="modal fade" id="approveModal{{ $quote->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">تأكيد الموافقة على عرض السعر</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>هل أنت متأكد من الموافقة على عرض السعر هذا؟</p>
                                                                <p>سيتم عرض هذا العرض للعميل للموافقة عليه.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                <form action="{{ route('agency.quotes.approve', $quote) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success">تأكيد الموافقة</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal تأكيد الرفض -->
                                                <div class="modal fade" id="rejectModal{{ $quote->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">رفض عرض السعر</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('agency.quotes.reject', $quote) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="rejection_reason{{ $quote->id }}" class="form-label">سبب الرفض</label>
                                                                        <textarea class="form-control" id="rejection_reason{{ $quote->id }}" name="rejection_reason" rows="3" required></textarea>
                                                                        <small class="form-text text-muted">سيتم إرسال هذا السبب إلى السبوكيل</small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                    <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                                                                </div>
                                                            </form>
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
