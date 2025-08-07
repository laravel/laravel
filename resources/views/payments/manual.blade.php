<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">Manual Payment</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="mb-4">After making a bank transfer, submit the reference below for admin approval.</p>
                    <form method="POST" action="{{ route('payments.manual.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block">Package</label>
                            <select name="package_id" class="w-full border rounded p-2" required>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}">{{ $pkg->name }} — {{ $pkg->credits }} credits — {{ $pkg->price }} {{ $pkg->currency }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block">Bank Reference</label>
                            <input name="reference" class="w-full border rounded p-2" required>
                        </div>
                        <div>
                            <label class="block">Notes (optional)</label>
                            <textarea name="notes" class="w-full border rounded p-2" rows="3"></textarea>
                        </div>
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Submit</button>
                        <a href="{{ route('dashboard') }}" class="ml-2 px-4 py-2 border rounded">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>