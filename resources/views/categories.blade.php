@extends('nav_layout')

@section('title', 'categories')
@section('content')


<div class="bg-white py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 ">
        <h2 class="text-3xl font-bold text-black w-fit cat_page_h2 bg-white  py-2 mx-auto  cat_heading mb-6">Categories</h2>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3  gap-6">

            @foreach($categories as $c)
            <div class="md:flex-1 ">
                <a href="/categories-products/{{$c->id }}"  style="width: fit-content;">
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg">
                        <img src="/storage/category_images/{{ $c->image }}" class="w-full h-48 object-cover" alt="Category Image">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $c->name }}</h3>
                            <p class="text-gray-600">{{ $c->description }}</p>
                            <a href="/categories-products/{{$c->id }}" class="block mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-center">Explore</a>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach

        </div>
    </div>
</div>



@section('scripts')

<!-- @include('scripts.product_page_script') -->


@endsection




@stop