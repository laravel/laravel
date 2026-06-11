@extends('layouts.app')

@section('title', $menuItem->name . ' - QuickBite')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
                <div class="h-72 bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-7xl text-white">
                    {{ $menuItem->category->icon ?? '🍽️' }}
                </div>
                <div class="p-6">
                    <h1 class="text-3xl font-bold mb-4">{{ $menuItem->name }}</h1>
                    <p class="text-gray-600 mb-4">{{ $menuItem->description }}</p>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-gray-700">
                            <span class="font-semibold">Category</span>
                            <span>{{ $menuItem->category->name ?? 'Uncategorized' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-700">
                            <span class="font-semibold">Restaurant</span>
                            <a href="{{ optional($menuItem->restaurant)->id ? route('restaurants.show', $menuItem->restaurant) : '#' }}" class="text-purple-600 hover:underline">
                                {{ $menuItem->restaurant->name ?? 'Unknown' }}
                            </a>
                        </div>
                        <div class="flex items-center justify-between text-gray-700">
                            <span class="font-semibold">Price</span>
                            <span class="text-xl font-bold text-purple-600">${{ number_format($menuItem->price, 2) }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if($menuItem->is_available)
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">Available</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm">Unavailable</span>
                            @endif

                            @if($menuItem->is_vegetarian)
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-sm">🌱 Vegetarian</span>
                            @endif

                            @if($menuItem->is_featured)
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-sm">⭐ Featured</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-lg p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start gap-6">
                        <div>
                            <h2 class="text-2xl font-semibold mb-2">About this dish</h2>
                            <p class="text-gray-600">{{ $menuItem->description }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Category</p>
                            <p class="font-semibold">{{ $menuItem->category->name ?? 'Uncategorized' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-8">
                    <h2 class="text-2xl font-semibold mb-4">Action</h2>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Add this item to your cart for quick checkout.</p>
                        </div>
                        <div>
                            @if($menuItem->is_available)
                                <button onclick="addToCart({{ $menuItem->id }})" class="bg-red-500 text-white px-6 py-3 rounded-full font-semibold hover:bg-red-600 transition">
                                    Add to Cart
                                </button>
                            @else
                                <span class="inline-flex items-center px-6 py-3 rounded-full bg-gray-200 text-gray-700">Currently unavailable</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-8">
                    <h2 class="text-2xl font-semibold mb-4">Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                        <div>
                            <p class="font-semibold">Restaurant</p>
                            <p>{{ $menuItem->restaurant->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="font-semibold">Category</p>
                            <p>{{ $menuItem->category->name ?? 'Uncategorized' }}</p>
                        </div>
                        <div>
                            <p class="font-semibold">Price</p>
                            <p>${{ number_format($menuItem->price, 2) }}</p>
                        </div>
                        <div>
                            <p class="font-semibold">Availability</p>
                            <p>{{ $menuItem->is_available ? 'In stock' : 'Unavailable' }}</p>
                        </div>
                    </div>
                </div>
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
            if (data.success) {
                document.getElementById('cart-count').textContent = data.cart_count;
                alert('Item added to cart!');
            }
        })
        .catch(console.error);
    }
</script>
@endpush
