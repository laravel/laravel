@extends('layouts.app')

@section('title', 'Restaurants - QuickBite')

@section('content')
    <div class="gradient-bg text-white py-16">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">All Restaurants</h1>
            <p class="text-lg">Discover amazing restaurants near you</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        {{-- Search and Filters --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form action="{{ route('restaurants.index') }}" method="GET" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search restaurants or dishes..." 
                       class="flex-1 px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:outline-none">
                <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-search"></i> Search
                </button>
                <button type="button" id="addRestaurantBtn" class="bg-blue-400 text-white px-4 py-3 rounded-lg hover:bg-purple-500 transition">
                    <a href="{{ route('restaurants.create') }}" class="text-white">
                        <i class="fa fa-cutlery" aria-hidden="true"></i> Add Restaurant
                    </a>
                </button>
            </form>
        </div>

        {{-- Restaurant Grid --}}
        @if($restaurants->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($restaurants as $restaurant)
                    <a href="{{ route('restaurants.show', $restaurant) }}" 
                       class="bg-white rounded-xl shadow-md overflow-hidden hover-scale">
                        <div class="w-full h-48 rounded-lg overflow-hidden">
                            @if($restaurant->image)
                                <img
                                     src="{{ asset('storage/' . $restaurant->image) }}"
                                     alt="{{ $restaurant->name }}"
                                     class="w-full h-full object-cover"
                              >
                            @else
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center text-4xl">
                                    🍽️
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2">{{ $restaurant->name }}</h3>
                            <p class="text-gray-600 mb-3">{{ Str::limit($restaurant->description, 60) }}</p>
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                                <span><i class="fas fa-star text-yellow-400"></i> {{ $restaurant->rating }}</span>
                                <span><i class="fas fa-clock"></i> {{ $restaurant->delivery_time }} min</span>
                                <span><i class="fas fa-dollar-sign"></i> ${{ number_format($restaurant->delivery_fee, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">
                                    <i class="fas fa-utensils"></i> {{ $restaurant->menu_items_count }} items
                                </span>
                                <span class="text-purple-600 font-semibold">View Menu →</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $restaurants->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-6xl mb-4">🔍</div>
                <h2 class="text-2xl font-bold mb-2">No restaurants found</h2>
                <p class="text-gray-600">Try adjusting your search criteria</p>
            </div>
        @endif
    </div>
@endsection