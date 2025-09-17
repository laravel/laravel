@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-edit"></i> Edit Employee
                        <a href="{{ route('employee.index') }}" class="btn btn-sm btn-default pull-right">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
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
                    
                    <form id="editEmployeeForm" method="POST" action="{{ route('employee.update', $employee->id) }}" class="form-horizontal">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="name" class="col-md-3 control-label">
                                <i class="fa fa-user"></i> Name:
                            </label>
                            <div class="col-md-9">
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       class="form-control" 
                                       placeholder="Enter employee name" 
                                       value="{{ old('name', $employee->name) }}" 
                                       required>
                                @if($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="col-md-3 control-label">
                                <i class="fa fa-envelope"></i> Email:
                            </label>
                            <div class="col-md-9">
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       class="form-control" 
                                       placeholder="Enter email address" 
                                       value="{{ old('email', $employee->email) }}" 
                                       required>
                                @if($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="position" class="col-md-3 control-label">
                                <i class="fa fa-briefcase"></i> Position:
                            </label>
                            <div class="col-md-9">
                                <input type="text" 
                                       id="position" 
                                       name="position" 
                                       class="form-control" 
                                       placeholder="Enter job position" 
                                       value="{{ old('position', $employee->position) }}" 
                                       required>
                                @if($errors->has('position'))
                                    <span class="text-danger">{{ $errors->first('position') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="department" class="col-md-3 control-label">
                                <i class="fa fa-building"></i> Department:
                            </label>
                            <div class="col-md-9">
                                <select id="department" name="department" class="form-control" required>
                                    <option value="">Select Department</option>
                                    <option value="IT" {{ old('department', $employee->department) == 'IT' ? 'selected' : '' }}>IT</option>
                                    <option value="HR" {{ old('department', $employee->department) == 'HR' ? 'selected' : '' }}>Human Resources</option>
                                    <option value="Finance" {{ old('department', $employee->department) == 'Finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="Marketing" {{ old('department', $employee->department) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="Operations" {{ old('department', $employee->department) == 'Operations' ? 'selected' : '' }}>Operations</option>
                                </select>
                                @if($errors->has('department'))
                                    <span class="text-danger">{{ $errors->first('department') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="salary" class="col-md-3 control-label">
                                <i class="fa fa-money"></i> Salary:
                            </label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="number" 
                                           id="salary" 
                                           name="salary" 
                                           class="form-control" 
                                           placeholder="Enter salary amount" 
                                           value="{{ old('salary', $employee->salary) }}" 
                                           min="0" 
                                           required>
                                </div>
                                @if($errors->has('salary'))
                                    <span class="text-danger">{{ $errors->first('salary') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="hire_date" class="col-md-3 control-label">
                                <i class="fa fa-calendar"></i> Hire Date:
                            </label>
                            <div class="col-md-9">
                                <input type="date" 
                                       id="hire_date" 
                                       name="hire_date" 
                                       class="form-control" 
                                       value="{{ old('hire_date', $employee->hire_date) }}" 
                                       required>
                                @if($errors->has('hire_date'))
                                    <span class="text-danger">{{ $errors->first('hire_date') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fa fa-save"></i> Update Employee
                                </button>
                                <button type="reset" class="btn btn-default btn-lg">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                                <a href="{{ route('employee.index') }}" class="btn btn-info btn-lg">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Employee Details Card -->
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-info-circle"></i> Current Employee Details
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Employee ID:</dt>
                                <dd>{{ $employee->id }}</dd>
                                
                                <dt>Full Name:</dt>
                                <dd>{{ $employee->name }}</dd>
                                
                                <dt>Email Address:</dt>
                                <dd>{{ $employee->email }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Position:</dt>
                                <dd><span class="label label-primary">{{ $employee->position }}</span></dd>
                                
                                <dt>Department:</dt>
                                <dd><span class="label label-info">{{ $employee->department }}</span></dd>
                                
                                <dt>Current Salary:</dt>
                                <dd><strong>Rp {{ number_format($employee->salary, 0, ',', '.') }}</strong></dd>
                                
                                <dt>Hire Date:</dt>
                                <dd>{{ date('d F Y', strtotime($employee->hire_date)) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Edit Page -->
<style>
    .panel {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
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
        margin-right: 5px;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .control-label {
        font-weight: 600;
        color: #555;
    }
    
    .input-group-addon {
        background-color: #f5f5f5;
        border-color: #ddd;
    }
    
    .dl-horizontal dt {
        width: 120px;
    }
    
    .dl-horizontal dd {
        margin-left: 140px;
    }
    
    .label {
        font-size: 11px;
    }
    
    .alert {
        border-radius: 6px;
    }
</style>

<script>
    $(document).ready(function() {
        // Form validation
        $('#editEmployeeForm').on('submit', function(e) {
            const name = $('#name').val().trim();
            const email = $('#email').val().trim();
            const position = $('#position').val().trim();
            const department = $('#department').val();
            const salary = $('#salary').val();
            const hireDate = $('#hire_date').val();
            
            // Basic validation
            if (!name || !email || !position || !department || !salary || !hireDate) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                $('#email').focus();
                return false;
            }
            
            // Salary validation
            if (parseFloat(salary) <= 0) {
                e.preventDefault();
                alert('Please enter a valid salary amount.');
                $('#salary').focus();
                return false;
            }
            
            // Name validation (only letters and spaces)
            const nameRegex = /^[a-zA-Z\s]+$/;
            if (!nameRegex.test(name)) {
                e.preventDefault();
                alert('Name should only contain letters and spaces.');
                $('#name').focus();
                return false;
            }
            
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...');
        });
        
        // Auto-capitalize name
        $('#name').on('input', function() {
            let value = $(this).val();
            value = value.replace(/\b\w/g, function(match) {
                return match.toUpperCase();
            });
            $(this).val(value);
        });
        
        // Auto-format email to lowercase
        $('#email').on('blur', function() {
            $(this).val($(this).val().toLowerCase());
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Confirmation before leaving with unsaved changes
        let formChanged = false;
        
        $('#editEmployeeForm input, #editEmployeeForm select').on('change', function() {
            formChanged = true;
        });
        
        $(window).on('beforeunload', function() {
            if (formChanged) {
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });
        
        $('#editEmployeeForm').on('submit', function() {
            formChanged = false;
        });
    });
</script>
@endsection
