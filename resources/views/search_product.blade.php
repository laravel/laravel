@extends('nav_layout')

@section('title', 'Search Products')
@section('content')



<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5" ">
    <!-- Product Cards for Electronics category -->
  




    @foreach ($products as $p)


<div class=" bg-white rounded-lg overflow-hidden ml-3 shadow-lg mb-3" style="width: 17rem;">
    <a href="/products-view/{{ $p->id }}">
        <img src="/storage/product_images/{{ $p->image }}" class="products_page_img" alt="{{ $p->name }}">
    </a>
    <div class="p-4"><a href="/products-view/{{$p->id }}">
            <h5 class="text-base font-semibold mb-1">{{$p->name}}</h5>
            <p class="text-gray-700 text-base">{{ $p->description   }}</p>
            <p class="text-gray-700 text-base">{{ $p->price }} </p>
            <div class="text-gray-700 text-base flex items-center">
                <span class="mr-2">Discount:</span>
                <span class="text-white bg-red-500 text-base px-2 py-1 rounded">{{$p->discount_percent}}</span>
            </div>
        </a><a href="#" id="{{ $p->id }}" quantity="1" class=" add_to_cart  block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 mt-4 rounded-lg text-center">Add to cart</a>
    </div>
</div>

@endforeach

</div>

<br>
<br>




@section('scripts')


<!-- @include('scripts.product_view_script') -->

@include('scripts.add_to_cart_script')


@endsection




@stop