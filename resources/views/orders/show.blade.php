<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Details') }} #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Order Information</h3>
                            <p><strong>Coffee:</strong> {{ $order->coffee->name }}</p>
                            <p><strong>Quantity:</strong> {{ $order->quantity }}</p>
                            <p><strong>Total Price:</strong> ${{ number_format($order->total_price, 2) }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 