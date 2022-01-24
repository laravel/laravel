@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        @include('pages.header')
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Liệt kê danh sách nguời dùng</div>
            </div>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
              @endif
            <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">STT</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $key=>$user)
                        <tr>
                         <td>{{$key+1}}</td>
                         <td>{{$user->name}}</td>
                         
                         <td>{{$user->email}}</td>
                         
                         <td>
                         {{ $user->role_id == "1" ? 'Admin': 'NV_1'}}</td>

                         <td><a class="btn btn-primary" href="{{URL::to('users-edit/'.$user->id)}}">Edit</a> 
                          <a onclick="return confirm('Bạn muốn xóa')" class=" btn btn-success" href="{{URL::to('users-delete/'.$user->id)}}">Delete</a> 
</td>  


                        </tr>
                       @endforeach
                    </tbody>
                    </table>
        </div>
    </div>
</div>
@endsection