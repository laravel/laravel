@extends('layouts.app')

@section('title', 'My Orders - QuickBite')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-8">My Orders</h1>

        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold">Order #{{ $order->order_number }}</h3>
                                <p class="text-gray-600">{{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                                <p class="text-gray-700 mt-1">
                                    <i class="fas fa-store"></i> {{ $order->restaurant->name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-4 py-2 rounded-full text-sm font-semibold inline-block
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'preparing') bg-purple-100 text-purple-800
                                    @elseif($order->status == 'on_the_way') bg-orange-100 text-orange-800
                                    @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                                <p class="text-2xl font-bold text-purple-600 mt-2">${{ number_format($order->total, 2) }}</p>
                            </div>
                        </div>
                        
                        <div class="border-t pt-4 flex justify-between items-center">
                            <div class="text-gray-600">
                                <i class="fas fa-shopping-bag"></i> {{ $order->items->count() }} items
                                <span class="mx-2">•</span>
                                <i class="fas fa-map-marker-alt"></i> {{ Str::limit($order->delivery_address, 50) }}
                            </div>
                            <a href="{{ route('orders.show', $order) }}" class="bg-purple-600 text-white px-6 py-2 rounded-full hover:bg-purple-700 transition">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-6xl mb-4">📦</div>
                <h2 class="text-2xl font-bold mb-2">No orders yet</h2>
                <p class="text-gray-600 mb-6">Start ordering delicious food now!</p>
                <a href="{{ route('restaurants.index') }}" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-full hover:bg-purple-700 transition">
                    Browse Restaurants
                </a>
            </div>
        @endif
    </div>
@endsection
