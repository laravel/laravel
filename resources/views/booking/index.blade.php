@extends('layout')
@section('content')
<!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">All Bookings
                                {{-- <a href="{{url('admin/booking/create')}}" class="float-right btn btn-success btn-sm">Add New</a> --}}
                            </h6>
                        </div>
                        <div class="card-body">
                            @if(Session::has('success'))
                            <p class="text-success">{{session('success')}}</p>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Customer</th>
                                            <th>Room No.</th>
                                            <th>CheckIn Date</th>
                                            <th>CheckOut Date</th>
                                            <th>Adults</th>
                                            <th>Children</th>
                                            <th>payment</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                   
                                    </tfoot>
                                    <tbody>
                                        @foreach($data as $booking)
                                        <tr>
                                            <td>{{$booking->id}}</td>
                                            <td>{{$booking->customer_name}}</td>
                                            <td>{{$booking->room_no}}</td> 
                                            <td>{{$booking->checkin_date}}</td>
                                            <td>{{$booking->checkout_date}}</td>
                                            <td>{{$booking->total_adults}}</td>
                                            <td>{{$booking->total_children}}</td>
                                            <td>{{$booking->payment}}</td>
                                            
                                            <td><a href="{{url('admin/booking/'.$booking->id.'/delete')}}" class="text-danger" onclick="return confirm('Are you sure you want to delete this data?')"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

@section('scripts')
<!-- Custom styles for this page -->
<link href="{{asset('public')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<!-- Page level plugins -->
<script src="{{asset('public')}}/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="{{asset('public')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script src="{{asset('public')}}/js/demo/datatables-demo.js"></script>

@endsection

@endsection