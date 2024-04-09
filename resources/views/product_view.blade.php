@extends('nav_layout')

@section('title', 'View Product')
@section('content')


<div class=" py-6" >



<div class="max-w-6xl mx-auto px-4  sm:px-6 lg:px-8 view_product  "  >
        <div class="flex flex-col md:flex-row -mx-4">
            <div class="md:flex-1 mt-4 px-4">
                <div class="h-[460px] rounded-lg overflow-hidden bg-gray-300  mb-4">
                    <img class="w-full h-full " src="/storage/product_images/{{ $product->image }}" alt="{{ $product->name }}">
                </div>
                <div class="flex justify-center -mx-2 mb-4">
                    <div class="w-1/2 px-2">
                        <button id="{{ $product->id }}" class="w-full  add_to_cart  bg-blue-500 text-white py-2 px-4 rounded-md font-bold hover:bg-blue-600">Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="md:flex-1 px-4">
                <h2 class="text-2xl font-bold text-gray-800 mt-3 mb-4">{{ $product->name }}</h2>
                <div class="grid grid-cols-2 gap-x-4 mb-4">
                    <div class="text-gray-600">
                        <span class="font-bold">Company Name:</span><br>{{ $product->company_name }}
                    </div>
                    <div class="text-gray-600">
                        <span class="font-bold">Color:</span><br>{{ $product->color }}
                    </div>
                    <div class="text-gray-600">
                        <span class="font-bold">Weight:</span><br>{{ $product->weight }}
                    </div>
                    <div class="text-gray-600">
                        <span class="font-bold">Price:</span><br>{{ $product->price }}
                    </div>
                    <div class="text-gray-600">
                        <span class="font-bold">Discount Amount:</span><br>{{ $product->discount_amount }}
                    </div>
                    <div class="text-gray-600">
                        <span class="font-bold">Discount Percent:</span><br>{{ $product->discount_percent }}
                    </div>
                </div>
                <div class="mb-4">
                    <span class="font-bold text-gray-700">Product Description:</span>
                    <p class="text-gray-600 text-sm mt-2">{{ $product->description }}</p>
                    <span class="font-bold text-gray-700">Product quantity:</span>

                    <input type="text" id="quantity_input" value="1" class="  product_quantity_view_page">

                </div>
            </div>
        </div>
    </div>


</div>




@section('scripts')


@include('scripts.product_view_script')



@endsection




@stop