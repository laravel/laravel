<x-guest-layout>
    <div class="max-w-3xl mx-auto py-10">
        <div class="flex items-center space-x-3 mb-4">
            @if($agent->avatar_url)
                <img src="{{ $agent->avatar_url }}" class="w-10 h-10 rounded-full" alt="avatar"/>
            @endif
            <div>
                <h1 class="text-2xl font-semibold">{{ $agent->name }}</h1>
                <p class="text-sm text-gray-500">Model: {{ $agent->model }}</p>
            </div>
        </div>
        <div id="chat" class="border rounded p-4 min-h-64">
            <p class="text-gray-500">Chat UI coming next. Welcome: {{ $agent->welcome_message }}</p>
        </div>
        <div class="mt-4">
            <iframe src="{{ route('agents.public', $agent->slug) }}" class="w-full h-96 border rounded"></iframe>
        </div>
    </div>
</x-guest-layout>