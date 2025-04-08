@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.subagents.index') }}">إدارة السبوكلاء</a></li>
    <li class="breadcrumb-item active">تعديل بيانات السبوكيل</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-edit me-2"></i> تعديل بيانات السبوكيل: {{ $subagent->name }}</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-1"></i> المعلومات الأساسية</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.subagents.update', $subagent) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="name" class="form-label">الاسم الكامل*</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $subagent->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="email" class="form-label">البريد الإلكتروني*</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $subagent->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="phone" class="form-label">رقم الهاتف*</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $subagent->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
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
                        
                        <div class="row">
                            <div class="col-md-4 offset-md-4 mt-3">
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="fas fa-save me-1"></i> حفظ التعديلات
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs me-1"></i> الخدمات المتاحة للسبوكيل</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agency.subagents.update-services', $subagent) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>الخدمة</th>
                                        <th>النوع</th>
                                        <th>السعر الأساسي</th>
                                        <th>نسبة العمولة (%)</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $service)
                                        @php
                                            $attached = $subagent->services->contains($service->id);
                                            $pivot = $attached ? $subagent->services->find($service->id)->pivot : null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="services[{{ $service->id }}][active]" id="service{{ $service->id }}" value="1" {{ $attached && $pivot->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="service{{ $service->id }}">
                                                        {{ $service->name }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                @if($service->type == 'security_approval')
                                                    <span class="badge bg-secondary">موافقة أمنية</span>
                                                @elseif($service->type == 'transportation')
                                                    <span class="badge bg-info">نقل بري</span>
                                                @elseif($service->type == 'hajj_umrah')
                                                    <span class="badge bg-success">حج وعمرة</span>
                                                @elseif($service->type == 'flight')
                                                    <span class="badge bg-primary">تذاكر طيران</span>
                                                @elseif($service->type == 'passport')
                                                    <span class="badge bg-warning">إصدار جوازات</span>
                                                @else
                                                    <span class="badge bg-dark">أخرى</span>
                                                @endif
                                            </td>
                                            <td>{{ $service->base_price }} ر.س</td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="services[{{ $service->id }}][commission_rate]" value="{{ $attached ? $pivot->custom_commission_rate : $service->commission_rate }}">
                                            </td>
                                            <td>
                                                @if($service->status == 'active')
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">غير نشط</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 offset-md-4 mt-3">
                                <button type="submit" class="btn btn-success w-100 py-2">
                                    <i class="fas fa-save me-1"></i> حفظ الخدمات المتاحة
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-1"></i> إجراءات إضافية</h5>
                </div>
                <div class="card-body row">
                    <div class="col-md-6">
                        <form action="{{ route('agency.subagents.toggle-status', $subagent) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $subagent->is_active ? 'danger' : 'success' }} w-100 mb-3">
                                <i class="fas fa-{{ $subagent->is_active ? 'ban' : 'check' }} me-1"></i> 
                                {{ $subagent->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}
                            </button>
                        </form>
                    </div>
                    
                    <div class="col-md-6">
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteSubagentModal">
                            <i class="fas fa-trash me-1"></i> حذف السبوكيل
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تأكيد الحذف -->
<div class="modal fade" id="deleteSubagentModal" tabindex="-1" aria-labelledby="deleteSubagentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteSubagentModalLabel">تأكيد حذف السبوكيل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في حذف السبوكيل <strong>{{ $subagent->name }}</strong>؟</p>
                <p class="text-danger"><strong>تحذير:</strong> سيتم حذف جميع بيانات السبوكيل بما في ذلك العروض المقدمة. هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('agency.subagents.destroy', $subagent) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
