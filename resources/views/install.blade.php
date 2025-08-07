<x-guest-layout>
    <div class="max-w-xl mx-auto py-10">
        <h1 class="text-2xl font-semibold mb-4">Installation</h1>
        <p class="mb-4">Upload the ZIP to your Plesk domain, extract it to the document root, and ensure the web server user can write to the <code>storage</code> and <code>bootstrap/cache</code> directories.</p>
        <ol class="list-decimal ml-6 space-y-2">
            <li>Create a MySQL database and user in Plesk, then set DB credentials in your <code>.env</code>.</li>
            <li>Set your <code>APP_URL</code>, mail settings, and Stripe/OpenAI keys in <code>.env</code>.</li>
            <li>Run the installer by executing migrations automatically on first run.</li>
        </ol>
        <div class="mt-6">
            <a href="{{ url('/') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Recheck</a>
        </div>
    </div>
</x-guest-layout>