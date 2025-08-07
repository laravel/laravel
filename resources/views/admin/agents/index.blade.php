<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Agents</h2>
            <a href="{{ route('admin.agents.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">New Agent</a>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="w-full text-left">
                        <thead>
                            <tr>
                                <th class="py-2">Name</th>
                                <th>Slug</th>
                                <th>Model</th>
                                <th>Public</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($agents as $agent)
                            <tr class="border-t">
                                <td class="py-2">{{ $agent->name }}</td>
                                <td>{{ $agent->slug }}</td>
                                <td>{{ $agent->model }}</td>
                                <td>{{ $agent->is_public ? 'Yes' : 'No' }}</td>
                                <td class="text-right space-x-2">
                                    <a href="{{ route('admin.agents.edit', $agent) }}" class="text-indigo-600">Edit</a>
                                    <form action="{{ route('admin.agents.destroy', $agent) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600" onclick="return confirm('Delete?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $agents->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>