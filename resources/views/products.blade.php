@extends('nav_layout')

@section('title', 'Products')
@section('content')


<div class="container mx-auto py-8 bg-white">
    <h1 class="text-3xl font-bold mb-4 cat_heading">Categories</h1> 
    @foreach($category as $cat)
    <div class="flex ">
    <h2 class="text-lg font-bold ml-2 mt-3 mb-4 cat_heading">{{ $cat->name }}</h2>
    <a href="/categories-products/{{ $cat->id }}" class="ml-2 mt-3 mb-4 justify-items-end flex  view_more_anchor text-black hover:text-gray-700">
         view more
        <img src="/icons/right.png" class="icon_view_right ml-1" alt="View All">
    </a>
</div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5" id="product_list_{{ $cat->id }}">
            <!-- Product Cards for {{$cat->name}} category -->
        </div>
    @endforeach
</div>









@section('scripts')

@include('scripts.product_page_script')

@include('scripts.add_to_cart_script')

@endsection




@stop