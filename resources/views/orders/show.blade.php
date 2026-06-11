@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' - QuickBite')

@section('content')
    <div class="container mx-auto px-4 py-12">
        {{-- Success Message --}}
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-6 mb-8 rounded">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-3xl mr-4"></i>
                <div>
                    <h2 class="text-2xl font-bold">Order Placed Successfully!</h2>
                    <p class="mt-1">Your order has been confirmed and is being prepared.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Order Details --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold">Order #{{ $order->order_number }}</h2>
                            <p class="text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                        <span class="px-4 py-2 rounded-full text-sm font-semibold
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800
                            @elseif($order->status == 'preparing') bg-purple-100 text-purple-800
                            @elseif($order->status == 'on_the_way') bg-orange-100 text-orange-800
                            @elseif($order->status == 'delivered') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>

                    {{-- Restaurant Info --}}
                    <div class="border-b pb-4 mb-4">
                        <h3 class="font-bold mb-2">Restaurant</h3>
                        <p class="text-lg">{{ $order->restaurant->name }}</p>
                        <p class="text-gray-600">{{ $order->restaurant->address }}</p>
                        <p class="text-gray-600">{{ $order->restaurant->phone }}</p>
                    </div>

                    {{-- Delivery Info --}}
                    <div class="border-b pb-4 mb-4">
                        <h3 class="font-bold mb-2">Delivery Address</h3>
                        <p>{{ $order->delivery_address }}</p>
                        <p class="text-gray-600 mt-1"><i class="fas fa-phone"></i> {{ $order->phone }}</p>
                        @if($order->notes)
                            <p class="text-gray-600 mt-2"><strong>Notes:</strong> {{ $order->notes }}</p>
                        @endif
                    </div>

                    {{-- Order Items --}}
                    <div>
                        <h3 class="font-bold mb-4">Order Items</h3>
                        <div class="space-y-3">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between py-3 border-b">
                                    <div class="flex items-center flex-1">
                                        <div class="w-16 h-16 bg-gradient-to-r from-purple-400 to-pink-400 rounded flex items-center justify-center text-2xl">
                                            🍽️
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="font-semibold">{{ $item->menuItem->name }}</h4>
                                            <p class="text-gray-600 text-sm">${{ number_format($item->price, 2) }} each</p>
                                            @if($item->special_instructions)
                                                <p class="text-gray-500 text-xs mt-1">Note: {{ $item->special_instructions }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">x{{ $item->quantity }}</p>
                                        <p class="text-purple-600 font-bold">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 sticky top-4">
                    <h3 class="text-xl font-bold mb-4">Payment Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delivery Fee</span>
                            <span>${{ number_format($order->delivery_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tax</span>
                            <span>${{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between font-bold text-xl">
                            <span>Total Paid</span>
                            <span class="text-purple-600">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Order Status Timeline --}}
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">Order Status</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white flex-shrink-0">
                                <i class="fas fa-check text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-semibold">Order Placed</p>
                                <p class="text-sm text-gray-600">{{ $order->created_at->format('h:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-8 h-8 {{ in_array($order->status, ['confirmed', 'preparing', 'on_the_way', 'delivered']) ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white flex-shrink-0">
                                <i class="fas fa-check text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-semibold">Confirmed</p>
                                <p class="text-sm text-gray-600">Restaurant accepted</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-8 h-8 {{ in_array($order->status, ['preparing', 'on_the_way', 'delivered']) ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white flex-shrink-0">
                                <i class="fas fa-{{ in_array($order->status, ['preparing', 'on_the_way', 'delivered']) ? 'check' : 'clock' }} text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-semibold">Preparing</p>
                                <p class="text-sm text-gray-600">Food is being prepared</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-8 h-8 {{ in_array($order->status, ['on_the_way', 'delivered']) ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white flex-shrink-0">
                                <i class="fas fa-{{ in_array($order->status, ['on_the_way', 'delivered']) ? 'check' : 'clock' }} text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-semibold">On the Way</p>
                                <p class="text-sm text-gray-600">Driver picked up order</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-8 h-8 {{ $order->status == 'delivered' ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center text-white flex-shrink-0">
                                <i class="fas fa-{{ $order->status == 'delivered' ? 'check' : 'clock' }} text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-semibold">Delivered</p>
                                @if($order->delivered_at)
                                    <p class="text-sm text-gray-600">{{ $order->delivered_at->format('h:i A') }}</p>
                                @else
                                    <p class="text-sm text-gray-600">Estimated: {{ $order->created_at->addMinutes($order->restaurant->delivery_time)->format('h:i A') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('restaurants.index') }}" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-full hover:bg-purple-700 transition mr-4">
                Order Again
            </a>
            <a href="{{ route('orders.my-orders') }}" class="inline-block bg-gray-200 text-gray-800 px-8 py-3 rounded-full hover:bg-gray-300 transition">
                View All Orders
            </a>
        </div>
    </div>
@endsection