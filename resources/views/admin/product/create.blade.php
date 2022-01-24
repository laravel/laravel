@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        @include('pages.header')
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Thêm sản phẩm</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                        
                  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection