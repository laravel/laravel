@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="row welcome-banner rounded-lg mb-5 shadow">
    <div class="col-md-8 offset-md-2 text-center py-5">
        <h1 class="mb-3">مرحباً بك في نظام وكالات السفر</h1>
        <p class="lead mb-4">منصة متكاملة لإدارة وكالات السفر والسبوكلاء والعملاء بطريقة سهلة وفعالة</p>
        @guest
        <div>
            <a href="{{ route('login') }}" class="btn btn-light btn-lg me-2 mb-2 mb-sm-0">
                <i class="fas fa-sign-in-alt me-1"></i> تسجيل الدخول
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg mb-2 mb-sm-0">
                <i class="fas fa-user-plus me-1"></i> التسجيل
            </a>
        </div>
        @else
        <div>
            @if(auth()->user()->isAgency())
                <a href="{{ route('agency.dashboard') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                </a>
            @elseif(auth()->user()->isSubagent())
                <a href="{{ route('subagent.dashboard') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                </a>
            @elseif(auth()->user()->isCustomer())
                <a href="{{ route('customer.dashboard') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                </a>
            @endif
        </div>
        @endguest
    </div>
</div>

<!-- Features Section -->
<div class="row mb-5">
    <div class="col-12 text-center mb-4">
        <h2>مميزات النظام</h2>
        <p class="lead text-muted">كل ما تحتاجه لإدارة أعمالك بكفاءة عالية</p>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-users feature-icon"></i>
                <h4>إدارة العملاء والسبوكلاء</h4>
                <p class="text-muted">إدارة فعالة لبيانات العملاء والسبوكلاء مع إمكانية تتبع النشاطات والطلبات.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-cogs feature-icon"></i>
                <h4>خدمات متنوعة</h4>
                <p class="text-muted">إدارة مجموعة واسعة من الخدمات مثل الموافقات الأمنية، النقل البري، الحج والعمرة وغيرها.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-file-alt feature-icon"></i>
                <h4>إدارة الطلبات</h4>
                <p class="text-muted">تقديم ومتابعة الطلبات وعروض الأسعار بطريقة سهلة ومنظمة.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-chart-bar feature-icon"></i>
                <h4>تقارير وإحصائيات</h4>
                <p class="text-muted">تقارير مفصلة وإحصائيات دقيقة لمساعدتك في اتخاذ القرارات الصحيحة.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-money-bill-wave feature-icon"></i>
                <h4>إدارة المعاملات المالية</h4>
                <p class="text-muted">متابعة العمولات والمدفوعات والمستحقات بين الوكالة والسبوكلاء والعملاء.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-globe feature-icon"></i>
                <h4>دعم تعدد العملات</h4>
                <p class="text-muted">دعم للعديد من العملات العالمية مثل الريال السعودي والدولار واليورو وغيرها للتعامل مع عملاء من مختلف الدول.</p>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="row">
    <div class="col-md-8 offset-md-2 text-center py-5">
        <h2 class="mb-3">ابدأ في استخدام النظام الآن</h2>
        <p class="lead mb-4">انضم إلى مئات الوكالات التي تستخدم نظامنا بنجاح لإدارة أعمالها</p>
        @guest
        <div>
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-2 mb-2 mb-sm-0">
                <i class="fas fa-user-plus me-1"></i> تسجيل حساب جديد
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg mb-2 mb-sm-0">
                <i class="fas fa-sign-in-alt me-1"></i> تسجيل الدخول
            </a>
        </div>
        @else
        <div>
            @if(auth()->user()->isAgency())
                <a href="{{ route('agency.dashboard') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> الذهاب إلى لوحة التحكم
                </a>
            @elseif(auth()->user()->isSubagent())
                <a href="{{ route('subagent.dashboard') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> الذهاب إلى لوحة التحكم
                </a>
            @elseif(auth()->user()->isCustomer())
                <a href="{{ route('customer.dashboard') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> الذهاب إلى لوحة التحكم
                </a>
            @endif
        </div>
        @endguest
    </div>
</div>
@endsection
