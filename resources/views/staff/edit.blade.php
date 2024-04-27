@extends('layout')
@section('content')
<!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Update Staff
                                <a href="{{url('admin/rooms')}}" class="float-right btn btn-success btn-sm">View All</a>
                            </h6>
                        </div>
                        <div class="card-body">
                            @if(Session::has('success'))
                            <p class="text-success">{{session('success')}}</p>
                            @endif
                            <div class="table-responsive">
                                <form enctype="multipart/form-data" method="post" action="{{url('admin/staff/'.$data->id)}}">
                                    @method('put')
                                    @csrf
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Full Name</th>
                                            <td><input value="{{$data->full_name}}" name="full_name" type="text" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Select Department</th>
                                            <td>
                                                <select name="department_id" class="form-control">
                                                    <option value="0">--- Select ---</option>
                                                    @foreach($departs as $dp)
                                                    <option @if($data->id==$dp->id) selected @endif value="{{$dp->id}}">{{$dp->title}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Photo</th>
                                            <td>
                                                <input name="photo" type="file" />
                                                <input type="hidden" value="{{$data->photo}}" name="prev_photo" />
                                                <img width="80" src="{{asset('storage/app/'.$data->photo)}}" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Bio</th>
                                            <td><textarea class="form-control" name="bio">{{$data->bio}}</textarea></td>
                                        </tr>
                                        <tr>
                                            <th>Salary Type</th>
                                            <td>
                                                <input @if($data->salary_type=='daily') checked @endif type="radio" name="salary_type" value="daily"> Daily
                                                <input @if($data->salary_type=='monthly') checked @endif type="radio" name="salary_type" value="monthly"> Monthly
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Salary Amount</th>
                                            <td><input value="{{$data->salary_amt}}" name="salary_amt" class="form-control" type="number" /></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <input type="submit" class="btn btn-primary" />
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