@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subagent.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subagent.requests.index') }}">طلبات عروض الأسعار</a></li>
    <li class="breadcrumb-item active">تفاصيل الطلب #{{ $request->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-file-alt me-2"></i> تفاصيل الطلب #{{ $request->id }}</h2>
        </div>
        <div class="col-md-6 text-md-end">
            @php
                $hasQuote = $request->quotes()->where('subagent_id', auth()->id())->exists();
            @endphp
            
            @if(!$hasQuote)
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quoteModal">
                    <i class="fas fa-tag me-1"></i> تقديم عرض سعر
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- بطاقة تفاصيل الطلب -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> معلومات الطلب</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>رقم الطلب:</span>
                                    <strong>{{ $request->id }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>الخدمة:</span>
                                    <strong>{{ $request->service->name }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>الأولوية:</span>
                                    @if($request->priority == 'normal')
                                        <span class="badge bg-info">عادي</span>
                                    @elseif($request->priority == 'urgent')
                                        <span class="badge bg-warning">مستعجل</span>
                                    @else
                                        <span class="badge bg-danger">طارئ</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>تاريخ الطلب:</span>
                                    <strong>{{ $request->created_at->format('Y-m-d h:i A') }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>الحالة:</span>
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning">قيد الانتظار</span>
                                    @elseif($request->status == 'in_progress')
                                        <span class="badge bg-info">قيد التنفيذ</span>
                                    @elseif($request->status == 'completed')
                                        <span class="badge bg-success">مكتمل</span>
                                    @else
                                        <span class="badge bg-danger">ملغي</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>التاريخ المطلوب:</span>
                                    <strong>{{ $request->requested_date ?? 'غير محدد' }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <h6>تفاصيل الطلب:</h6>
                    <p class="border p-3 rounded">{{ $request->details ?? 'لا توجد تفاصيل إضافية' }}</p>
                </div>
            </div>
            
            <!-- عرض السعر الخاص بي -->
            @if($hasQuote)
                @php
                    $myQuote = $request->quotes()->where('subagent_id', auth()->id())->first();
                @endphp
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-tag me-1"></i> عرض السعر الخاص بي</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>رقم العرض:</span>
                                        <strong>{{ $myQuote->id }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>السعر:</span>
                                        <strong>{{ $myQuote->price }} ر.س</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>العمولة:</span>
                                        <strong>{{ $myQuote->commission_amount }} ر.س</strong>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>تاريخ العرض:</span>
                                        <strong>{{ $myQuote->created_at->format('Y-m-d h:i A') }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>حالة العرض:</span>
                                        @if($myQuote->status == 'pending')
                                            <span class="badge bg-warning">بانتظار الموافقة</span>
                                        @elseif($myQuote->status == 'agency_approved')
                                            <span class="badge bg-info">معتمد من الوكيل</span>
                                        @elseif($myQuote->status == 'agency_rejected')
                                            <span class="badge bg-danger">مرفوض من الوكيل</span>
                                        @elseif($myQuote->status == 'customer_approved')
                                            <span class="badge bg-success">معتمد من العميل</span>
                                        @elseif($myQuote->status == 'customer_rejected')
                                            <span class="badge bg-danger">مرفوض من العميل</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <hr>
                        <h6>تفاصيل العرض:</h6>
                        <p class="border p-3 rounded">{{ $myQuote->details ?? 'لا توجد تفاصيل إضافية' }}</p>
                        
                        @if($myQuote->status == 'pending')
                            <div class="mt-3">
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editQuoteModal">
                                    <i class="fas fa-edit me-1"></i> تعديل العرض
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <div class="col-md-4">
            <!-- الوثائق المرفقة -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file me-1"></i> المستندات</h5>
                </div>
                <div class="card-body">
                    @php
                        $documents = $request->documents()
                            ->whereIn('visibility', ['agency', 'customer'])
                            ->get();
                    @endphp
                    
                    @if($documents->isNotEmpty())
                        <ul class="list-group">
                            @foreach($documents as $document)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-{{ $document->file_type == 'pdf' ? 'pdf' : 'image' }} me-2"></i>
                                        {{ $document->name }}
                                    </div>
                                    <div>
                                        <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info">
                            لا توجد مستندات مرفقة بهذا الطلب.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- حالة الطلب -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-1"></i> تحديثات الحالة</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-badge bg-primary"><i class="fas fa-plus"></i></div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h6 class="timeline-title">تم إنشاء الطلب</h6>
                                    <p><small class="text-muted"><i class="fas fa-clock"></i> {{ $request->created_at->format('Y-m-d h:i A') }}</small></p>
                                </div>
                            </div>
                        </li>
                        @if($request->status != 'pending')
                            <li class="timeline-item">
                                <div class="timeline-badge bg-info"><i class="fas fa-sync"></i></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h6 class="timeline-title">تغيير الحالة إلى: {{ $request->status == 'in_progress' ? 'قيد التنفيذ' : ($request->status == 'completed' ? 'مكتمل' : 'ملغي') }}</h6>
                                        <p><small class="text-muted"><i class="fas fa-clock"></i> {{ $request->updated_at->format('Y-m-d h:i A') }}</small></p>
                                    </div>
                                </div>
                            </li>
                        @endif
                        @if($hasQuote)
                            <li class="timeline-item">
                                <div class="timeline-badge bg-success"><i class="fas fa-tag"></i></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h6 class="timeline-title">تم تقديم عرض سعر</h6>
                                        <p><small class="text-muted"><i class="fas fa-clock"></i> {{ $myQuote->created_at->format('Y-m-d h:i A') }}</small></p>
                                    </div>
                                </div>
                            </li>
                            @if($myQuote->status != 'pending')
                                <li class="timeline-item">
                                    <div class="timeline-badge {{ in_array($myQuote->status, ['agency_approved', 'customer_approved']) ? 'bg-success' : 'bg-danger' }}">
                                        <i class="fas {{ in_array($myQuote->status, ['agency_approved', 'customer_approved']) ? 'fa-check' : 'fa-times' }}"></i>
                                    </div>
                                    <div class="timeline-panel">
                                        <div class="timeline-heading">
                                            <h6 class="timeline-title">
                                                @if($myQuote->status == 'agency_approved')
                                                    تمت الموافقة على العرض من قبل الوكيل
                                                @elseif($myQuote->status == 'agency_rejected')
                                                    تم رفض العرض من قبل الوكيل
                                                @elseif($myQuote->status == 'customer_approved')
                                                    تمت الموافقة على العرض من قبل العميل
                                                @else
                                                    تم رفض العرض من قبل العميل
                                                @endif
                                            </h6>
                                            <p><small class="text-muted"><i class="fas fa-clock"></i> {{ $myQuote->updated_at->format('Y-m-d h:i A') }}</small></p>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تقديم عرض سعر -->
<div class="modal fade" id="quoteModal" tabindex="-1" aria-labelledby="quoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('subagent.quotes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="request_id" value="{{ $request->id }}">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="quoteModalLabel">تقديم عرض سعر للطلب #{{ $request->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label">السعر (ر.س)*</label>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="commission_amount" class="form-label">مبلغ العمولة (ر.س)*</label>
                            <input type="number" class="form-control" id="commission_amount" name="commission_amount" min="0" step="0.01" required>
                            <div class="form-text">
                                هذا هو المبلغ الذي ستحصل عليه كعمولة
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="details" class="form-label">تفاصيل العرض*</label>
                        <textarea class="form-control" id="details" name="details" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تقديم العرض</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($hasQuote)
    <!-- Modal تعديل عرض السعر -->
    <div class="modal fade" id="editQuoteModal" tabindex="-1" aria-labelledby="editQuoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('subagent.quotes.update', $myQuote) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="editQuoteModalLabel">تعديل عرض السعر #{{ $myQuote->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price" class="form-label">السعر (ر.س)*</label>
                                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="{{ $myQuote->price }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="commission_amount" class="form-label">مبلغ العمولة (ر.س)*</label>
                                <input type="number" class="form-control" id="commission_amount" name="commission_amount" min="0" step="0.01" value="{{ $myQuote->commission_amount }}" required>
                                <div class="form-text">
                                    هذا هو المبلغ الذي ستحصل عليه كعمولة
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="details" class="form-label">تفاصيل العرض*</label>
                            <textarea class="form-control" id="details" name="details" rows="5" required>{{ $myQuote->details }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">تحديث العرض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<style>
.timeline {
    list-style: none;
    padding: 0;
    position: relative;
}

.timeline:before {
    top: 0;
    bottom: 0;
    position: absolute;
    content: " ";
    width: 3px;
    background-color: #eeeeee;
    right: 25px;
    margin-left: -1.5px;
}

.timeline > li {
    margin-bottom: 20px;
    position: relative;
}

.timeline > li:before,
.timeline > li:after {
    content: " ";
    display: table;
}

.timeline > li:after {
    clear: both;
}

.timeline > li > .timeline-panel {
    width: calc(100% - 75px);
    float: right;
    border: 1px solid #d4d4d4;
    border-radius: 2px;
    padding: 15px;
    position: relative;
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
    margin-right: 15px;
}

.timeline > li > .timeline-badge {
    color: #fff;
    width: 50px;
    height: 50px;
    line-height: 50px;
    font-size: 1.4em;
    text-align: center;
    position: absolute;
    top: 16px;
    right: 0;
    margin-right: 0;
    background-color: #999999;
    z-index: 100;
    border-radius: 50%;
}
</style>
@endsection
