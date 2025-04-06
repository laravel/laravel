@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item active">لوحة تحكم العميل</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">لوحة تحكم العميل</h1>
            
            <div class="row">
                <!-- إحصائيات سريعة -->
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-4">
                        <div class="card-body">
                            <h5 class="card-title">إجمالي الطلبات</h5>
                            <p class="card-text display-4">{{ auth()->user()->customerRequests->count() ?? 0 }}</p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="#">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i> مرحباً بك في لوحة تحكم العميل!</h5>
                <p>من هنا يمكنك متابعة طلباتك وعروض الأسعار المقدمة لك.</p>
            </div>
        </div>
    </div>
</div>
@endsection
