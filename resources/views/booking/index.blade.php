@extends('layout')
@section('content')
<!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">All Bookings
                                <a href="{{url('admin/booking/create')}}" class="float-right btn btn-success btn-sm">Add New</a>
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
                                            <th>Guest Name</th>
                                            <th>Guest Email</th>
                                            <th>Room Type</th>
                                            <th>Check-in Date</th>
                                            <th>Check-out Date</th>
                                            <th>Payment</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                   
                                    </tfoot>
                                    <tbody>

                                        <tr>
                                            

                                         @foreach ($booking as $booking)
                                             
                                        
                                             <td>{{$booking->booking_id}}</td>
                                            <td>{{$booking->guest_name}}</td>
                                            <td>{{$booking->guest_email}}</td>
                                            <?php $room_type_id = DB::table('room_types')->where('id', $booking->room_type_id)->first(); ?>
                                            <td value="{{$booking->room_type_id}}">{{$room_type_id->title }}</td>
                                          <td>{{$booking->check_in_date}}</td>                                            
                                          <td>{{$booking->check_out_date}}</td>                                           
                                            <td>{{$booking->total_price}}</td>
                                            
                                            <td>
                                                <a href="{{url('admin/booking/'.$booking->booking_id.'/edit')}}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                                <a href="{{url('admin/booking/'.$booking->booking_id)}}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
                                                <a onclick="return confirm('Are you sure to delete this data?')"  href="{{url('admin/booking/'.$booking->booking_id).'/delete'}}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                            </td>
                                            

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