<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="mb-4">Welcome, {{ Auth::user()->name }}. Credits: <strong>{{ Auth::user()->credits }}</strong></p>
                    <form action="{{ route('checkout') }}" method="POST" class="space-y-2">
                        @csrf
                        <label>Select package</label>
                        <select name="package_id" class="border p-2 rounded">
                            @php($packages = \App\Models\CreditPackage::where('is_active', true)->orderBy('price')->get())
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}">{{ $pkg->name }} — {{ $pkg->credits }} credits — {{ $pkg->price }} {{ $pkg->currency }}</option>
                            @endforeach
                        </select>
                        <button class="px-3 py-2 bg-indigo-600 text-white rounded">Buy with Stripe</button>
                    </form>
                    <div class="mt-6">
                        @php($latest = \App\Models\ChatThread::where('user_id', Auth::id())->latest()->first())
                        @if($latest)
                            <a href="{{ route('threads.share', $latest) }}" class="text-indigo-600">Share last chat</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
