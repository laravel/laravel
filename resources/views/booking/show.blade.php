@extends('layout')
@section('content')
<!-- Begin Page Content -->
    <div class="container-fluid">
                        <!-- DataTales Example -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">View Booking
                                    <a href="{{url('admin/booking')}}" class="btn btn-primary">Back</a> 
                                    <a href="{{url('admin/booking')}}" class="float-right btn btn-success btn-sm">View All</a>
                                </h6>
                            </div>
                            <div class="card-body">

                            <table class="table table-bordered" >
                                <tr>
                                    <th>guest_name<span class="text-danger"></span></th>
                                    <td>{{$booking->guest_name}}" </td>
                                </tr>
                                <tr>
                                    <th>Email <span class="text-danger"></span></th>
                                    <td>{{$booking->guest_email}} </td>
                                </tr>
                                <tr>
                                    <th>Check-in Date:<span class="text-danger"></span></th>
                                    <td>{{$booking->check_in_date}}</td>
                                </tr>
                                <tr>
                                    <th>Check-out Date:<span class="text-danger"></span></th>
                                    <td>{{$booking->check_out_date}}</td>
                                </tr>
                                <tr>
                                    <th>Room Type:<span class="text-danger"></span></th>
                                    <?php $room_type_id = DB::table('room_types')->where('id', $booking->room_type_id)->first(); ?>
                                    <td value="{{$booking->room_type_id}}">{{$room_type_id->title }}</td>
                                <tr>
                                    <th>Number of Guests:<span class="text-danger"></span></th>
                                    <td>{{$booking->num_guests}}</td>
                                </tr>
                                <tr>
                                    <th>Total Price:<span class="text-danger"></span></th>
                                    <td>{{$booking->total_price}}</td>
                                </tr>
                                <tr>
                                    <th>Booking Status:<span class="text-danger"></span></th>
                                    <td>{{$booking->booking_status}}</td>
                                </tr>

                                @endsection
                            