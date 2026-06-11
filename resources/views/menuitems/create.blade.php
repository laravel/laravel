@extends('layouts.app')

@section('title', 'Add Menu Item - QuickBite')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-lg p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold">Add New Menu Item</h1>
                    <p class="text-gray-500 mt-2">Create a new dish and save it to the database.</p>
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

            <form action="{{ route('menuitems.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="name">Dish Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="price">Price</label>
                        <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price') }}" required
                               class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="restaurant_id">Restaurant</label>
                        <select id="restaurant_id" name="restaurant_id" required
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <option value="">Select restaurant</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                    {{ $restaurant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="category_id">Category</label>
                        <select id="category_id" name="category_id" required
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="mt-3 text-sm text-gray-600">Can't find a category? Add one:</div>
                        <div id="new-category-form" data-action="{{ route('categories.store') }}" class="mt-2 flex gap-2">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input name="name" placeholder="New category name" type="text" required
                                class="flex-1 rounded-2xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <input name="icon" placeholder="Icon (e.g. 🍜)" type="text" maxlength="4"
                                class="w-20 rounded-2xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <button type="button" id="new-category-submit" class="rounded-2xl bg-purple-600 text-white px-4">Add</button>
                        </div>
                        <div id="new-category-msg" class="text-sm mt-2"></div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="description">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="image">Image</label>
                    <input id="image" name="image" type="file" accept="image/*"
                           class="w-full rounded-2xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="inline-flex items-center space-x-2 rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available') ? 'checked' : '' }}>
                        <span class="text-sm">Available</span>
                    </label>
                    <label class="inline-flex items-center space-x-2 rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3">
                        <input type="checkbox" name="is_vegetarian" value="1" {{ old('is_vegetarian') ? 'checked' : '' }}>
                        <span class="text-sm">Vegetarian</span>
                    </label>
                    <label class="inline-flex items-center space-x-2 rounded-2xl border border-gray-300 bg-gray-50 px-4 py-3">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <span class="text-sm">Featured</span>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-purple-600 px-8 py-3 text-white font-semibold hover:bg-purple-700 transition">
                        Save Menu Item
                    </button>
                    <a href="{{ route('restaurants.index') }}" class="text-gray-600 hover:text-purple-600">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('new-category-form');
    const submitButton = document.getElementById('new-category-submit');
    if (!form || !submitButton) return;

    submitButton.addEventListener('click', async function (e) {
        e.preventDefault();
        const name = form.querySelector('input[name="name"]').value.trim();
        const icon = form.querySelector('input[name="icon"]').value.trim();
        const msg = document.getElementById('new-category-msg');
        if (!name) {
            msg.textContent = 'Please enter a category name.';
            msg.className = 'text-sm mt-2 text-red-600';
            return;
        }

        const action = form.dataset.action;
        const tokenEl = form.querySelector('input[name="_token"]');
        const token = tokenEl ? tokenEl.value : document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            const res = await fetch(action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ name, icon })
            });

            if (res.ok) {
                const data = await res.json();
                const select = document.getElementById('category_id');
                const opt = document.createElement('option');
                opt.value = data.category.id;
                opt.textContent = data.category.name;
                select.appendChild(opt);
                select.value = data.category.id;
                form.querySelector('input[name="name"]').value = '';
                form.querySelector('input[name="icon"]').value = '';
                msg.textContent = 'Category added.';
                msg.className = 'text-sm mt-2 text-green-600';
                setTimeout(() => { msg.textContent = ''; }, 3000);
            } else {
                const err = await res.json().catch(() => null);
                const message = err?.message || (err?.errors ? Object.values(err.errors).flat().join(' ') : 'Could not add category');
                msg.textContent = message;
                msg.className = 'text-sm mt-2 text-red-600';
            }
        } catch (error) {
            msg.textContent = 'Network error';
            msg.className = 'text-sm mt-2 text-red-600';
        }
    });
});
</script>
@endpush
