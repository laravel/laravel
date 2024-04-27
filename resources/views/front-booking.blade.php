@extends('frontlayout')
@section('content')
<div class="container my-4">
	<h3 class="mb-3">Room Booking</h3>
    @if($errors->any())
        @foreach($errors->all() as $error)
            <p class="text-danger">{{$error}}</p>
        @endforeach
    @endif

    @if(Session::has('success'))
    <p class="text-success">{{session('success')}}</p>
    @endif
    <div class="table-responsive">
        <form method="post" enctype="multipart/form-data" action="{{url('page/Thankyou')}}">
            @csrf
            <table class="table table-bordered">
                <tr>
                    <th>Customer Name <span class="text-danger">*</span></th>
                    <td><input name="customer_name" type="text" class="form-control" /></td>
                </tr>
                <tr>
                    <th>CheckIn Date <span class="text-danger">*</span></th>
                    <td><input name="checkin_date" type="date" class="form-control checkin-date" /></td>
                </tr>
                <tr>
                    <th>CheckOut Date <span class="text-danger">*</span></th>
                    <td><input name="checkout_date" type="date" class="form-control" /></td>
                </tr>
                <tr>
                  
                        <select class="form-control room-list" name="room_id"> --}}
                            <tr>
                                <th>Room Type <span class="text-danger">*</span></th>
                            <td> 
                                <select class="form-control roomtype-list" name="roomtype_id" aria-placeholder="CHOOSE YOUR ROOM">
                                    <option value="">CHOOSE YOUR ROOM</option>
                                    @foreach ($roomtypes as $roomtypes)
                                        <option value="{{ $roomtypes->id }}">{{ $roomtypes->title }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        </select>
                        
                </tr>
                <tr>
                    <th>Total Adults <span class="text-danger">*</span></th>
                    <td><input name="total_adults" type="number" class="form-control" /></td>
                </tr>
                <tr>
                    <th>Total Children</th>
                    <td><input name="total_children" type="number" class="form-control" /></td>
                </tr>
            </tr>
            <th>Payment Method <span class="text-danger">*</span></th>
            <td>
                <select class="form-control" name="payment_method" aria-placeholder="CHOOSE YOUR PAYMENT METHOD">
                    <option value="">CHOOSE YOUR PAYMENT METHOD</option>
                    <option value="1">Cash</option>
                    <option value="2">Card</option>
                    
                </select>
                
            </td>
                <tr>
                    <td colspan="2">
                        @if(Session::has('data'))
                    	<input type="hidden" name="customer_id" value="{{session('data')[0]->id}}" />
                        @endif
                        <input type="hidden" name="roomprice" class="room-price" value="" />
                    	<input type="hidden" name="ref" value="front" />
                        {{-- <button type="submit" class="btn btn-primary">Book Now</button> --}}
                        <input type="submit" class="btn btn-primary" />
                        
                    </td> 
                </tr>
            </table>
        </form>
    </div>   
<div>
    {{-- <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th>Room</th>
                <th>Price</th>
            </tr>
            <tr>    
                <td class="show-room-title" >{{$roomtypes->title}}</td>
                <td class="show-room-price">{{ $roomtypes->price }}</td>
            </tr>
        </table>
    </div>             --}}


<script type="text/javascript">
    $(document).ready(function(){
        $(".checkin-date").on('blur',function(){
            var _checkindate=$(this).val();
            // Ajax
            $.ajax({
                url:"{{url('/page/Thankyou')}}/available-rooms/"+_checkindate,
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