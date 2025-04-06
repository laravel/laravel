@extends('layouts.app')

@section('content')
<div class="px-4 py-5 my-5 text-center">
    <img class="d-block mx-auto mb-4" src="{{ asset('images/logo.png') }}" alt="الشعار" width="72" height="72">
    <h1 class="display-5 fw-bold text-body-emphasis">تطبيق وكالات السفر</h1>
    <div class="col-lg-6 mx-auto">
        <p class="lead mb-4">منصة إدارة متكاملة لوكالات السفر، تمكنك من إدارة الوكالة والسبوكلاء والعملاء بكفاءة عالية.</p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 gap-3">تسجيل الدخول</a>
            <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">إنشاء حساب</a>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center">
                    <i class="fa fa-building fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">إدارة الوكالة</h5>
                    <p class="card-text">إدارة متكاملة للوكالة والسبوكلاء والعملاء وجميع الخدمات في منصة واحدة.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center">
                    <i class="fa fa-user-tie fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">إدارة السبوكلاء</h5>
                    <p class="card-text">تمكين السبوكلاء من تقديم عروض أسعار بسهولة مع الحفاظ على سرية المعلومات.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body text-center">
                    <i class="fa fa-user fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">تجربة العملاء</h5>
                    <p class="card-text">واجهة بسيطة للعملاء لطلب الخدمات والاطلاع على العروض دون تعقيد.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
