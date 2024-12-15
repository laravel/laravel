<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($orders->isEmpty())
                        <p class="text-center">No orders yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left">Order ID</th>
                                        <th class="px-6 py-3 text-left">Coffee</th>
                                        <th class="px-6 py-3 text-left">Quantity</th>
                                        <th class="px-6 py-3 text-left">Total</th>
                                        <th class="px-6 py-3 text-left">Status</th>
                                        <th class="px-6 py-3 text-left">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($orders as $order)
                                        <tr>
                                            <td class="px-6 py-4">#{{ $order->id }}</td>
                                            <td class="px-6 py-4">{{ $order->coffee->name }}</td>
                                            <td class="px-6 py-4">{{ $order->quantity }}</td>
                                            <td class="px-6 py-4">${{ number_format($order->total_price, 2) }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 rounded text-sm 
                                                    @if($order->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 