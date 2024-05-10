@extends('layout')
@section('content')
<!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Update Customer
                                <a href="{{url('admin/customer')}}" class="float-right btn btn-success btn-sm">View All</a>
                            </h6>
                        </div>
                        <div class="card-body">

                            @if($errors->any())
                                @foreach($errors->all() as $error)
                                    <p class="text-danger">{{$error}}</p>
                                @endforeach
                            @endif

                            @if(Session::has('success'))
                            <p class="text-success">{{session('success')}}</p>
                            @endif
                            <div class="table-responsive">
                                <form method="post" enctype="multipart/form-data" action="{{url('admin/customer/'.$data->id)}}">
                                    @csrf
                                    @method('put')
                                    <table class="table table-bordered" >
                                        <tr>
                                            <th>Full Name <span class="text-danger">*</span></th>
                                            <td><input value="{{$data->full_name}}" name="full_name" type="text" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Email <span class="text-danger">*</span></th>
                                            <td><input value="{{$data->email}}" name="email" type="email" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Mobile <span class="text-danger">*</span></th>
                                            <td><input value="{{$data->mobile}}" name="mobile" type="text" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Photo</th>
                                            <td>
                                                <input name="photo" type="file" />
                                                <input type="hidden" name="prev_photo" value="{{$data->photo}}" />
                                                <img width="100" src="{{asset('storage/app/'.$data->photo)}}" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td><textarea name="address" class="form-control">{{$data->address}}</textarea></td>
                                        </tr>
                                        {{-- <td>
                                            <select name="rt_id" class="form-control">
                                                <option value="0">--- Select ---</option>
                                                @foreach($roomtypes as $rt)
                                                <option value="{{$rt->id}}">{{$rt->title}}</option>
                                                @endforeach
                                            </select>
                                        </td> --}}
                                        <tr>
                                            <td colspan="2">
                                                <input type="submit" class="btn btn-primary"  />
                                            </td> 
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

@endsection

