@extends('layouts.app')

@section('title', 'Shopping Cart - QuickBite')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

        @if(empty($cart))
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-6xl mb-4">🛒</div>
                <h2 class="text-2xl font-bold mb-2">Your cart is empty</h2>
                <p class="text-gray-600 mb-6">Add some delicious items to get started!</p>
                <a href="{{ route('restaurants.index') }}" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-full hover:bg-purple-700 transition">
                    Browse Restaurants
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Cart Items --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cart as $id => $item)
                        <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                            <div class="w-24 h-24 bg-gradient-to-r from-purple-400 to-pink-400 rounded-lg flex items-center justify-center text-3xl flex-shrink-0">
                                🍽️
                            </div>
                            <div class="ml-6 flex-1">
                                <h3 class="font-bold text-lg">{{ $item['name'] }}</h3>
                                <p class="text-purple-600 font-semibold">${{ number_format($item['price'], 2) }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center border rounded-lg">
                                    <button onclick="updateQuantity({{ $id }}, -1)" class="px-3 py-1 hover:bg-gray-100">-</button>
                                    <span class="px-4 py-1 border-x">{{ $item['quantity'] }}</span>
                                    <button onclick="updateQuantity({{ $id }}, 1)" class="px-3 py-1 hover:bg-gray-100">+</button>
                                </div>
                                <span class="font-bold text-lg w-20 text-right">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                <button onclick="removeFromCart({{ $id }})" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>${{ number_format($total, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Delivery Fee</span>
                                <span>$5.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tax (10%)</span>
                                <span>${{ number_format($total * 0.10, 2) }}</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span>${{ number_format($total + 5 + ($total * 0.10), 2) }}</span>
                            </div>
                        </div>
                        
                        @auth
                            <a href="{{ route('checkout') }}" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                                Proceed to Checkout
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                                Login to Checkout
                            </a>
                        @endauth
                        
                        <form action="{{ route('cart.clear') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="w-full text-red-500 hover:text-red-700 py-2">
                                Clear Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function removeFromCart(itemId) {
        if(!confirm('Remove this item from cart?')) return;
        
        fetch('{{ route("cart.remove") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ id: itemId })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        });
    }

    function updateQuantity(itemId, change) {
        // Implement quantity update logic
        console.log('Update quantity for item', itemId, 'by', change);
    }
</script>
@endpush