@extends('layout')
@section('content')
<!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Add Booking
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
                                <form method="Post"enctype="multipart/form-data" action="{{url('admin/booking')}}">

                                    @csrf
                                    <table class="table table-bordered" >
                                        <tr>
                                            <th>guest_name<span class="text-danger">*</span></th>
                                            <td><input  name="guest_name" type="text" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Email <span class="text-danger">*</span></th>
                                            <td><input  name="guest_email" type="email" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Check-in Date:<span class="text-danger">*</span></th>
                                            <td><input name="check_in_date" type="date" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Check-out Date:<span class="text-danger">*</span></th>
                                            <td><input  name="check_out_date" type="date" class="form-control" /></td>
                                        </tr>
                                        <tr>  
                                                        <th>Room Type<span class="text-danger">*</span></th>
                                                    <td> 
                                                        <select class="form-control roomtype-list" name="room_type_id" aria-placeholder="CHOOSE YOUR ROOM">
                                                            <option value="">CHOOSE YOUR ROOM</option>
                                                            @foreach ($roomtypes as $roomtypes)
                                                                <option value="{{ $roomtypes->id }}">{{ $roomtypes->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                
                        
                                              
                                                
                                        </tr>
                                        <tr>
                                            <th>Number of Guests:<span class="text-danger">*</span></th>
                                            <td><input name="num_guests" type="number" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Total Price:<span class="text-danger">*</span></th>
                                            <td><input  name="total_price" type="text" class="form-control" /></td>
                                        </tr>
                                        <tr>
                                            <th>Booking Status:<span class="text-danger">*</span></th>
                                            <td><input name="booking_status" type="text" class="form-control" /></td>
                                        </tr>

                                        <tr>
                                            <td colspan="2">
                                                <input type="submit" class="btn btn-primary" value="Submit" />
                                            </td>
                                        </tr>
                                    
                                </form>
                                
                                @endsection

                <!-- /.container-fluid -->

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            $(".checkin-date").on('blur',function(){
                var _checkindate=$(this).val();
                // Ajax
                $.ajax({
                    url:"{{url('admin/booking')}}/available-rooms/"+_checkindate,
                    dataType:'json',
                    beforeSend:function(){
                        $(".room-list").html('<option>--- Loading ---</option>');
                    },
                    success:function(res){
                        var _html='';
                        $.each(res.data,function(index,row){
                            _html+='<option data-price="'+row.roomtype.price+'" value="'+row.room.id+'">'+row.room.title+'-'+row.roomtype.title+'</option>';
                        });
                        $(".room-list").html(_html);

                        var _selectedPrice=$(".room-list").find('option:selected').attr('data-price');
                        $(".room-price").val(_selectedPrice);
                        $(".show-room-price").text(_selectedPrice);
                    }
                });
            });

            $(document).on("change",".room-list",function(){
                var _selectedPrice=$(this).find('option:selected').attr('data-price');
                $(".room-price").val(_selectedPrice);
                $(".show-room-price").text(_selectedPrice);
            });

        });
</script>
@endsection

