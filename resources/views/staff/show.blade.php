@extends('layout')
@section('content')
<!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">{{$data->full_name}} Detail
                                <a href="{{url('admin/staff')}}" class="float-right btn btn-success btn-sm">View All</a>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" >
                                    <tr>
                                        <th>Full Name</th>
                                        <td>{{$data->full_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Department</th>
                                        <td>{{$data->department->title}}</td>
                                    </tr>
                                    <tr>
                                        <th>Photo</th>
                                        <td><img width="80" src="{{asset('storage/app/'.$data->photo)}}" /></td>
                                    </tr>
                                    <tr>
                                        <th>Bio</th>
                                        <td>{{$data->bio}}</td>
                                    </tr>
                                    <tr>
                                        <th>Salary Type</th>
                                        <td>{{$data->salary_type}}</td>
                                    </tr>
                                    <tr>
                                        <th>Salary Amount</th>
                                        <td>{{$data->salary_amt}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

@endsection