<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">Edit Agent</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.agents.update', $agent) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block">Name</label>
                            <input name="name" class="w-full p-2 border rounded" value="{{ old('name', $agent->name) }}" required>
                        </div>
                        <div>
                            <label class="block">Slug</label>
                            <input name="slug" class="w-full p-2 border rounded" value="{{ old('slug', $agent->slug) }}" required>
                        </div>
                        <div>
                            <label class="block">Model</label>
                            <input name="model" class="w-full p-2 border rounded" value="{{ old('model', $agent->model) }}" required>
                        </div>
                        <div>
                            <label class="block">Temperature</label>
                            <input type="number" step="0.01" min="0" max="2" name="temperature" value="{{ old('temperature', $agent->temperature) }}" class="w-full p-2 border rounded">
                        </div>
                        <div>
                            <label class="block">Prompt</label>
                            <textarea name="prompt" class="w-full p-2 border rounded" rows="5">{{ old('prompt', $agent->prompt) }}</textarea>
                        </div>
                        <div>
                            <label class="block">Avatar URL</label>
                            <input name="avatar_url" class="w-full p-2 border rounded" value="{{ old('avatar_url', $agent->avatar_url) }}">
                        </div>
                        <div>
                            <label class="block">Welcome Message</label>
                            <textarea name="welcome_message" class="w-full p-2 border rounded" rows="3">{{ old('welcome_message', $agent->welcome_message) }}</textarea>
                        </div>
                        <div>
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="is_public" value="1" {{ old('is_public', $agent->is_public) ? 'checked' : '' }}>
                                <span>Public</span>
                            </label>
                        </div>
                        <div>
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
                            <a href="{{ route('admin.agents.index') }}" class="ml-2 px-4 py-2 border rounded">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>