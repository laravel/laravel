@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        @include('pages.header')
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Thêm Quyền</div>
                @if ($errors->any())
                     <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
              @endif
              <!-- <div class="card"> -->
                    <div class="card-body">
                <form action="{{ URL::to('/role-update/'.$role->id)}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Tên quyền</label>
                        <input type="text" value="{{$role->name}}" class="form-control" id="name" name ="name">
                    </div>
                    <div class="form-group">
                        <label for="name">Mô tả quyền</label>
                        <input type="text"  value="{{$role->description}}" class="form-control" id="desc" name ="desc">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection