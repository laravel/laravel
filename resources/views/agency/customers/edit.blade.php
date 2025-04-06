@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.customers.index') }}">إدارة العملاء</a></li>
    <li class="breadcrumb-item active">تعديل بيانات العميل</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-edit me-2"></i> تعديل بيانات العميل: {{ $customer->name }}</h2>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-edit me-1"></i> نموذج تعديل العميل</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('agency.customers.update', $customer) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">الاسم الكامل*</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">البريد الإلكتروني*</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label">رقم الهاتف*</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">كلمة المرور (اختياري)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="اتركها فارغة للاحتفاظ بكلمة المرور الحالية">
                        <small class="form-text text-muted">اتركها فارغة إذا كنت لا ترغب في تغيير كلمة المرور</small>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6 offset-md-3">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-save me-1"></i> حفظ التعديلات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> معلومات حساب العميل</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span>تاريخ الإنضمام:</span>
                        <strong>{{ $customer->created_at->format('Y-m-d') }}</strong>
                    </div>
                    
                    <div class="mb-3">
                        <span>الحالة:</span>
                        @if($customer->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-danger">معطل</span>
                        @endif
                    </div>
                    
                    <div>
                        <span>عدد الطلبات:</span>
                        <strong>{{ $customer->customerRequests->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-1"></i> إجراءات إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('agency.customers.toggle-status', $customer) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $customer->is_active ? 'danger' : 'success' }} w-100 mb-3">
                                <i class="fas fa-{{ $customer->is_active ? 'ban' : 'check' }} me-1"></i> 
                                {{ $customer->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}
                            </button>
                        </form>
                        
                        @if($customer->customerRequests->count() == 0)
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteCustomerModal">
                                <i class="fas fa-trash me-1"></i> حذف العميل
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary w-100" disabled title="لا يمكن حذف عميل لديه طلبات">
                                <i class="fas fa-trash me-1"></i> حذف العميل
                            </button>
                            <small class="text-muted mt-1">لا يمكن حذف عميل لديه طلبات مسجلة</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تأكيد الحذف -->
<div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-labelledby="deleteCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCustomerModalLabel">تأكيد حذف العميل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في حذف العميل <strong>{{ $customer->name }}</strong>؟</p>
                <p class="text-danger"><strong>تحذير:</strong> هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('agency.customers.destroy', $customer) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
