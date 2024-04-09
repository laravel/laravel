@extends('nav_layout')

@section('title', 'Order List')
@section('content')





<div class="bg-white mb-6 p-6 rounded-lg ">
    <h1 class="mb-10 text-center text-2xl font-bold">Your Orders</h1>
    <div class="mx-auto text-lg " style="margin-left:100px !important;"  >Total Orders: <span id="total_orders"></span></div>
    <div class="order_list_container  flex flex-wrap justify-center gap-6 pt-1 pb-2 max-w-7xl mx-auto" id="order_list">
       
    </div>
</div>
<br><br><br>




@section('scripts')


@include('scripts.order_list_script')



@endsection




@stop