@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.settings.index') }}">الإعدادات</a></li>
    <li class="breadcrumb-item active">إدارة العملات</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-money-bill-wave me-2"></i> إدارة العملات</h2>
            <p class="text-muted">إدارة العملات المستخدمة في النظام وتحديث أسعار الصرف</p>
        </div>
        <div class="col-md-4 text-md-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCurrencyModal">
                <i class="fas fa-plus-circle me-1"></i> إضافة عملة جديدة
            </button>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-1"></i> قائمة العملات</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>الرمز</th>
                                    <th>الاسم</th>
                                    <th>الرمز</th>
                                    <th>سعر الصرف</th>
                                    <th>الحالة</th>
                                    <th>العملة الافتراضية</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currencies as $currency)
                                    <tr>
                                        <td>{{ $currency->code }}</td>
                                        <td>{{ $currency->name }}</td>
                                        <td>{{ $currency->symbol }}</td>
                                        <td>{{ $currency->exchange_rate }}</td>
                                        <td>
                                            @if($currency->is_active)
                                                <span class="badge bg-success">نشطة</span>
                                            @else
                                                <span class="badge bg-danger">غير نشطة</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($currency->is_default)
                                                <span class="badge bg-primary">العملة الافتراضية</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editCurrencyModal{{ $currency->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if(!$currency->is_default)
                                                    <form action="{{ route('agency.settings.currencies.set-default', $currency) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-warning" title="تعيين كعملة افتراضية">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('agency.settings.currencies.toggle-status', $currency) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm {{ $currency->is_active ? 'btn-danger' : 'btn-success' }}" title="{{ $currency->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                            <i class="fas fa-{{ $currency->is_active ? 'ban' : 'check' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('agency.settings.currencies.destroy', $currency) }}" method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>

                                            <!-- Modal تعديل العملة -->
                                            <div class="modal fade" id="editCurrencyModal{{ $currency->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">تعديل العملة - {{ $currency->code }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('agency.settings.currencies.update', $currency) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="name{{ $currency->id }}" class="form-label">الاسم</label>
                                                                    <input type="text" class="form-control" id="name{{ $currency->id }}" name="name" value="{{ $currency->name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="symbol{{ $currency->id }}" class="form-label">الرمز</label>
                                                                    <input type="text" class="form-control" id="symbol{{ $currency->id }}" name="symbol" value="{{ $currency->symbol }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="exchange_rate{{ $currency->id }}" class="form-label">سعر الصرف</label>
                                                                    <input type="number" step="0.0001" min="0.0001" class="form-control" id="exchange_rate{{ $currency->id }}" name="exchange_rate" value="{{ $currency->exchange_rate }}" required {{ $currency->is_default ? 'disabled' : '' }}>
                                                                    @if($currency->is_default)
                                                                        <small class="form-text text-muted">لا يمكن تغيير سعر الصرف للعملة الافتراضية (دائماً = 1).</small>
                                                                        <input type="hidden" name="exchange_rate" value="1.0000">
                                                                    @else
                                                                        <small class="form-text text-muted">سعر الصرف مقارنة بالعملة الافتراضية ({{ App\Models\Currency::where('is_default', true)->first()->code }}).</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> معلومات حول العملات</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-primary">
                        <h6><i class="fas fa-star me-1"></i> العملة الافتراضية</h6>
                        <p class="mb-0">العملة الافتراضية هي: <strong>{{ App\Models\Currency::where('is_default', true)->first()->name }} ({{ App\Models\Currency::where('is_default', true)->first()->code }})</strong></p>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-1"></i> معلومات مهمة</h6>
                        <ul class="mb-0">
                            <li>العملة الافتراضية دائماً لها سعر صرف = 1</li>
                            <li>جميع الأسعار تعتمد على سعر الصرف مقارنة بالعملة الافتراضية</li>
                            <li>تغيير العملة الافتراضية يؤدي إلى إعادة حساب أسعار صرف جميع العملات</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-1"></i> تحذير</h6>
                        <p class="mb-0">تعطيل أو حذف العملات قد يؤثر على الخدمات وعروض الأسعار المرتبطة بها.</p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-sync-alt me-1"></i> تحديث أسعار الصرف</h5>
                </div>
                <div class="card-body">
                    <p>يمكنك تحديث أسعار الصرف يدوياً أو استيرادها تلقائياً.</p>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-success">
                            <i class="fas fa-cloud-download-alt me-1"></i> استيراد أسعار الصرف
                        </a>
                        <small class="text-muted text-center">هذه الميزة ستكون متاحة في الإصدار القادم</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة عملة جديدة -->
<div class="modal fade" id="addCurrencyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة عملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('agency.settings.currencies.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="code" class="form-label">رمز العملة (ISO code)</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="SAR, USD, EUR" required maxlength="3" minlength="3">
                        <small class="form-text text-muted">ادخل رمز العملة المكون من 3 أحرف وفقاً لمعيار ISO (مثال: SAR للريال السعودي)</small>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="الريال السعودي" required>
                    </div>
                    <div class="mb-3">
                        <label for="symbol" class="form-label">الرمز</label>
                        <input type="text" class="form-control" id="symbol" name="symbol" placeholder="ر.س" required>
                    </div>
                    <div class="mb-3">
                        <label for="exchange_rate" class="form-label">سعر الصرف</label>
                        <input type="number" step="0.0001" min="0.0001" class="form-control" id="exchange_rate" name="exchange_rate" placeholder="3.75" required>
                        <small class="form-text text-muted">سعر الصرف مقارنة بالعملة الافتراضية ({{ App\Models\Currency::where('is_default', true)->first()->code }})</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة العملة</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تأكيد حذف العملة
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('هل أنت متأكد من رغبتك في حذف هذه العملة؟')) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
