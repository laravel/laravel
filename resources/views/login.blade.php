@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading text-center">
                    <h3 class="panel-title">
                        <i class="fa fa-lock"></i> Login Sistem Slip Gaji
                    </h3>
                </div>
                <div class="panel-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="username" 
                                    name="username" 
                                    placeholder="Masukkan username"
                                    value="{{ old('username') }}"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Masukkan password"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember"> Ingat saya
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fa fa-sign-in"></i> Login
                        </button>
                    </form>
                    
                    <hr>
                    
                    <div class="well well-sm">
                        <h5><i class="fa fa-info-circle"></i> Demo Login:</h5>
                        <p><strong>Username:</strong> admin</p>
                        <p><strong>Password:</strong> admin123</p>
                        <hr>
                        <p><strong>Username:</strong> user</p>
                        <p><strong>Password:</strong> user123</p>
                    </div>
                </div>
                <div class="panel-footer text-center">
                    <small class="text-muted">
                        © {{ date('Y') }} Sistem Slip Gaji
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
