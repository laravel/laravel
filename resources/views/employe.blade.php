@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-users"></i> Manajemen Karyawan
                    </h3>
                </div>
                <div class="panel-body">
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif
                    
                    <!-- Employee Form -->
                    <form id="employeeForm" method="POST" action="{{ route('employee.store') }}" class="form-horizontal">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name" class="col-md-3 control-label">
                                <i class="fa fa-user"></i> Nama:
                            </label>
                            <div class="col-md-9">
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       class="form-control" 
                                       placeholder="Masukkan nama karyawan" 
                                       value="{{ old('name') }}" 
                                       required>
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="salary" class="col-md-3 control-label">
                                <i class="fa fa-money"></i> Gaji:
                            </label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="number" 
                                           id="salary" 
                                           name="salary" 
                                           class="form-control" 
                                           placeholder="Masukkan gaji pokok" 
                                           value="{{ old('salary') }}" 
                                           min="0" 
                                           required>
                                </div>
                                @if($errors->has('salary'))
                                    <span class="text-danger">{{ $errors->first('salary') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="position" class="col-md-3 control-label">
                                <i class="fa fa-briefcase"></i> Posisi:
                            </label>
                            <div class="col-md-9">
                                <input type="text" 
                                       id="position" 
                                       name="position" 
                                       class="form-control" 
                                       placeholder="Masukkan posisi/jabatan" 
                                       value="{{ old('position') }}" 
                                       required>
                                @if($errors->has('position'))
                                    <span class="text-danger">{{ $errors->first('position') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="meal_allowance" class="col-md-3 control-label">
                                <i class="fa fa-cutlery"></i> Uang Makan:
                            </label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="number" 
                                           id="meal_allowance" 
                                           name="meal_allowance" 
                                           class="form-control" 
                                           placeholder="Masukkan uang makan per hari" 
                                           value="{{ old('meal_allowance') }}" 
                                           min="0" 
                                           required>
                                </div>
                                @if($errors->has('meal_allowance'))
                                    <span class="text-danger">{{ $errors->first('meal_allowance') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="additional_allowance" class="col-md-3 control-label">
                                <i class="fa fa-plus-circle"></i> Uang Penambah:
                            </label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="number" 
                                           id="additional_allowance" 
                                           name="additional_allowance" 
                                           class="form-control" 
                                           placeholder="Masukkan tunjangan tambahan" 
                                           value="{{ old('additional_allowance') }}" 
                                           min="0" 
                                           required>
                                </div>
                                @if($errors->has('additional_allowance'))
                                    <span class="text-danger">{{ $errors->first('additional_allowance') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-plus"></i> Tambah Karyawan
                                </button>
                                <button type="reset" class="btn btn-default btn-lg">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Employee List Panel -->
            <div class="panel panel-info" style="margin-top: 20px;">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-list"></i> Daftar Karyawan
                        @if(isset($employees))
                            <span class="badge pull-right">{{ count($employees) }}</span>
                        @endif
                        <div class="pull-right" style="margin-right: 10px;">
                            <a href="{{ route('employee.export') }}" class="btn btn-xs btn-success">
                                <i class="fa fa-download"></i> Export CSV
                            </a>
                        </div>
                    </h3>
                </div>
                
                <!-- Search Panel -->
                <div class="panel-body" style="background-color: #f9f9f9; border-bottom: 1px solid #ddd;">
                    <form id="searchForm" method="GET" action="{{ route('employee.search') }}" class="form-inline">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="name" 
                                       class="form-control" 
                                       placeholder="Cari berdasarkan nama..." 
                                       value="{{ request('name') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" 
                                       name="position" 
                                       class="form-control" 
                                       placeholder="Cari berdasarkan posisi..." 
                                       value="{{ request('position') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="number" 
                                       name="min_salary" 
                                       class="form-control" 
                                       placeholder="Gaji Min" 
                                       value="{{ request('min_salary') }}">
                            </div>
                            <div class="col-md-3">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i> Cari
                                    </button>
                                    <a href="{{ route('employee.index') }}" class="btn btn-default">
                                        <i class="fa fa-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="panel-body">
                    @if(isset($employees) && count($employees) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-hashtag"></i> No</th>
                                        <th><i class="fa fa-user"></i> Nama</th>
                                        <th><i class="fa fa-money"></i> Gaji Pokok</th>
                                        <th><i class="fa fa-briefcase"></i> Posisi</th>
                                        <th><i class="fa fa-cutlery"></i> Uang Makan</th>
                                        <th><i class="fa fa-plus-circle"></i> Uang Penambah</th>
                                        <th><i class="fa fa-calculator"></i> Total Gaji</th>
                                        <th><i class="fa fa-cogs"></i> Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $index => $employee)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $employee->name }}</strong>
                                        </td>
                                        <td>
                                            <strong>Rp {{ number_format($employee->salary, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <span class="label label-primary">{{ $employee->position }}</span>
                                        </td>
                                        <td>
                                            <span class="text-success">Rp {{ number_format($employee->meal_allowance ?? 0, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-info">Rp {{ number_format($employee->additional_allowance ?? 0, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-primary">
                                                Rp {{ number_format(($employee->salary + ($employee->meal_allowance ?? 0) + ($employee->additional_allowance ?? 0)), 0, ',', '.') }}
                                            </strong>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-xs">
                                                <button type="button" 
                                                        class="btn btn-warning" 
                                                        onclick="editEmployee({{ $employee->id }})"
                                                        title="Edit Karyawan">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-danger" 
                                                        onclick="deleteEmployee({{ $employee->id }})"
                                                        title="Hapus Karyawan">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fa fa-info-circle fa-2x"></i>
                            <h4>Tidak Ada Karyawan</h4>
                            <p>Mulai dengan menambahkan karyawan pertama menggunakan form di atas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Employee Page -->
<style>
    .panel {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .panel-heading {
        border-radius: 8px 8px 0 0;
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
    
    .table > tbody > tr > td {
        vertical-align: middle;
    }
    
    .label {
        font-size: 11px;
    }
    
    .badge {
        background-color: #5bc0de;
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
    
    .control-label {
        font-weight: 600;
        color: #555;
    }
    
    .input-group-addon {
        background-color: #f5f5f5;
        border-color: #ddd;
    }
    
    /* Animation for new rows */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .table tbody tr {
        animation: fadeIn 0.5s ease-out;
    }
</style>

<script>
    function editEmployee(id) {
        // For now, redirect to edit page
        // You can replace this with a modal later
        if (confirm('Redirect to edit page for employee ID: ' + id + '?')) {
            window.location.href = '/employee/' + id + '/edit';
        }
    }
    
    function deleteEmployee(id) {
        if (confirm('Apakah Anda yakin ingin menghapus karyawan ini?')) {
            // Create a form to submit DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/employee/' + id;
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Add method override for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Form validation and enhancement
    $(document).ready(function() {
        // Form validation
        $('#employeeForm').on('submit', function(e) {
            const name = $('#name').val().trim();
            const salary = $('#salary').val();
            const position = $('#position').val().trim();
            const mealAllowance = $('#meal_allowance').val();
            const additionalAllowance = $('#additional_allowance').val();
            
            // Basic validation
            if (!name || !salary || !position || !mealAllowance || !additionalAllowance) {
                e.preventDefault();
                alert('Harap isi semua field yang diperlukan.');
                return false;
            }
            
            // Salary validation
            if (parseFloat(salary) <= 0) {
                e.preventDefault();
                alert('Harap masukkan jumlah gaji yang valid.');
                $('#salary').focus();
                return false;
            }
            
            // Meal allowance validation
            if (parseFloat(mealAllowance) < 0) {
                e.preventDefault();
                alert('Uang makan tidak boleh negatif.');
                $('#meal_allowance').focus();
                return false;
            }
            
            // Additional allowance validation
            if (parseFloat(additionalAllowance) < 0) {
                e.preventDefault();
                alert('Uang penambah tidak boleh negatif.');
                $('#additional_allowance').focus();
                return false;
            }
            
            // Name validation (only letters and spaces)
            const nameRegex = /^[a-zA-Z\s]+$/;
            if (!nameRegex.test(name)) {
                e.preventDefault();
                alert('Nama hanya boleh berisi huruf dan spasi.');
                $('#name').focus();
                return false;
            }
            
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Menambahkan...');
        });
        
        // Format salary input with thousands separator
        $('#salary, #meal_allowance, #additional_allowance').on('input', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            if (value) {
                // Store original value for form submission
                $(this).data('original-value', value);
            }
        });
        
        // Auto-capitalize name
        $('#name').on('input', function() {
            let value = $(this).val();
            value = value.replace(/\b\w/g, function(match) {
                return match.toUpperCase();
            });
            $(this).val(value);
        });
        
        // Calculate total salary dynamically
        function calculateTotalSalary() {
            const salary = parseFloat($('#salary').val()) || 0;
            const mealAllowance = parseFloat($('#meal_allowance').val()) || 0;
            const additionalAllowance = parseFloat($('#additional_allowance').val()) || 0;
            const total = salary + mealAllowance + additionalAllowance;
            
            // You can display this total somewhere if needed
            console.log('Total Gaji:', 'Rp ' + total.toLocaleString('id-ID'));
        }
        
        $('#salary, #meal_allowance, #additional_allowance').on('input', calculateTotalSalary);
        
        // Enhanced table interactions
        $('.table tbody tr').hover(
            function() {
                $(this).addClass('warning');
            },
            function() {
                $(this).removeClass('warning');
            }
        );
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Real-time search (optional)
        let searchTimeout;
        $('#searchForm input[name="name"]').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                // Uncomment for real-time search
                // $('#searchForm').submit();
            }, 500);
        });
        
        // Department statistics (optional feature)
        function loadStatistics() {
            $.get('{{ route("employee.statistics") }}', function(data) {
                console.log('Statistik Karyawan:', data);
                // You can display these statistics in a modal or dashboard
            }).fail(function() {
                console.log('Gagal memuat statistik');
            });
        }
        
        // Load statistics on page load
        loadStatistics();
    });
</script>
@endsection