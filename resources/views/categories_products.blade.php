@extends('nav_layout')

@section('title', 'Categories Products')
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
<!-- {{ $products->links() }} -->

<!-- Tailwind CSS styled pagination -->

<div class="flex justify-center mt-8">
    <nav aria-label="Pagination">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($products->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link px-3 py-1 rounded-md bg-gray-300 text-gray-700">Prev</span>
            </li>
            @else
            <li class="page-item">
                <a class="page-link px-3 py-1 hover:bg-blue-500  rounded-md bg-blue-500 text-white" href="{{ $products->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">Prev</a>
            </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($products as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
            <li class="page-item disabled hover:bg-blue-500 bg-blue-500 text-white" aria-disabled="true"><span class="page-link hover:bg-blue-500 bg-blue-500 text-white">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
            @foreach ($element as $page => $url)
            @if ($page == $products->currentPage())
            <li class="page-item active hover:bg-blue-500 bg-blue-500 text-white" aria-current="page"><span class="page-link">{{ $page }}</span></li>
            @else
            <li class="page-item bg-blue-500 hover:bg-blue-500 text-white"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
            @endif
            @endforeach
            @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($products->hasMorePages())
            <li class="page-item">
                <a class="page-link px-3 py-1 rounded-md hover:bg-blue-500 bg-blue-500 text-white" href="{{ $products->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">Next</a>
            </li>
            @else
            <li class="page-item disabled">
                <span class="page-link px-3 py-1 rounded-md bg-gray-300 text-gray-700" aria-hidden="true">Next</span>
            </li>
            @endif
        </ul>
    </nav>
</div>



@section('scripts')


<!-- @include('scripts.product_view_script') -->

@include('scripts.add_to_cart_script')


@endsection




@stop