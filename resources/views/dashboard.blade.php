<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Coffee Shop Menu') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($products as $product)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $product->description }}</p>
                            <p class="text-gray-800 dark:text-gray-200 mt-4 font-bold">${{ number_format($product->price, 2) }}</p>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">Stock: {{ $product->stock_quantity }}</p>
                            
                            @auth
                                <form action="{{ route('orders.store') }}" method="POST" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="coffee_id" value="{{ $product->id }}">
                                    <div class="flex items-center gap-4">
                                        <select name="quantity" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            @for ($i = 1; $i <= min(5, $product->stock_quantity); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <button type="submit" 
                                            @if($product->stock_quantity < 1) disabled @endif
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                                            @if($product->stock_quantity < 1)
                                                Out of Stock
                                            @else
                                                Order Now
                                            @endif
                                        </button>
                                    </div>
                                </form>
                            @else
                                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    Please <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500">login</a> to order
                                </p>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                            No products available yet.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
