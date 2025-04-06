@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">المعاملات المالية</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-money-bill me-2"></i> المعاملات المالية</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                <i class="fas fa-plus-circle me-1"></i> إضافة معاملة جديدة
            </button>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search me-1"></i> بحث وتصفية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('agency.transactions.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="user_type" class="form-label">نوع المستخدم</label>
                    <select class="form-select" id="user_type" name="user_type">
                        <option value="">الكل</option>
                        <option value="subagent">سبوكلاء</option>
                        <option value="customer">عملاء</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">نوع المعاملة</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">الكل</option>
                        <option value="payment">دفعة</option>
                        <option value="commission">عمولة</option>
                        <option value="refund">استرداد</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">الكل</option>
                        <option value="pending">معلق</option>
                        <option value="completed">مكتمل</option>
                        <option value="cancelled">ملغي</option>
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
            <div class="alert alert-info">
                <p>لا توجد معاملات مالية حالياً. يمكنك إنشاء معاملات جديدة باستخدام زر "إضافة معاملة جديدة".</p>
            </div>
            
            <div class="table-responsive d-none">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">المستخدم</th>
                            <th scope="col">النوع</th>
                            <th scope="col">المبلغ</th>
                            <th scope="col">الحالة</th>
                            <th scope="col">تاريخ المعاملة</th>
                            <th scope="col">ملاحظات</th>
                            <th scope="col">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ستتم إضافة البيانات الفعلية هنا -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة معاملة جديدة -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('agency.transactions.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addTransactionModalLabel">إضافة معاملة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">المستخدم*</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">اختر المستخدم</option>
                                <optgroup label="سبوكلاء">
                                    <!-- هنا نضيف السبوكلاء بشكل ديناميكي من قاعدة البيانات -->
                                </optgroup>
                                <optgroup label="عملاء">
                                    <!-- هنا نضيف العملاء بشكل ديناميكي من قاعدة البيانات -->
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="transaction_type" class="form-label">نوع المعاملة*</label>
                            <select class="form-select" id="transaction_type" name="type" required>
                                <option value="payment">دفعة</option>
                                <option value="commission">عمولة</option>
                                <option value="refund">استرداد</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="amount" class="form-label">المبلغ*</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" required>
                                <span class="input-group-text">ر.س</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">الحالة*</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">معلق</option>
                                <option value="completed">مكتمل</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة المعاملة</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
