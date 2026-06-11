@extends('layouts.app')

@section('title', 'Add Restaurant - QuickBite')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-lg p-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold">Add New Restaurant</h1>
                    <p class="text-gray-500 mt-2">Create a restaurant entry and upload it to the database.</p>
                </div>
                <a href="{{ route('restaurants.index') }}" class="text-purple-600 hover:underline">Back to restaurants</a>
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-red-700">
                    <p class="font-semibold">Please fix the errors below:</p>
                    <ul class="list-disc pl-5 mt-2 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('restaurants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <input id="address" name="address" type="text" value="{{ old('address') }}"
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>
                    <div>
                        <label for="delivery_time" class="block text-sm font-medium text-gray-700 mb-2">Delivery Time (min)</label>
                        <input id="delivery_time" name="delivery_time" type="number" min="0" value="{{ old('delivery_time') }}" required
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-2">Delivery Fee</label>
                        <input id="delivery_fee" name="delivery_fee" type="number" step="0.01" min="0" value="{{ old('delivery_fee') }}" required
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>
                    <div>
                        <label for="minimum_order" class="block text-sm font-medium text-gray-700 mb-2">Minimum Order</label>
                        <input id="minimum_order" name="minimum_order" type="number" step="0.01" min="0" value="{{ old('minimum_order') }}" required
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <input id="rating" name="rating" type="number" step="0.1" min="0" max="5" value="{{ old('rating', 4.0) }}" required
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Image</label>
                        <input id="image" name="image" type="file" accept="image/*"
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="inline-flex items-center space-x-2 rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                        <span class="text-sm">Active</span>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-purple-600 px-8 py-3 text-white font-semibold hover:bg-purple-700 transition">
                        Save Restaurant
                    </button>
                    <a href="{{ route('restaurants.index') }}" class="text-gray-600 hover:text-purple-600">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
