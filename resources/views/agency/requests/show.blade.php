@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.requests.index') }}">إدارة الطلبات</a></li>
    <li class="breadcrumb-item active">تفاصيل الطلب #{{ $request->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-file-alt me-2"></i> تفاصيل الطلب #{{ $request->id }}</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('agency.requests.edit', $request) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> تعديل الطلب
            </a>
            <a href="{{ route('agency.quotes.create', ['request_id' => $request->id]) }}" class="btn btn-success">
                <i class="fas fa-plus-circle me-1"></i> إضافة عرض سعر
            </a>
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
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>تاريخ الطلب:</span>
                                    <strong>{{ $request->created_at->format('Y-m-d h:i A') }}</strong>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>العميل:</span>
                                    <strong>{{ $request->customer->name }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>رقم الهاتف:</span>
                                    <strong>{{ $request->customer->phone }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>البريد الإلكتروني:</span>
                                    <strong>{{ $request->customer->email }}</strong>
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
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <h6>تفاصيل الطلب:</h6>
                    <p class="border p-3 rounded">{{ $request->details ?? 'لا توجد تفاصيل إضافية' }}</p>
                    
                    <!-- تغيير حالة الطلب -->
                    <div class="mt-4">
                        <h6>تغيير حالة الطلب:</h6>
                        <form action="{{ route('agency.requests.update_status', $request) }}" method="POST" class="row g-2">
                            @csrf
                            @method('PATCH')
                            <div class="col-md-8">
                                <select name="status" class="form-select">
                                    <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="in_progress" {{ $request->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                    <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">تحديث الحالة</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- عروض الأسعار -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-tags me-1"></i> عروض الأسعار ({{ $request->quotes->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($request->quotes->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">السبوكيل</th>
                                        <th scope="col">السعر</th>
                                        <th scope="col">العمولة</th>
                                        <th scope="col">الحالة</th>
                                        <th scope="col">تاريخ العرض</th>
                                        <th scope="col">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request->quotes as $quote)
                                        <tr>
                                            <td>{{ $quote->id }}</td>
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
                                                    
                                                    @if($quote->status == 'pending')
                                                        <form action="{{ route('agency.quotes.approve', $quote) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        
                                                        <form action="{{ route('agency.quotes.reject', $quote) }}" method="POST">
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
                    @else
                        <div class="alert alert-info">
                            لا توجد عروض أسعار لهذا الطلب حتى الآن.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- بطاقة إرسال للسبوكلاء -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-share-alt me-1"></i> إرسال للسبوكلاء</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.requests.share', $request) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="subagents" class="form-label">اختر السبوكلاء</label>
                            <select id="subagents" name="subagents[]" class="form-select" multiple size="8">
                                @foreach(\App\Models\User::where('agency_id', auth()->user()->agency_id)
                                        ->where('user_type', 'subagent')
                                        ->get() as $subagent)
                                    <option value="{{ $subagent->id }}">{{ $subagent->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">اضغط CTRL للاختيار المتعدد</div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">رسالة (اختياري)</label>
                            <textarea id="message" name="message" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-paper-plane me-1"></i> إرسال الطلب
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- بطاقة المستندات -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file me-1"></i> المستندات</h5>
                </div>
                <div class="card-body">
                    @if($request->documents->isNotEmpty())
                        <ul class="list-group">
                            @foreach($request->documents as $document)
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
                    
                    <hr>
                    
                    <form action="{{ route('agency.documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="request_id" value="{{ $request->id }}">
                        <div class="mb-3">
                            <label for="document_name" class="form-label">اسم المستند</label>
                            <input type="text" class="form-control" id="document_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="document_file" class="form-label">ملف المستند</label>
                            <input type="file" class="form-control" id="document_file" name="file" required>
                        </div>
                        <div class="mb-3">
                            <label for="visibility" class="form-label">الرؤية</label>
                            <select id="visibility" name="visibility" class="form-select">
                                <option value="private">خاص (الوكالة فقط)</option>
                                <option value="agency">الوكالة والسبوكلاء</option>
                                <option value="customer">الكل (بما في ذلك العميل)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info w-100">
                            <i class="fas fa-upload me-1"></i> رفع مستند
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
