<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Credit Packages</h2>
            <a href="{{ route('admin.packages.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">New Package</a>
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
                                <th>Credits</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($packages as $pkg)
                            <tr class="border-t">
                                <td class="py-2">{{ $pkg->name }}</td>
                                <td>{{ $pkg->credits }}</td>
                                <td>{{ $pkg->price }} {{ $pkg->currency }}</td>
                                <td>{{ $pkg->is_active ? 'Active' : 'Inactive' }}</td>
                                <td class="text-right space-x-2">
                                    <a href="{{ route('admin.packages.edit', $pkg) }}" class="text-indigo-600">Edit</a>
                                    <form method="POST" action="{{ route('admin.packages.destroy', $pkg) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600" onclick="return confirm('Delete?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $packages->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>