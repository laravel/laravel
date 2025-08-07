<x-guest-layout>
    <div class="max-w-3xl mx-auto py-10">
        <h1 class="text-2xl font-semibold mb-4">Shared Chat</h1>
        <div class="space-y-3">
            @foreach($messages as $m)
                <div class="{{ $m->role === 'user' ? 'text-right' : '' }}">
                    <div class="inline-block rounded p-2 {{ $m->role === 'user' ? 'bg-indigo-50' : 'bg-gray-50' }}">
                        <div class="text-xs text-gray-500 mb-1">{{ ucfirst($m->role) }}</div>
                        <div>{{ $m->content }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-guest-layout>