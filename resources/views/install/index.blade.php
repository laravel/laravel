<x-guest-layout>
    <div class="max-w-xl mx-auto py-10">
        <h1 class="text-2xl font-semibold mb-4">Installation</h1>
        @if($errors)
            @if(count($errors))
                <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
                    <strong>Fix before continuing:</strong>
                    <ul class="list-disc ml-6">
                        @foreach($errors as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif
        <div class="mb-6 p-3 border rounded">
            <div>Env file: <strong>{{ $hasEnv ? 'Found' : 'Missing (using .env.example)'}} </strong></div>
            <div>App key: <strong>{{ $hasKey ? 'Set' : 'Not set (will be generated)'}} </strong></div>
        </div>
        <form method="POST" action="{{ route('install.store') }}" class="space-y-4">
            @csrf
            <h2 class="text-lg font-medium">Create Admin User</h2>
            <div>
                <label class="block">Name</label>
                <input name="name" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Install</button>
        </form>
    </div>
</x-guest-layout>