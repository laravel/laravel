@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">التقارير</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-chart-bar me-2"></i> التقارير</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i> مرحباً!</h5>
                <p>صفحة التقارير قيد التطوير. ستتوفر قريباً مجموعة متنوعة من التقارير الإحصائية والمالية.</p>
            </div>
            
            <!-- نماذج للتقارير المتوقعة -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">تقارير المبيعات</h5>
                        </div>
                        <div class="card-body text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                            <h4>تقرير المبيعات</h4>
                            <p class="text-muted">سيتوفر قريباً</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">تقارير العمولات</h5>
                        </div>
                        <div class="card-body text-center py-5">
                            <i class="fas fa-money-bill-wave fa-3x text-success mb-3"></i>
                            <h4>تقرير العمولات</h4>
                            <p class="text-muted">سيتوفر قريباً</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
