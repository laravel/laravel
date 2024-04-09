@extends('home_layout')

@section('title', 'Home Page')
@section('content')





<!-- Hero Section -->
<section class="bg-white text-blue-500">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="md:w-1/2 mb-8 md:mb-0">
                <h2 class="text-4xl font-semibold mb-4">Discover Great Deals</h2>
                <p class="text-lg mb-6">Shop our wide selection of products at amazing prices.</p>
                <a href="/products" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded">Shop Now</a>
            </div>

            <!-- <img src="{{ asset('images/6666912.jpg') }}" class="home_page_illustration" alt="Hero Image" class="rounded-md md:w-1/2"> -->
            <img src="{{ asset('images/6666912.jpg') }}" class="home_page_illustration md:object-cover object-center md:w-1/2 w-full" alt="Hero Image">


        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="container bg-gray-900 mx-auto px-4 py-12">
   
    <h1 class="text-3xl font-bold text-white mb-4 cat_heading">Categories</h1>
    @foreach($category as $cat)
    <div class="flex ">
        <h2 class="text-lg text-white font-bold ml-2 mt-3 mb-4 cat_heading">{{ $cat->name }}</h2>
        <a href="/categories-products/{{ $cat->id }}" class="ml-2 mt-3 mb-4 justify-items-end flex  view_more_anchor text-black hover:text-gray-700">
            view more
            <img src="/icons/right.png" class="icon_view_right ml-1" alt="View All">
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5" id="product_list_{{ $cat->id }}">
        <!-- Product Cards for {{$cat->name}} category -->
    </div>
    @endforeach
</section>












@section('scripts')

<!-- Add jQuery -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->

@include('scripts.product_page_script')

@include('scripts.add_to_cart_script')

<script>
    $(document).ready(function() {
        $('#toggleNavbarBtn').click(function() {
            $('#navbarNav').toggleClass('hidden'); // Toggle the 'hidden' class on #navbarNav
        });
    });
</script>



@endsection




@stop