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
            <div>Env file: <strong>{{ $hasEnv ? 'Found' : 'Missing (will create)'}} </strong></div>
            <div>App key: <strong>{{ $hasKey ? 'Set' : 'Not set (will be generated)'}} </strong></div>
        </div>
        <form method="POST" action="{{ route('install.store') }}" class="space-y-4">
            @csrf
            <h2 class="text-lg font-medium">App & Database</h2>
            <div>
                <label class="block">APP_URL</label>
                <input name="app_url" class="w-full p-2 border rounded" placeholder="https://example.com">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block">DB_HOST</label>
                    <input name="db_host" class="w-full p-2 border rounded" placeholder="127.0.0.1">
                </div>
                <div>
                    <label class="block">DB_DATABASE</label>
                    <input name="db_database" class="w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block">DB_USERNAME</label>
                    <input name="db_username" class="w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block">DB_PASSWORD</label>
                    <input name="db_password" class="w-full p-2 border rounded">
                </div>
            </div>
            <h2 class="text-lg font-medium">API Keys</h2>
            <div>
                <label class="block">OpenAI API Key</label>
                <input name="openai" class="w-full p-2 border rounded" placeholder="sk-...">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block">Stripe Public Key</label>
                    <input name="stripe_public" class="w-full p-2 border rounded" placeholder="pk_live_...">
                </div>
                <div>
                    <label class="block">Stripe Secret Key</label>
                    <input name="stripe_secret" class="w-full p-2 border rounded" placeholder="sk_live_...">
                </div>
            </div>
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