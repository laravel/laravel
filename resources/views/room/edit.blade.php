@extends('layout')
@section('content')
<!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Add Room
                                <a href="{{url('admin/rooms')}}" class="float-right btn btn-success btn-sm">View All</a>
                            </h6>
                        </div>
                        <div class="card-body">
                            @if(Session::has('success'))
                            <p class="text-success">{{session('success')}}</p>
                            @endif
                            <div class="table-responsive">
                                <form method="post" action="{{url('admin/rooms/'.$data->id)}}">
                                    @csrf
                                    @method('put')
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Select Room Type</th>
                                            <td>
                                                <select name="rt_id" class="form-control">
                                                    <option value="0">--- Select ---</option>
                                                    @foreach($roomtypes as $rt)
                                                <option @if($data->room_type_id==$rt->id) selected @endif value="{{$rt->id}}">{{$rt->title}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Title</th>
                                            <td><input value="{{$data->title}}" name="title" type="text" class="form-control" /></td>
                                        </tr>
                
                                        <tr>
                                            <th>description	</th>
                                            <td><input value="{{$data->description}}" name="description" type="text" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <a href="{{url('admin/rooms')}}" class="btn btn-primary">submit</a>
                                               
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