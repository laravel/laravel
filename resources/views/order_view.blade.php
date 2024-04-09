@extends('nav_layout')

@section('title', 'Orders Items')

@section('content')



<div class="   bg-white ">
    <h1 class="mb-10 text-center text-2xl place-items-center font-bold">Order Details</h1>
    <div class="mx-auto max-w-5xl justify-center px-6 md:flex md:space-x-6 xl:px-0">
        <div class="rounded-lg md:w-2/3">


            @foreach($order_items as $items)

            <div id="item_container_{{ $items->id }}" class="justify-between mb-6 rounded-lg  p-6 shadow-lg sm:flex sm:justify-start">
                <img src="/storage/product_images/{{$items->image }}" alt="{{$items->name }}" style="width:160px;height:110px;" class="w-full rounded-lg sm:w-40" />
                <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between">
                    <div class=" sm:mt-0">
                        <h2 class="text-lg font-bold text-gray-900">{{$items->name}}</h2>
                        <p class="mt-1 text-gray-900 text-base "> Price: {{ $items->product_price  }} </p>

                        <p id="quantity_{{$items->id }}" class="mt-1 text-gray-900 text-base ">Quantity: {{ $items->quantity  }}</p>
                        <p id="price_{{$items->id }}" class="mt-1 text-gray-900 text-base ">Total: {{ $items->total_price }}</p>
                        <p id="discount_{{$items->id }}" class="mt-1 text-gray-900 text-base ">Discount: {{ $items->discount_amount  }} </p>


                    </div>
                    <div class="mt-4  flex justify-between sm:space-y-6 sm:mt-0 sm:block sm:space-x-6">





                    </div>

                </div>
            </div>

            @endforeach

        </div>

        <!-- Sub total -->
        <div class="mt-6 h-full rounded-lg border bg-white p-6 shadow-lg md:mt-0 md:w-1/3">
            <div class="mb-2 flex justify-between">
                <p class="text-gray-700">Total Items</p>
                <p id="main_quantity" class="text-gray-700">{{ $order->number_of_items }}</p>
            </div>
            <div class="flex justify-between">
                <p class="text-gray-700">Total Amount</p>
                <p id="main_price" class="text-gray-700"> {{ $order->price  }} </p>

            </div>
            <div class="flex justify-between">
                <p class="text-gray-700">Total Disocunt</p>
                <p id="main_discount_amount" class="text-gray-700"> {{ $order->discount_amount  }} </p>
            </div>

            <div class="flex justify-between mb-1">
                <p class="text-gray-700">Address</p>
                <p id="main_discount_amount " title="{{ $order_address->full_address  }}" class="text-gray-700 ml-2 "> {{ Str::limit($order_address->full_address, 15) }} </p>
            </div>

            <div class="flex justify-between">
                <p class="text-gray-700"> Order Status</p>


                @if($order->status == 0)
                <span class="bg-amber-500 text-white  text-sm font-medium me-2 px-2.5 py-0.5 rounded ">Pending</span>
                @endif


                @if($order->status == 1)
                <span class="bg-green-500 text-white text-sm font-medium me-2 px-2.5 py-0.5 rounded ">Completed</span>
                @endif

                @if($order->status == 2)
                <span class="bg-red-500 text-white text-sm font-medium me-2 px-2.5 py-0.5 rounded ">Cancel</span>
                @endif

                @if($order->status == 3)
                <span class="bg-blue-500 text-white text-sm font-medium me-2 px-2.5 py-0.5 rounded ">Accepted</span>
                @endif

                @if($order->status == 4)
                <span class="bg-amber-900 text-white text-sm font-medium me-2 px-2.5 py-0.5 rounded ">Rejected</span>
                @endif

                @if($order->status == 5)
                <span class="bg-pink-500 text-white text-sm font-medium me-2 px-2.5 py-0.5 rounded ">Dispatched</span>
                @endif

                @if($order->status == 6)
                <span class="bg-purple-500 text-white text-sm font-medium me-2 px-2.5 py-0.5 rounded ">Delivered</span>
                @endif





            </div>


            <hr class="my-4" />
            <div class="flex justify-between ">
                <p class="text-lg font-bold"> Amount to Pay</p>
                <div class="">
                    <p id="main_sub_total_price" class="mb-1 text-lg font-bold"> {{ $order->sub_total   }} </p>
                </div>
            </div>

            @if($order->status == 0)
                <div class="flex justify-between ">
                    <div class="">
                        <button id="{{ $order->id }}" style="margin-left: 80px; width:120px;height: 30px;" class=" cancel_order_btn block bg-red-500 hover:bg-red-600 text-white font-semibold    mt-1 rounded-lg text-center"> Cancel Order</button>
                    </div>
                </div>
            @endif

            @if(isset($order->status_reason) && $order->status == 4 )
                <div>
                <div class="mb-2 flex justify-between">
                </div>
                <div class="mb-2 flex justify-between">
                    <p id="main_quantity" class="text-red-500 font-bold" >{{ $order->status_reason }}</p>
                </div>
                </div>
            @endif


        </div>

        <br>


    




    </div>





</div>






@section('scripts')
@include('scripts.order_view_script')
@endsection

@stop