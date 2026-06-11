@extends('layouts.app')

@section('title', 'Checkout - QuickBite')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-8">Checkout</h1>

        <form action="{{ route('orders.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Delivery Details --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Delivery Information</h2>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Delivery Address *</label>
                            <textarea name="delivery_address" rows="3" required
                                      class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none"
                                      placeholder="Enter your delivery address">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Phone Number *</label>
                            <input type="tel" name="phone" required
                                   value="{{ old('phone') }}"
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none"
                                   placeholder="Your phone number">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Special Instructions (Optional)</label>
                            <textarea name="notes" rows="2"
                                      class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none"
                                      placeholder="Any special requests?">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold mb-4">Order Items</h2>
                        <div class="space-y-3">
                            @foreach($cart as $item)
                                <div class="flex justify-between items-center py-2 border-b">
                                    <div>
                                        <span class="font-semibold">{{ $item['name'] }}</span>
                                        <span class="text-gray-500"> x{{ $item['quantity'] }}</span>
                                    </div>
                                    <span class="font-semibold">${{ number_format($item['price'] * $item['quantity'], 2) }}

                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Delivery Fee</span>
                                <span>${{ number_format($deliveryFee, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tax</span>
                                <span>${{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span>${{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                            Place Order
                        </button>
                        
                        <a href="{{ route('cart.index') }}" class="block text-center text-gray-600 hover:text-gray-800 mt-3">
                            Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection