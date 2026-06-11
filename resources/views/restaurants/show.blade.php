@extends('layouts.app')

@section('title', $restaurant->name . ' - QuickBite')

@section('content')
    {{-- Restaurant Header --}}
    <div class="gradient-bg text-white py-12">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-6">
                <div class="w-100 h-100 rounded-lg overflow-hidden">
                    @if($restaurant->image)
                        <img src="{{ asset('storage/' . $restaurant->image) }}"
                         alt="{{ $restaurant->name }}"
                         class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-4xl">
                        🍽️
                    </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-4xl font-bold mb-2">{{ $restaurant->name }}</h1>
                    <p class="text-lg mb-3">{{ $restaurant->description }}</p>
                    <div class="flex items-center space-x-6">
                        <span><i class="fas fa-star text-yellow-400"></i> {{ $restaurant->rating }}</span>
                        <span><i class="fas fa-clock"></i> {{ $restaurant->delivery_time }} min</span>
                        <span><i class="fas fa-dollar-sign"></i> ${{ number_format($restaurant->delivery_fee, 2) }} delivery</span>
                        <span><i class="fas fa-shopping-bag"></i> Min. order ${{ number_format($restaurant->minimum_order, 2) }}</span>
                    </div>
                    <div class="mt-3">
                        <i class="fas fa-map-marker-alt"></i> {{ $restaurant->address }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Menu --}}
    <div class="container mx-auto px-4 py-12">
        <div class="flex gap-8">
            {{-- Category Sidebar --}}
            <div class="w-64 hidden lg:block">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h3 class="font-bold text-lg mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li><a href="#all" class="text-purple-600 hover:underline">All Items</a></li>
                        @foreach($categories as $category)
                            <li><a href="#category-{{ $category->id }}" class="text-gray-600 hover:text-purple-600 hover:underline">
                                {{ $category->icon }} {{ $category->name }}
                            </a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Menu Items --}}
            <div class="flex-1">
                @foreach($categories as $category)
                    @php
                        $categoryItems = $restaurant->menuItems->where('category_id', $category->id);
                    @endphp
                    @if($categoryItems->count() > 0)
                        <div id="category-{{ $category->id }}" class="mb-12">
                            <h2 class="text-2xl font-bold mb-6">{{ $category->icon }} {{ $category->name }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($categoryItems as $item)
                                    <div class="bg-white rounded-lg shadow-md overflow-hidden flex">
                                        <div class="w-32 h-32 bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-4xl flex-shrink-0">
                                            {{ $category->icon }}
                                        </div>
                                        <div class="p-4 flex-1">
                                            <h3 class="font-bold text-lg mb-1">{{ $item->name }}</h3>
                                            <p class="text-gray-600 text-sm mb-3">{{ $item->description }}</p>
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                                                <a href="{{ route('menuitems.show', $item) }}" class="text-sm text-purple-600 hover:underline">
                                                    View menu item
                                                </a>
                                                <span class="text-xl font-bold text-purple-600">${{ number_format($item->price, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                @if($item->is_available)
                                                    <button onclick="addToCart({{ $item->id }})" 
                                                            class="bg-red-500 text-white px-4 py-2 rounded-full text-sm hover:bg-red-600 transition">
                                                        Add
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 text-sm">Unavailable</span>
                                                @endif
                                            </div>
                                            @if($item->is_vegetarian)
                                                <span class="inline-block mt-2 text-xs bg-green-100 text-green-600 px-2 py-1 rounded">
                                                    🌱 Vegetarian
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function addToCart(menuItemId) {
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                menu_item_id: menuItemId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('cart-count').textContent = data.cart_count;
                alert('Item added to cart!');
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endpush