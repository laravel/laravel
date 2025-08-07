<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('admin.agents.index') }}" class="block p-4 border rounded">Manage Agents</a>
                        <a href="#" class="block p-4 border rounded">Users</a>
                        <a href="#" class="block p-4 border rounded">Payments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>