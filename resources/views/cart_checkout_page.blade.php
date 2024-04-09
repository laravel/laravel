@extends('nav_layout')

@section('title', 'Cart Page')
@section('content')




<div class="   bg-white ">
    <h1 class="mb-10 text-center text-2xl place-items-center font-bold">Cart Items</h1>
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


                        <div class="flex items-center">
                            <button id="add_{{$items->id}}" class="bg-white  cart_decrement_item text-gray-600 px-2 py-2 rounded-l border border-gray-300 hover:bg-blue-100 focus:outline-none">
                                <img style="height: 25px;width: 25px;" src="/icons/minus.png" alt="">
                            </button>

                            <input type="number" style="font-size: 20px;" id="text_quantity_{{$items->id}}" class="w-16 h-10 text-center border border-gray-300" value="{{ $items->quantity  }}" readonly>

                            <button id="sub_{{$items->id }}" class="bg-white cart_increment_item text-gray-600 px-2 py-2 rounded-r hover:bg-blue-600 border border-blue-500 focus:outline-none">
                                <img style="height: 25px;width: 25px;" src="/icons/plus.png" alt="">
                            </button>
                        </div>


                    </div>

                </div>
            </div>

            @endforeach













        </div>
        <!-- Sub total -->
        <div class="mt-6 h-full rounded-lg border bg-white p-6 shadow-lg md:mt-0 md:w-1/3">
            <div class="mb-2 flex justify-between">
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
            <button id="place_order_btn" class="mt-6 w-full rounded-md bg-blue-500 py-1.5 font-medium text-blue-50 hover:bg-blue-600">Place Order</button>
        </div>
    </div>
</div>





@section('scripts')

@include('scripts.cart_checkout_page_script')


@endsection




@stop