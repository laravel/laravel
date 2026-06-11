@extends('layouts.app')

@section('title', 'Home - QuickBite')

@section('content')
    {{-- Hero Section --}}
    <section class="gradient-bg text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-4">Delicious Food Delivered Fast</h1>
            <p class="text-xl mb-8">Order from your favorite restaurants and get it delivered to your door</p>
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('restaurants.index') }}" method="GET" class="flex gap-4">
                    <input type="text" name="search" placeholder="Search for restaurants or dishes..." 
                           class="flex-1 px-6 py-3 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <button type="submit" class="bg-red-500 px-8 py-3 rounded-full font-semibold hover:bg-red-600 transition">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Categories --}}
    <section class="container mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold text-center mb-12">Popular Categories</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('restaurants.index', ['category' => $category->slug]) }}" 
                   class="bg-white p-6 rounded-xl shadow-md hover-scale text-center">
                    <div class="text-5xl mb-3">{{ $category->icon }}</div>
                    <h3 class="font-semibold text-lg">{{ $category->name }}</h3>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured Items --}}
    <section class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Featured Dishes</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($featuredItems as $item)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover-scale">
                        <div class="h-48 bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-6xl">
                            {{ $item->category->icon ?? '🍽️' }}
                        </div>
                        <div class="p-6">
                            <div class="text-sm text-gray-500 mb-2">{{ $item->restaurant->name }}</div>
                            <h3 class="text-xl font-bold mb-2">{{ $item->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $item->description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-purple-600">${{ number_format($item->price, 2) }}</span>
                                <button onclick="addToCart({{ $item->id }})" 
                                        class="bg-red-500 text-white px-6 py-2 rounded-full hover:bg-red-600 transition">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Restaurants --}}
    <section class="container mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold text-center mb-12">Popular Restaurants</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($restaurants as $restaurant)
                <a href="{{ route('restaurants.show', $restaurant) }}" 
                   class="bg-white rounded-xl shadow-md overflow-hidden hover-scale">
                   <div class="w-full h-48 rounded-lg overflow-hidden">
                    @if($restaurant->image)
                        <img 
                         src="{{ asset('storage/' . $restaurant->image) }}"
                         alt="{{ $restaurant->name }}"
                         class="w-full h-full object-cover"
                         >
                    @else
                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-4xl">
                        🍽️
                        </div>
                    @endif
                </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">{{ $restaurant->name }}</h3>
                        <p class="text-gray-600 mb-3">{{ $restaurant->description }}</p>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span><i class="fas fa-star text-yellow-400"></i> {{ $restaurant->rating }}</span>
                            <span><i class="fas fa-clock"></i> {{ $restaurant->delivery_time }} min</span>
                            <span><i class="fas fa-dollar-sign"></i> ${{ number_format($restaurant->delivery_fee, 2) }} delivery</span>
                        </div>
                        <div class="mt-3 text-sm text-gray-500">
                            <i class="fas fa-utensils"></i> {{ $restaurant->menu_items_count }} items
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $restaurants->links() }}
        </div>
    </section>

    {{-- Features --}}
    <section class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Why Choose QuickBite?</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-5xl mb-4">🚀</div>
                    <h3 class="text-xl font-bold mb-2">Fast Delivery</h3>
                    <p class="text-gray-600">Get your food in 30 minutes or less</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl mb-4">⭐</div>
                    <h3 class="text-xl font-bold mb-2">Quality Food</h3>
                    <p class="text-gray-600">Only the best restaurants</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl mb-4">💳</div>
                    <h3 class="text-xl font-bold mb-2">Easy Payment</h3>
                    <p class="text-gray-600">Multiple payment options</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl mb-4">📱</div>
                    <h3 class="text-xl font-bold mb-2">Track Orders</h3>
                    <p class="text-gray-600">Real-time order tracking</p>
                </div>
            </div>
        </div>
    </section>
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
