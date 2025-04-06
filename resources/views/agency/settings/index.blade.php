@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">الإعدادات</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-cog me-2"></i> إعدادات النظام</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="list-group">
                <a href="#general-settings" class="list-group-item list-group-item-action active" data-bs-toggle="list">الإعدادات العامة</a>
                <a href="#notification-settings" class="list-group-item list-group-item-action" data-bs-toggle="list">إعدادات الإشعارات</a>
                <a href="#commission-settings" class="list-group-item list-group-item-action" data-bs-toggle="list">إعدادات العمولات</a>
                <a href="#appearance-settings" class="list-group-item list-group-item-action" data-bs-toggle="list">إعدادات المظهر</a>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="tab-content">
                <!-- الإعدادات العامة -->
                <div class="tab-pane fade show active" id="general-settings">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">الإعدادات العامة</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('agency.settings.update') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                <div class="alert alert-info">
                                    <p>صفحة الإعدادات قيد التطوير.</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="default_language" class="form-label">اللغة الافتراضية</label>
                                    <select class="form-select" id="default_language" name="default_language">
                                        <option value="ar" selected>العربية</option>
                                        <option value="en">الإنجليزية</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="timezone" class="form-label">المنطقة الزمنية</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <option value="Asia/Riyadh" selected>الرياض (GMT+3)</option>
                                        <option value="Asia/Aden">عدن (GMT+3)</option>
                                        <option value="Asia/Sanaa">صنعاء (GMT+3)</option>
                                    </select>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" checked>
                                    <label class="form-check-label" for="email_notifications">تفعيل إشعارات البريد الإلكتروني</label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary" disabled>
                                    <i class="fas fa-save me-1"></i> حفظ الإعدادات
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات الإشعارات -->
                <div class="tab-pane fade" id="notification-settings">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">إعدادات الإشعارات</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <p>إعدادات الإشعارات قيد التطوير.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات العمولات -->
                <div class="tab-pane fade" id="commission-settings">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">إعدادات العمولات</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <p>إعدادات العمولات قيد التطوير.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات المظهر -->
                <div class="tab-pane fade" id="appearance-settings">
                    <div class="card shadow">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">إعدادات المظهر</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <p>إعدادات المظهر قيد التطوير.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
