@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron">
                <h1><i class="fa fa-money"></i> Sistem Slip Gaji</h1>
                <p class="lead">Kelola slip gaji karyawan dengan mudah dan efisien menggunakan teknologi modern.</p>
                <p>
                    <a class="btn btn-primary btn-lg" href="#" role="button">
                        <i class="fa fa-plus"></i> Buat Slip Gaji
                    </a>
                    <a class="btn btn-success btn-lg" href="#" role="button">
                        <i class="fa fa-users"></i> Kelola Karyawan
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Vue.js Component -->
    <slip-gaji></slip-gaji>
    
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-users"></i> Manajemen Karyawan</h3>
                </div>
                <div class="panel-body">
                    <p>Kelola data karyawan, jabatan, dan informasi personal dengan sistem yang terintegrasi.</p>
                    <a href="#" class="btn btn-primary">Kelola <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-calculator"></i> Perhitungan Gaji</h3>
                </div>
                <div class="panel-body">
                    <p>Sistem perhitungan otomatis dengan berbagai komponen gaji, tunjangan, dan potongan.</p>
                    <a href="#" class="btn btn-success">Hitung <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-file-pdf-o"></i> Export & Laporan</h3>
                </div>
                <div class="panel-body">
                    <p>Export slip gaji dalam format PDF dan buat laporan payroll yang komprehensif.</p>
                    <a href="#" class="btn btn-info">Export <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
