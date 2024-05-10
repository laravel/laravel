@extends('layout')
@section('content')
<!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Update booking
                                <a href="{{url('admin/booking')}}" class="float-right btn btn-success btn-sm">View All</a>
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
                                <form method="post"action="{{url('admin/booking/')}}">
                                    @csrf
                                    @method('put')
                                    <table class="table table-bordered" >
                                        <tr>
                                            <th>guest_name<span class="text-danger">*</span></th>
                                            <td><input value="{{$booking->guest_name}}" name="full_name" type="text" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Email <span class="text-danger">*</span></th>
                                            <td><input value="{{$booking->guest_email}}" name="email" type="email" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Check-in Date:<span class="text-danger">*</span></th>
                                            <td><input value="{{$booking->check_in_date}}" name="check_in_date" type="date" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Check-out Date:<span class="text-danger">*</span></th>
                                            <td><input value="{{$booking->check_out_date}}" name="check_out_date" type="date" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Room Type:<span class="text-danger">*</span></th>
                                      <td>
                                            <select name="room_type_id" class="form-control">
                                                <option value="0">--- Select ---</option>
                                                @foreach($roomtypes as $rt)
                                                
                                        <option value="{{ $rt->id }}" {{ $rt->id == $rt->room_type_id ? 'selected' : '' }}>
                                          {{$rt->title}}</option>
                                                @endforeach
                                            </select>
                                        </td> 
                                        <tr>
                                            <th>Number of Guests:<span class="text-danger">*</span></th>
                                            <td><input value="{{$booking->num_guests}}" name="num_guests" type="number" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Total Price:<span class="text-danger">*</span></th>
                                            <td><input value="{{$booking->total_price}}" name="total_price" type="text" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Booking Status:<span class="text-danger">*</span></th>
                                            <td><input value="{{$booking->booking_status}}" name="booking_status" type="text" class="form-control" /></td>
                                        </tr>
                                    
                                        
                                        {{-- <td>
                                            <select name="rt_id" class="form-control">
                                                <option value="0">--- Select ---</option>
                                                @foreach($roomtypes as $rt)
                                                <option value="{{$rt->id}}">{{$rt->title}}</option>
                                                @endforeach
                                            </select>
                                        </td> --}}
                                        <tr><td colspan="2">
                                           <a href="{{url('admin/booking')}}" class="btn btn-primary">Back</a>
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

