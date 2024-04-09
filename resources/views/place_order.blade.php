@extends('nav_layout')

@section('title', 'Place Order')

@section('content')



<div class="   bg-white " id="next_animation">
    <h1 class="mb-10 text-center text-2xl place-items-center font-bold">Confirm Order</h1>
    <div class="mx-auto max-w-5xl justify-center px-6 md:flex md:space-x-6 xl:px-0">
        <div class="rounded-lg md:w-2/3">


            @foreach($cart_items as $items)

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


            <div class=" flex justify-between ">
                <p class="font-medium text-black text-base ">Name:</p>
                <p id="main_quantity" class="font-medium text-black  text-base ">{{ $user_data->full_name }}</p>
            </div>

            <div class=" flex justify-between ">
                <p class="font-medium text-black text-base ">Email:</p>
                <p id="main_quantity" class="font-medium text-black  text-base ">{{ $user_data->email }}</p>
            </div>

            <div class=" flex justify-between ">
                <p class="font-medium text-black text-base ">Address:</p>
                <!-- <p id="main_quantity" class="font-medium text-black  text-base ">{{ $user_data->address }}</p> -->
                @php
              
                     
                
                  $address = session()->get('user_address');

                


                @endphp
                <p class="font-medium text-black text-base truncate" title="{{ $address }}" >{{ Str::limit($address, 15) }}</p>
                
            </div>

            <div class=" mb-2 flex justify-between ">
            <button id="change_address" class="mt-6 w-full rounded-md bg-blue-500 py-1.5 font-medium text-blue-50 hover:bg-blue-600">Change Address </button>
              
                
            </div>

            <div class=" flex justify-between">
                <p class="text-gray-700">Total Items</p>
                <p id="main_quantity" class="text-gray-700">{{ $cart->number_of_items }}</p>
            </div>

            <div class="flex justify-between">
                <p class="text-gray-700">Total Amount</p>
                <p id="main_price" class="text-gray-700"> {{ $cart->price  }} </p>

            </div>
            <div class="flex justify-between">
                <p class="text-gray-700">Total Disocunt</p>
                <p id="main_discount_amount" class="text-gray-700"> {{ $cart->discount_amount  }} </p>
            </div>

            <hr class="my-4" />
            <div class="flex justify-between">
                <p class="text-lg font-bold"> Amount to Pay</p>
                <div class="">
                    <p id="main_sub_total_price" class="mb-1 text-lg font-bold"> {{ $cart->sub_total   }} </p>
                </div>
            </div>
            <button id="place_order_btn" class="mt-6 w-full rounded-md bg-blue-500 py-1.5 font-medium text-blue-50 hover:bg-blue-600">Confirm Order</button>
        </div>

    </div>
</div>





@section('scripts')
@include('scripts.place_order_page_script')
@endsection

@stop