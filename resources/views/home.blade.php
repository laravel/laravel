@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Welcome Message for Logged User -->
    @if(session('user'))
        <div class="alert alert-success">
            <i class="fa fa-user-circle"></i> 
            Selamat datang, <strong>{{ session('user')['name'] }}</strong>! 
            <small class="text-muted">({{ session('user')['role'] }})</small>
        </div>
    @endif
    
    <!-- Selected Date Range Display -->
    <div id="selected-dates" class="alert alert-info" style="display: none;">
        <h4><i class="fa fa-calendar-check-o"></i> Periode Slip Gaji:</h4>
        <p id="date-range-text" class="lead"></p>
        <a href="{{ url('/choosedate') }}" class="btn btn-sm btn-warning">
            <i class="fa fa-edit"></i> Ubah Periode
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron">
                <h1><i class="fa fa-money"></i> Sistem Slip Gaji</h1>
                <p class="lead">Kelola slip gaji karyawan dengan mudah dan efisien menggunakan teknologi modern.</p>
                <p>
                    <a class="btn btn-primary btn-lg" href="#" role="button">
                        <i class="fa fa-plus"></i> Buat Slip Gaji
                    </a>
                    <a class="btn btn-success btn-lg" href="{{ url('/choosedate') }}" role="button">
                        <i class="fa fa-calendar"></i> Pilih Periode
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for selected dates in localStorage
    const startDate = localStorage.getItem('startDate');
    const endDate = localStorage.getItem('endDate');
    
    if (startDate && endDate) {
        // Show selected dates alert
        const selectedDatesDiv = document.getElementById('selected-dates');
        const dateRangeText = document.getElementById('date-range-text');
        
        // Format dates (assuming they're in YYYY-MM-DD format)
        const formatDate = (dateStr) => {
            const date = new Date(dateStr);
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
        };
        
        dateRangeText.innerHTML = 
            '<strong>Dari:</strong> ' + formatDate(startDate) + 
            ' <strong>Sampai:</strong> ' + formatDate(endDate);
        
        selectedDatesDiv.style.display = 'block';
        
        // Store dates globally for Vue component
        window.selectedDateRange = {
            startDate: startDate,
            endDate: endDate
        };
    }
});
</script>
@endsection
