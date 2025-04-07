@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('agency.settings.index') }}">الإعدادات</a></li>
    <li class="breadcrumb-item active">معلومات النظام</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-info-circle me-2"></i> معلومات النظام</h2>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">إصدار النظام</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>إصدار النظام الحالي:</strong> {{ config('app_version.version') }}</p>
                    <p><strong>اسم الإصدار:</strong> {{ config('app_version.release_name') }}</p>
                    <p><strong>تاريخ الإصدار:</strong> {{ config('app_version.version_date') }}</p>
                </div>
                <div class="col-md-6">
                    @if(config('app_version.next_version'))
                        <div class="alert alert-info">
                            <p class="mb-0"><strong>الإصدار القادم:</strong> {{ config('app_version.next_version') }}</p>
                            <p class="mb-0 small">يتم حالياً تطوير هذا الإصدار مع ميزات جديدة.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">معلومات النظام التقنية</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>إصدار PHP:</strong> {{ phpversion() }}</p>
                    <p><strong>إصدار Laravel:</strong> {{ app()->version() }}</p>
                    <p><strong>نظام التشغيل:</strong> {{ php_uname('s') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>الوقت الحالي للخادم:</strong> {{ now() }}</p>
                    <p><strong>المنطقة الزمنية:</strong> {{ config('app.timezone') }}</p>
                    <p><strong>الذاكرة المستخدمة:</strong> {{ round(memory_get_usage() / 1048576, 2) }} ميجابايت</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
