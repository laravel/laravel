@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-list"></i> Daftar Karyawan
                        <span class="badge pull-right" id="totalKaryawan">0</span>
                        <div class="pull-right" style="margin-right: 10px;">
                            <button onclick="showAddForm()" class="btn btn-xs btn-primary">
                                <i class="fa fa-plus"></i> Tambah Karyawan
                            </button>
                            <button onclick="exportData()" class="btn btn-xs btn-success">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </h3>
                </div>
                
                <!-- Search & Filter Panel -->
                <div class="panel-body" style="background-color: #f8f9fa; border-bottom: 1px solid #ddd;">
                    <form id="searchForm" class="form-inline">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="sr-only" for="searchName">Nama</label>
                                    <input type="text" 
                                           id="searchName" 
                                           class="form-control" 
                                           placeholder="Cari berdasarkan nama..."
                                           style="width: 100%;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="sr-only" for="filterDepartment">Departemen</label>
                                    <select id="filterDepartment" class="form-control" style="width: 100%;">
                                        <option value="">Semua Departemen</option>
                                        <option value="IT">IT</option>
                                        <option value="HR">HR</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="Operations">Operations</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="sr-only" for="filterPosition">Posisi</label>
                                    <input type="text" 
                                           id="filterPosition" 
                                           class="form-control" 
                                           placeholder="Filter posisi..."
                                           style="width: 100%;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="sr-only" for="sortBy">Urutkan</label>
                                    <select id="sortBy" class="form-control" style="width: 100%;">
                                        <option value="name">Nama A-Z</option>
                                        <option value="name_desc">Nama Z-A</option>
                                        <option value="salary">Gaji Terendah</option>
                                        <option value="salary_desc">Gaji Tertinggi</option>
                                        <option value="hire_date">Terlama Bekerja</option>
                                        <option value="hire_date_desc">Terbaru Bekerja</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-primary" 
                                            onclick="applyFilters()">
                                        <i class="fa fa-search"></i> Cari
                                    </button>
                                    <button type="button" 
                                            class="btn btn-default" 
                                            onclick="resetFilters()">
                                        <i class="fa fa-refresh"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Karyawan List Panel -->
                <div class="panel-body">
                    <div id="karyawanList">
                        <!-- Tabel akan ditampilkan di sini -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-hashtag"></i> No</th>
                                        <th><i class="fa fa-user"></i> Nama</th>
                                        <th><i class="fa fa-money"></i> Gaji Pokok</th>
                                        <th><i class="fa fa-briefcase"></i> Posisi</th>
                                        <th><i class="fa fa-clock-o"></i> Jam Kerja</th>
                                        <th><i class="fa fa-hourglass-half"></i> Overtime</th>
                                        <th><i class="fa fa-cutlery"></i> Uang Makan</th>
                                        <th><i class="fa fa-plus-circle"></i> Uang Penambah</th>
                                        <th><i class="fa fa-calculator"></i> Total Gaji</th>
                                        <th><i class="fa fa-cogs"></i> Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="karyawanTableBody">
                                    <!-- Data karyawan akan dimuat di sini -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="text-center" style="display: none;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p>Memuat data karyawan...</p>
                    </div>
                    
                    <!-- Empty State -->
                    <div id="emptyState" class="text-center" style="display: none;">
                        <i class="fa fa-info-circle fa-2x"></i>
                        <h4>Tidak Ada Karyawan</h4>
                        <p>Mulai dengan menambahkan karyawan pertama menggunakan form di atas.</p>
                    </div>
                </div>
            </div>
            
            <!-- Summary Statistics -->
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-2">
                    <div class="panel panel-info">
                        <div class="panel-body text-center">
                            <h4><i class="fa fa-users"></i> Total Karyawan</h4>
                            <h2 id="statTotalKaryawan" class="text-primary">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-success">
                        <div class="panel-body text-center">
                            <h4><i class="fa fa-money"></i> Rata-rata Gaji</h4>
                            <h2 id="statAvgSalary" class="text-success">Rp 0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-warning">
                        <div class="panel-body text-center">
                            <h4><i class="fa fa-calculator"></i> Total Pengeluaran</h4>
                            <h2 id="statTotalExpense" class="text-warning">Rp 0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-danger">
                        <div class="panel-body text-center">
                            <h4><i class="fa fa-building"></i> Departemen</h4>
                            <h2 id="statDepartments" class="text-danger">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-purple" style="border-color: #6f42c1;">
                        <div class="panel-body text-center">
                            <h4><i class="fa fa-clock-o"></i> Total Jam Kerja</h4>
                            <h2 id="statTotalHours" style="color: #6f42c1;">0 jam</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-dark" style="border-color: #343a40;">
                        <div class="panel-body text-center">
                            <h4><i class="fa fa-hourglass-half"></i> Total Overtime</h4>
                            <h2 id="statTotalOvertime" style="color: #343a40;">0 jam</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Karyawan -->
<div class="modal fade" id="karyawanDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-user"></i> Detail Karyawan
                </h4>
            </div>
            <div class="modal-body" id="karyawanDetailContent">
                <!-- Detail content akan dimuat di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="editKaryawan()">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Tambah/Edit Karyawan -->
<div class="modal fade" id="karyawanFormModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="formModalTitle">
                    <i class="fa fa-plus"></i> Tambah Karyawan Baru
                </h4>
            </div>
            <form id="karyawanForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama"><i class="fa fa-user"></i> Nama Lengkap *</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email"><i class="fa fa-envelope"></i> Email *</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="contoh@company.com" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="posisi"><i class="fa fa-briefcase"></i> Posisi *</label>
                                <input type="text" class="form-control" id="posisi" name="posisi" placeholder="Masukkan posisi/jabatan" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departemen"><i class="fa fa-building"></i> Departemen *</label>
                                <select class="form-control" id="departemen" name="departemen" required>
                                    <option value="">Pilih Departemen</option>
                                    <option value="IT">IT</option>
                                    <option value="HR">HR</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Operations">Operations</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone"><i class="fa fa-phone"></i> Nomor Telepon *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="081234567890" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hire_date"><i class="fa fa-calendar"></i> Tanggal Masuk *</label>
                                <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5><i class="fa fa-money"></i> Informasi Gaji</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gaji_pokok"><i class="fa fa-money"></i> Gaji Pokok *</label>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" placeholder="8000000" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="uang_makan"><i class="fa fa-cutlery"></i> Uang Makan</label>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="number" class="form-control" id="uang_makan" name="uang_makan" placeholder="300000" min="0" value="300000">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="uang_penambah"><i class="fa fa-plus-circle"></i> Uang Penambah</label>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="number" class="form-control" id="uang_penambah" name="uang_penambah" placeholder="500000" min="0" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="overtime_rate"><i class="fa fa-hourglass-half"></i> Rate Overtime per Jam</label>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="number" class="form-control" id="overtime_rate" name="overtime_rate" placeholder="25000" min="0" value="25000">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h5><i class="fa fa-clock-o"></i> Informasi Waktu Kerja</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jam_kerja"><i class="fa fa-clock-o"></i> Jam Kerja per Minggu</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="jam_kerja" name="jam_kerja" placeholder="40" min="0" max="60" value="40">
                                    <span class="input-group-addon">jam</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="overtime_hours"><i class="fa fa-hourglass-half"></i> Jam Overtime Bulan Ini</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="overtime_hours" name="overtime_hours" placeholder="0" min="0" max="100" value="0">
                                    <span class="input-group-addon">jam</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        <strong>Catatan:</strong> Jadwal kerja harian default adalah Senin-Jumat 09:00-17:00. 
                        Anda dapat mengubahnya setelah karyawan ditambahkan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Custom Styles untuk Employee List */
.panel {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.panel-heading {
    border-radius: 8px 8px 0 0;
}

.table > tbody > tr > td {
    vertical-align: middle;
}

.label {
    font-size: 11px;
}

.badge {
    background-color: #5bc0de;
}

.badge-info {
    background-color: #5bc0de;
}

.badge-success {
    background-color: #5cb85c;
}

.badge-warning {
    background-color: #f0ad4e;
}

.badge-danger {
    background-color: #d9534f;
}

.karyawan-avatar {
    background: linear-gradient(45deg, #3097d1, #2ab27b);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.nav-tabs {
    border-bottom: 1px solid #ddd;
}

.nav-tabs > li.active > a {
    color: #555;
    cursor: default;
    background-color: #fff;
    border: 1px solid #ddd;
    border-bottom-color: transparent;
}

.tab-content {
    background: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
}

.panel-purple {
    border-color: #6f42c1;
}

.panel-dark {
    border-color: #343a40;
}

/* Form Styling */
.form-group label {
    font-weight: bold;
    color: #555;
    margin-bottom: 5px;
}

.form-control {
    border-radius: 4px;
    border: 1px solid #ddd;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.form-control:focus {
    border-color: #66afe9;
    box-shadow: 0 0 8px rgba(102, 175, 233, 0.3);
}

.input-group-addon {
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    color: #555;
}

.modal-lg {
    width: 900px;
}

.modal-body {
    max-height: 500px;
    overflow-y: auto;
}

.alert-info {
    background-color: #d9edf7;
    border-color: #bce8f1;
    color: #31708f;
}

/* Button improvements */
.btn-xs {
    padding: 1px 5px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}

/* Required field indicator */
.form-group label:after {
    content: "";
}

.form-group label[for$="*"]:after,
.form-group label:contains("*"):after {
    content: " *";
    color: red;
}

/* Validation styles */
.form-control.error {
    border-color: #a94442;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 6px #ce8483;
}

.form-control.success {
    border-color: #3c763d;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 6px #67b168;
}

.alert {
    border-radius: 6px;
}

.btn-group-xs > .btn {
    padding: 1px 5px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}

.form-control {
    border-radius: 4px;
    border: 1px solid #ddd;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.form-control:focus {
    border-color: #66afe9;
    box-shadow: 0 0 8px rgba(102, 175, 233, 0.3);
}

.btn {
    border-radius: 4px;
    transition: all 0.3s;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Animation for table rows */
@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(20px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.table tbody tr {
    animation: fadeIn 0.5s ease-out;
}

/* Enhanced table interactions */
.table tbody tr:hover {
    background-color: #f5f5f5;
}

/* Statistics panels */
.panel-info .panel-body,
.panel-success .panel-body,
.panel-warning .panel-body,
.panel-danger .panel-body {
    padding: 15px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }
    
    .btn-group-xs > .btn {
        padding: 2px 4px;
        font-size: 10px;
    }
    
    .label {
        font-size: 10px;
    }
}
</style>

<script>
// Sample data karyawan
let karyawanData = [
    {
        id: 1,
        name: "Ahmad Susanto",
        position: "Software Developer",
        department: "IT",
        salary: 8500000,
        meal_allowance: 300000,
        additional_allowance: 500000,
        email: "ahmad.susanto@company.com",
        phone: "081234567890",
        hire_date: "2023-01-15",
        address: "Jl. Sudirman No. 123, Jakarta",
        working_hours: 40,
        overtime_hours: 8,
        overtime_rate: 25000,
        daily_schedule: {
            monday: "09:00-17:00",
            tuesday: "09:00-17:00",
            wednesday: "09:00-17:00",
            thursday: "09:00-17:00",
            friday: "09:00-17:00",
            saturday: "Off",
            sunday: "Off"
        }
    },
    {
        id: 2,
        name: "Sari Indah Lestari",
        position: "HR Manager",
        department: "HR",
        salary: 12000000,
        meal_allowance: 400000,
        additional_allowance: 800000,
        email: "sari.lestari@company.com",
        phone: "081234567891",
        hire_date: "2022-06-01",
        address: "Jl. Thamrin No. 456, Jakarta",
        working_hours: 40,
        overtime_hours: 5,
        overtime_rate: 35000,
        daily_schedule: {
            monday: "08:30-16:30",
            tuesday: "08:30-16:30",
            wednesday: "08:30-16:30",
            thursday: "08:30-16:30",
            friday: "08:30-16:30",
            saturday: "Off",
            sunday: "Off"
        }
    },
    {
        id: 3,
        name: "Budi Prasetyo",
        position: "Financial Analyst",
        department: "Finance",
        salary: 9500000,
        meal_allowance: 350000,
        additional_allowance: 600000,
        email: "budi.prasetyo@company.com",
        phone: "081234567892",
        hire_date: "2023-03-10",
        address: "Jl. Gatot Subroto No. 789, Jakarta",
        working_hours: 40,
        overtime_hours: 12,
        overtime_rate: 30000,
        daily_schedule: {
            monday: "09:00-17:00",
            tuesday: "09:00-17:00",
            wednesday: "09:00-17:00",
            thursday: "09:00-17:00",
            friday: "09:00-17:00",
            saturday: "Off",
            sunday: "Off"
        }
    },
    {
        id: 4,
        name: "Dewi Kartika",
        position: "Marketing Specialist",
        department: "Marketing",
        salary: 7500000,
        meal_allowance: 300000,
        additional_allowance: 450000,
        email: "dewi.kartika@company.com",
        phone: "081234567893",
        hire_date: "2023-07-20",
        address: "Jl. Kuningan No. 101, Jakarta",
        working_hours: 40,
        overtime_hours: 6,
        overtime_rate: 22000,
        daily_schedule: {
            monday: "09:00-17:00",
            tuesday: "09:00-17:00",
            wednesday: "09:00-17:00",
            thursday: "09:00-17:00",
            friday: "09:00-17:00",
            saturday: "Off",
            sunday: "Off"
        }
    },
    {
        id: 5,
        name: "Eko Wijaya",
        position: "Operations Manager",
        department: "Operations",
        salary: 11000000,
        meal_allowance: 400000,
        additional_allowance: 750000,
        email: "eko.wijaya@company.com",
        phone: "081234567894",
        hire_date: "2022-11-05",
        address: "Jl. Casablanca No. 202, Jakarta",
        working_hours: 45,
        overtime_hours: 10,
        overtime_rate: 32000,
        daily_schedule: {
            monday: "08:00-17:00",
            tuesday: "08:00-17:00",
            wednesday: "08:00-17:00",
            thursday: "08:00-17:00",
            friday: "08:00-17:00",
            saturday: "09:00-12:00",
            sunday: "Off"
        }
    },
    {
        id: 6,
        name: "Fitri Handayani",
        position: "System Administrator",
        department: "IT",
        salary: 8000000,
        meal_allowance: 300000,
        additional_allowance: 500000,
        email: "fitri.handayani@company.com",
        phone: "081234567895",
        hire_date: "2023-02-28",
        address: "Jl. Menteng No. 303, Jakarta",
        working_hours: 40,
        overtime_hours: 15,
        overtime_rate: 25000,
        daily_schedule: {
            monday: "09:00-17:00",
            tuesday: "09:00-17:00",
            wednesday: "09:00-17:00",
            thursday: "09:00-17:00",
            friday: "09:00-17:00",
            saturday: "Off",
            sunday: "Off"
        }
    },
    {
        id: 7,
        name: "Gunawan Setiawan",
        position: "Accountant",
        department: "Finance",
        salary: 7000000,
        meal_allowance: 300000,
        additional_allowance: 400000,
        email: "gunawan.setiawan@company.com",
        phone: "081234567896",
        hire_date: "2023-05-15",
        address: "Jl. Kemang No. 404, Jakarta",
        working_hours: 40,
        overtime_hours: 7,
        overtime_rate: 20000,
        daily_schedule: {
            monday: "08:30-16:30",
            tuesday: "08:30-16:30",
            wednesday: "08:30-16:30",
            thursday: "08:30-16:30",
            friday: "08:30-16:30",
            saturday: "Off",
            sunday: "Off"
        }
    },
    {
        id: 8,
        name: "Hesti Ramadhani",
        position: "Digital Marketing",
        department: "Marketing",
        salary: 7800000,
        meal_allowance: 300000,
        additional_allowance: 450000,
        email: "hesti.ramadhani@company.com",
        phone: "081234567897",
        hire_date: "2023-04-12",
        address: "Jl. Pondok Indah No. 505, Jakarta",
        working_hours: 40,
        overtime_hours: 4,
        overtime_rate: 23000,
        daily_schedule: {
            monday: "10:00-18:00",
            tuesday: "10:00-18:00",
            wednesday: "10:00-18:00",
            thursday: "10:00-18:00",
            friday: "10:00-18:00",
            saturday: "Off",
            sunday: "Off"
        }
    },
    {
        id: 9,
        name: "Indra Kurniawan",
        position: "Quality Control",
        department: "Operations",
        salary: 6500000,
        meal_allowance: 300000,
        additional_allowance: 350000,
        email: "indra.kurniawan@company.com",
        phone: "081234567898",
        hire_date: "2023-08-01",
        address: "Jl. Kelapa Gading No. 606, Jakarta",
        working_hours: 40,
        overtime_hours: 9,
        overtime_rate: 20000,
        daily_schedule: {
            monday: "07:00-15:00",
            tuesday: "07:00-15:00",
            wednesday: "07:00-15:00",
            thursday: "07:00-15:00",
            friday: "07:00-15:00",
            saturday: "Off",
            sunday: "Off"
        }
    },
    {
        id: 10,
        name: "Julia Sari",
        position: "HR Specialist",
        department: "HR",
        salary: 6800000,
        meal_allowance: 300000,
        additional_allowance: 400000,
        email: "julia.sari@company.com",
        phone: "081234567899",
        hire_date: "2023-06-18",
        address: "Jl. Bintaro No. 707, Jakarta",
        working_hours: 40,
        overtime_hours: 3,
        overtime_rate: 20000,
        daily_schedule: {
            monday: "08:30-16:30",
            tuesday: "08:30-16:30",
            wednesday: "08:30-16:30",
            thursday: "08:30-16:30",
            friday: "08:30-16:30",
            saturday: "Off",
            sunday: "Off"
        }
    }
];

let filteredData = [...karyawanData];
let currentDetailId = null;
let editingId = null;

// Initialize page
$(document).ready(function() {
    // Pastikan DOM sudah siap sepenuhnya
    setTimeout(function() {
        displayKaryawan(karyawanData);
        updateStatistics(karyawanData);
    }, 100);
    
    // Real-time search
    $('#searchName').on('input', debounce(applyFilters, 300));
    $('#filterPosition').on('input', debounce(applyFilters, 300));
    $('#filterDepartment, #sortBy').on('change', applyFilters);
    
    // Form submission
    $('#karyawanForm').on('submit', handleFormSubmit);
    
    // Set default hire date to today
    $('#hire_date').val(new Date().toISOString().split('T')[0]);
});

// Display karyawan in table format
function displayKaryawan(data) {
    console.log('displayKaryawan called with', data.length, 'employees');
    const tableBody = $('#karyawanTableBody');
    const container = $('#karyawanList');
    const emptyState = $('#emptyState');
    
    // Pastikan semua element ada
    if (tableBody.length === 0 || container.length === 0 || emptyState.length === 0) {
        console.error('Required elements not found in DOM');
        return;
    }
    
    if (data.length === 0) {
        console.log('No data found, showing empty state');
        emptyState.show();
        container.hide();
        return;
    }
    
    console.log('Data found, hiding empty state and showing container');
    emptyState.hide();
    container.show();
    
    let html = '';
    data.forEach((karyawan, index) => {
        const overtimePay = karyawan.overtime_hours * karyawan.overtime_rate;
        const totalSalary = karyawan.salary + karyawan.meal_allowance + karyawan.additional_allowance + overtimePay;
        
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <strong>${karyawan.name}</strong>
                </td>
                <td>
                    <strong>Rp ${formatNumber(karyawan.salary)}</strong>
                </td>
                <td>
                    <span class="label label-primary">${karyawan.position}</span>
                </td>
                <td>
                    <div class="text-center">
                        <span class="badge badge-info">${karyawan.working_hours} jam/minggu</span>
                    </div>
                </td>
                <td>
                    <div class="text-center">
                        <span class="badge ${karyawan.overtime_hours > 10 ? 'badge-danger' : karyawan.overtime_hours > 5 ? 'badge-warning' : 'badge-success'}">${karyawan.overtime_hours} jam</span>
                        <br><small class="text-muted">Rp ${formatNumber(overtimePay)}</small>
                    </div>
                </td>
                <td>
                    <span class="text-success">Rp ${formatNumber(karyawan.meal_allowance)}</span>
                </td>
                <td>
                    <span class="text-info">Rp ${formatNumber(karyawan.additional_allowance)}</span>
                </td>
                <td>
                    <strong class="text-primary">Rp ${formatNumber(totalSalary)}</strong>
                </td>
                <td>
                    <div class="btn-group btn-group-xs">
                        <button type="button" 
                                class="btn btn-info" 
                                onclick="showDetail(${karyawan.id})"
                                title="Lihat Detail">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" 
                                class="btn btn-warning" 
                                onclick="editKaryawan(${karyawan.id})"
                                title="Edit Karyawan">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" 
                                class="btn btn-success" 
                                onclick="manageSchedule(${karyawan.id})"
                                title="Kelola Jadwal">
                            <i class="fa fa-calendar"></i>
                        </button>
                        <button type="button" 
                                class="btn btn-danger" 
                                onclick="deleteKaryawan(${karyawan.id})"
                                title="Hapus Karyawan">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    console.log('Inserting HTML into table body, length:', html.length);
    tableBody.html(html);
    $('#totalKaryawan').text(data.length);
    console.log('displayKaryawan completed successfully');
}

// Show add form
function showAddForm() {
    editingId = null;
    $('#formModalTitle').html('<i class="fa fa-plus"></i> Tambah Karyawan Baru');
    $('#karyawanForm')[0].reset();
    $('#hire_date').val(new Date().toISOString().split('T')[0]);
    $('#uang_makan').val(300000);
    $('#overtime_rate').val(25000);
    $('#jam_kerja').val(40);
    $('#overtime_hours').val(0);
    $('#karyawanFormModal').modal('show');
}

// Handle form submission
function handleFormSubmit(e) {
    e.preventDefault();
    
    const formData = {
        nama: $('#nama').val().trim(),
        email: $('#email').val().trim(),
        posisi: $('#posisi').val().trim(),
        departemen: $('#departemen').val(),
        phone: $('#phone').val().trim(),
        hire_date: $('#hire_date').val(),
        alamat: $('#alamat').val().trim(),
        gaji_pokok: parseInt($('#gaji_pokok').val()) || 0,
        uang_makan: parseInt($('#uang_makan').val()) || 0,
        uang_penambah: parseInt($('#uang_penambah').val()) || 0,
        overtime_rate: parseInt($('#overtime_rate').val()) || 0,
        jam_kerja: parseInt($('#jam_kerja').val()) || 40,
        overtime_hours: parseInt($('#overtime_hours').val()) || 0
    };
    
    // Validation
    if (!formData.nama || !formData.email || !formData.posisi || !formData.departemen || !formData.phone || !formData.hire_date) {
        alert('Mohon lengkapi semua field yang wajib diisi (*)');
        return;
    }
    
    if (formData.gaji_pokok <= 0) {
        alert('Gaji pokok harus lebih dari 0');
        return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        alert('Format email tidak valid');
        return;
    }
    
    // Phone validation
    const phoneRegex = /^[0-9+\-\s()]+$/;
    if (!phoneRegex.test(formData.phone)) {
        alert('Format nomor telepon tidak valid');
        return;
    }
    
    if (editingId) {
        // Update existing employee
        updateEmployee(editingId, formData);
    } else {
        // Add new employee
        addNewEmployee(formData);
    }
}

// Add new employee
function addNewEmployee(formData) {
    const newId = Math.max(...karyawanData.map(k => k.id)) + 1;
    
    const newKaryawan = {
        id: newId,
        name: formData.nama,
        position: formData.posisi,
        department: formData.departemen,
        salary: formData.gaji_pokok,
        meal_allowance: formData.uang_makan,
        additional_allowance: formData.uang_penambah,
        email: formData.email,
        phone: formData.phone,
        hire_date: formData.hire_date,
        address: formData.alamat || 'Alamat belum diisi',
        working_hours: formData.jam_kerja,
        overtime_hours: formData.overtime_hours,
        overtime_rate: formData.overtime_rate,
        daily_schedule: {
            monday: "09:00-17:00",
            tuesday: "09:00-17:00",
            wednesday: "09:00-17:00",
            thursday: "09:00-17:00",
            friday: "09:00-17:00",
            saturday: "Off",
            sunday: "Off"
        }
    };
    
    karyawanData.push(newKaryawan);
    applyFilters();
    $('#karyawanFormModal').modal('hide');
    
    alert(`Karyawan ${formData.nama} berhasil ditambahkan!`);
}

// Update existing employee
function updateEmployee(id, formData) {
    const index = karyawanData.findIndex(k => k.id === id);
    if (index === -1) return;
    
    karyawanData[index] = {
        ...karyawanData[index],
        name: formData.nama,
        position: formData.posisi,
        department: formData.departemen,
        salary: formData.gaji_pokok,
        meal_allowance: formData.uang_makan,
        additional_allowance: formData.uang_penambah,
        email: formData.email,
        phone: formData.phone,
        hire_date: formData.hire_date,
        address: formData.alamat || karyawanData[index].address,
        working_hours: formData.jam_kerja,
        overtime_hours: formData.overtime_hours,
        overtime_rate: formData.overtime_rate
    };
    
    applyFilters();
    $('#karyawanFormModal').modal('hide');
    
    alert(`Data karyawan ${formData.nama} berhasil diperbarui!`);
}

// Apply filters and search
function applyFilters() {
    const searchName = $('#searchName').val().toLowerCase();
    const filterDepartment = $('#filterDepartment').val();
    const filterPosition = $('#filterPosition').val().toLowerCase();
    const sortBy = $('#sortBy').val();
    
    // Filter data
    filteredData = karyawanData.filter(karyawan => {
        const matchName = karyawan.name.toLowerCase().includes(searchName);
        const matchDepartment = !filterDepartment || karyawan.department === filterDepartment;
        const matchPosition = karyawan.position.toLowerCase().includes(filterPosition);
        
        return matchName && matchDepartment && matchPosition;
    });
    
    // Sort data
    filteredData.sort((a, b) => {
        switch(sortBy) {
            case 'name':
                return a.name.localeCompare(b.name);
            case 'name_desc':
                return b.name.localeCompare(a.name);
            case 'salary':
                return a.salary - b.salary;
            case 'salary_desc':
                return b.salary - a.salary;
            case 'hire_date':
                return new Date(a.hire_date) - new Date(b.hire_date);
            case 'hire_date_desc':
                return new Date(b.hire_date) - new Date(a.hire_date);
            default:
                return 0;
        }
    });
    
    displayKaryawan(filteredData);
    updateStatistics(filteredData);
}

// Reset filters
function resetFilters() {
    $('#searchName').val('');
    $('#filterDepartment').val('');
    $('#filterPosition').val('');
    $('#sortBy').val('name');
    
    filteredData = [...karyawanData];
    displayKaryawan(filteredData);
    updateStatistics(filteredData);
}

// Show karyawan detail
function showDetail(id) {
    const karyawan = karyawanData.find(k => k.id === id);
    if (!karyawan) return;
    
    currentDetailId = id;
    const overtimePay = karyawan.overtime_hours * karyawan.overtime_rate;
    const totalSalary = karyawan.salary + karyawan.meal_allowance + karyawan.additional_allowance + overtimePay;
    
    const html = `
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="karyawan-avatar" style="width: 120px; height: 120px; font-size: 48px; margin: 0 auto 20px;">
                    ${karyawan.name.split(' ').map(n => n[0]).join('').substring(0, 2)}
                </div>
                <h4>${karyawan.name}</h4>
                <p class="text-muted">${karyawan.position}</p>
            </div>
            <div class="col-md-8">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#personal-info" role="tab" data-toggle="tab">
                            <i class="fa fa-user"></i> Info Personal
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#salary-info" role="tab" data-toggle="tab">
                            <i class="fa fa-money"></i> Info Gaji
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#schedule-info" role="tab" data-toggle="tab">
                            <i class="fa fa-calendar"></i> Jadwal Kerja
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content" style="margin-top: 15px;">
                    <div role="tabpanel" class="tab-pane active" id="personal-info">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Departemen:</strong></td>
                                <td><span class="label label-primary">${karyawan.department}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>${karyawan.email}</td>
                            </tr>
                            <tr>
                                <td><strong>Telepon:</strong></td>
                                <td>${karyawan.phone}</td>
                            </tr>
                            <tr>
                                <td><strong>Alamat:</strong></td>
                                <td>${karyawan.address}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Masuk:</strong></td>
                                <td>${formatDate(karyawan.hire_date)}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="salary-info">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Gaji Pokok:</strong></td>
                                <td class="text-success">Rp ${formatNumber(karyawan.salary)}</td>
                            </tr>
                            <tr>
                                <td><strong>Uang Makan:</strong></td>
                                <td class="text-info">Rp ${formatNumber(karyawan.meal_allowance)}</td>
                            </tr>
                            <tr>
                                <td><strong>Uang Penambah:</strong></td>
                                <td class="text-warning">Rp ${formatNumber(karyawan.additional_allowance)}</td>
                            </tr>
                            <tr style="border-top: 2px solid #ddd;">
                                <td><strong>Jam Kerja Normal:</strong></td>
                                <td><span class="badge badge-info">${karyawan.working_hours} jam/minggu</span></td>
                            </tr>
                            <tr>
                                <td><strong>Jam Overtime:</strong></td>
                                <td><span class="badge ${karyawan.overtime_hours > 10 ? 'badge-danger' : karyawan.overtime_hours > 5 ? 'badge-warning' : 'badge-success'}">${karyawan.overtime_hours} jam</span></td>
                            </tr>
                            <tr>
                                <td><strong>Rate Overtime:</strong></td>
                                <td>Rp ${formatNumber(karyawan.overtime_rate)}/jam</td>
                            </tr>
                            <tr>
                                <td><strong>Bayaran Overtime:</strong></td>
                                <td class="text-danger">Rp ${formatNumber(overtimePay)}</td>
                            </tr>
                            <tr style="border-top: 2px solid #ddd;">
                                <td><strong>Total Gaji:</strong></td>
                                <td class="text-primary"><h4>Rp ${formatNumber(totalSalary)}</h4></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="schedule-info">
                        <h5><i class="fa fa-calendar"></i> Jadwal Harian</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Jam Kerja</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Senin</strong></td>
                                    <td>${karyawan.daily_schedule.monday}</td>
                                    <td>${karyawan.daily_schedule.monday === 'Off' ? '<span class="label label-danger">Libur</span>' : '<span class="label label-success">Kerja</span>'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Selasa</strong></td>
                                    <td>${karyawan.daily_schedule.tuesday}</td>
                                    <td>${karyawan.daily_schedule.tuesday === 'Off' ? '<span class="label label-danger">Libur</span>' : '<span class="label label-success">Kerja</span>'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Rabu</strong></td>
                                    <td>${karyawan.daily_schedule.wednesday}</td>
                                    <td>${karyawan.daily_schedule.wednesday === 'Off' ? '<span class="label label-danger">Libur</span>' : '<span class="label label-success">Kerja</span>'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kamis</strong></td>
                                    <td>${karyawan.daily_schedule.thursday}</td>
                                    <td>${karyawan.daily_schedule.thursday === 'Off' ? '<span class="label label-danger">Libur</span>' : '<span class="label label-success">Kerja</span>'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jumat</strong></td>
                                    <td>${karyawan.daily_schedule.friday}</td>
                                    <td>${karyawan.daily_schedule.friday === 'Off' ? '<span class="label label-danger">Libur</span>' : '<span class="label label-success">Kerja</span>'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sabtu</strong></td>
                                    <td>${karyawan.daily_schedule.saturday}</td>
                                    <td>${karyawan.daily_schedule.saturday === 'Off' ? '<span class="label label-danger">Libur</span>' : '<span class="label label-warning">Kerja Paruh Waktu</span>'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Minggu</strong></td>
                                    <td>${karyawan.daily_schedule.sunday}</td>
                                    <td>${karyawan.daily_schedule.sunday === 'Off' ? '<span class="label label-danger">Libur</span>' : '<span class="label label-success">Kerja</span>'}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#karyawanDetailContent').html(html);
    $('#karyawanDetailModal').modal('show');
}

// Manage karyawan schedule
function manageSchedule(id) {
    const karyawan = karyawanData.find(k => k.id === id);
    if (!karyawan) return;
    
    alert(`Fitur kelola jadwal untuk ${karyawan.name} akan segera tersedia!\n\nInformasi saat ini:\n- Jam kerja normal: ${karyawan.working_hours} jam/minggu\n- Jam overtime: ${karyawan.overtime_hours} jam\n- Rate overtime: Rp ${formatNumber(karyawan.overtime_rate)}/jam`);
}

// Edit karyawan
function editKaryawan(id = null) {
    const targetId = id || currentDetailId;
    const karyawan = karyawanData.find(k => k.id === targetId);
    
    if (!karyawan) {
        alert('Karyawan tidak ditemukan');
        return;
    }
    
    editingId = targetId;
    $('#formModalTitle').html('<i class="fa fa-edit"></i> Edit Karyawan: ' + karyawan.name);
    
    // Fill form with existing data
    $('#nama').val(karyawan.name);
    $('#email').val(karyawan.email);
    $('#posisi').val(karyawan.position);
    $('#departemen').val(karyawan.department);
    $('#phone').val(karyawan.phone);
    $('#hire_date').val(karyawan.hire_date);
    $('#alamat').val(karyawan.address);
    $('#gaji_pokok').val(karyawan.salary);
    $('#uang_makan').val(karyawan.meal_allowance);
    $('#uang_penambah').val(karyawan.additional_allowance);
    $('#overtime_rate').val(karyawan.overtime_rate);
    $('#jam_kerja').val(karyawan.working_hours);
    $('#overtime_hours').val(karyawan.overtime_hours);
    
    $('#karyawanDetailModal').modal('hide');
    $('#karyawanFormModal').modal('show');
}

// Delete karyawan
function deleteKaryawan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus karyawan ini?')) {
        // Remove from data array
        const index = karyawanData.findIndex(k => k.id === id);
        if (index > -1) {
            karyawanData.splice(index, 1);
            applyFilters();
            alert('Karyawan berhasil dihapus!');
        }
    }
}

// Export data
function exportData() {
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Nama,Posisi,Departemen,Gaji Pokok,Uang Makan,Uang Penambah,Jam Kerja,Jam Overtime,Rate Overtime,Bayaran Overtime,Total Gaji,Email,Telepon,Tanggal Masuk\n";
    
    filteredData.forEach(karyawan => {
        const overtimePay = karyawan.overtime_hours * karyawan.overtime_rate;
        const totalSalary = karyawan.salary + karyawan.meal_allowance + karyawan.additional_allowance + overtimePay;
        const row = [
            karyawan.name,
            karyawan.position,
            karyawan.department,
            karyawan.salary,
            karyawan.meal_allowance,
            karyawan.additional_allowance,
            karyawan.working_hours,
            karyawan.overtime_hours,
            karyawan.overtime_rate,
            overtimePay,
            totalSalary,
            karyawan.email,
            karyawan.phone,
            karyawan.hire_date
        ].join(",");
        csvContent += row + "\n";
    });
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "daftar_karyawan_" + new Date().toISOString().split('T')[0] + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Update statistics
function updateStatistics(data) {
    const totalKaryawan = data.length;
    const totalWorkingHours = data.reduce((sum, k) => sum + k.working_hours, 0);
    const totalOvertimeHours = data.reduce((sum, k) => sum + k.overtime_hours, 0);
    const avgSalary = data.length > 0 ? data.reduce((sum, k) => sum + k.salary, 0) / data.length : 0;
    const totalExpense = data.reduce((sum, k) => {
        const overtimePay = k.overtime_hours * k.overtime_rate;
        return sum + k.salary + k.meal_allowance + k.additional_allowance + overtimePay;
    }, 0);
    const departments = [...new Set(data.map(k => k.department))].length;
    
    $('#statTotalKaryawan').text(totalKaryawan);
    $('#statAvgSalary').text('Rp ' + formatNumber(Math.round(avgSalary)));
    $('#statTotalExpense').text('Rp ' + formatNumber(totalExpense));
    $('#statDepartments').text(departments);
    $('#statTotalHours').text(totalWorkingHours + ' jam');
    $('#statTotalOvertime').text(totalOvertimeHours + ' jam');
}

// Utility functions
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endsection
