<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">Payments</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="w-full text-left">
                        <thead>
                            <tr>
                                <th class="py-2">User</th>
                                <th>Provider</th>
                                <th>Reference</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($payments as $p)
                            <tr class="border-t">
                                <td class="py-2">{{ $p->user->email }}</td>
                                <td>{{ ucfirst($p->provider) }}</td>
                                <td>{{ $p->provider_ref }}</td>
                                <td>{{ $p->amount }} {{ $p->currency }}</td>
                                <td>{{ $p->status }}</td>
                                <td class="text-right space-x-2">
                                    @if($p->provider === 'manual' && $p->status === 'pending')
                                        <form method="POST" action="{{ route('admin.payments.approve', $p) }}" class="inline">@csrf<button class="text-green-600">Approve</button></form>
                                        <form method="POST" action="{{ route('admin.payments.fail', $p) }}" class="inline">@csrf<button class="text-red-600">Fail</button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $payments->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>