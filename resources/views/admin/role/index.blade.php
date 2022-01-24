@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        @include('pages.header')
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Liệt kê danh sách danh mục</div>
            </div>
            <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">STT</th>
                        <th scope="col">Tên quyền</th>
                        <th scope="col">Mô tả</th>
                        <th scope="col">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($role as $key=> $role_list)
                        <tr>
                         <td>{{$key+1}}</td>
                         <td>{{$role_list->name}}</td>
                         <td>{{$role_list->description}}</td>
                         <td><a class="btn btn-primary" href="{{URL::to('role-edit/'.$role_list->id)}}">Sửa</a> 
                          <a onclick="return confirm('Bạn muốn xóa')" class=" btn btn-success" href="{{URL::to('role-delete/'.$role_list->id)}}">Xóa</a> 
</td>  
                        </td>
                        </tr>
                       @endforeach
                    </tbody>
                    </table>
        </div>
    </div>
</div>
@endsection