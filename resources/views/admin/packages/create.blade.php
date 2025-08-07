<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">New Package</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.packages.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block">Name</label>
                            <input name="name" class="w-full p-2 border rounded" required>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block">Credits</label>
                                <input type="number" min="1" name="credits" class="w-full p-2 border rounded" required>
                            </div>
                            <div>
                                <label class="block">Price</label>
                                <input type="number" step="0.01" min="0.5" name="price" class="w-full p-2 border rounded" required>
                            </div>
                            <div>
                                <label class="block">Currency</label>
                                <input name="currency" value="USD" class="w-full p-2 border rounded" required>
                            </div>
                        </div>
                        <div>
                            <label class="block">Description</label>
                            <textarea name="description" class="w-full p-2 border rounded" rows="3"></textarea>
                        </div>
                        <div>
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="is_active" value="1" checked>
                                <span>Active</span>
                            </label>
                        </div>
                        <div>
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Create</button>
                            <a href="{{ route('admin.packages.index') }}" class="ml-2 px-4 py-2 border rounded">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>