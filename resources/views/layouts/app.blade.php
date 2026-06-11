
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'QuickBite - Food Delivery')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
     <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-gray-50">
    {{-- Navigation --}}
    <nav class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="{{ route('home') }}" class="text-2xl font-bold flex items-center">
                    <span class="text-3xl mr-2">🍔</span> QuickBite
                </a>
                
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('home') }}" class="hover:text-gray-200 transition">Home</a>
                    <a href="{{ route('restaurants.index') }}" class="hover:text-gray-200 transition">Restaurants</a>
                    
                    @auth
                        
                        <a href="{{ route('orders.my-orders') }}" class="hover:text-gray-200 transition">My Orders</a>
                    @endauth
                    
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('cart.index') }}" class="relative hover:text-gray-200 transition">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ count(session('cart', [])) }}
                        </span>
                    </a>
                    
                    @auth
                        <el-dropdown class="inline-block">
                            <button class="inline-flex w-full justify-center gap-x-1.5 rounded-md text-sm font-semibold text-white inset-ring-1 inset-ring-white/5 hover:text-gray-200">
                                <span>{{ Auth::user()->name }}</span><svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="-mr-1 size-5 text-gray-400">
                                       <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd">
                                       </svg>
                                       
                            </button>
                            <el-menu anchor="bottom end" popover class="absolute right-20 mt-2 w-48 rounded-md bg-white outline-1 -outline-offset-1 outline-white/10 transition transition-discrete [--anchor-gap:--spacing(2)] data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in">
                               <div class="py-1">
                                <a href="{{ route('orders.my-orders') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">My Orders</a>
                                <a href="{{ route('restaurants.create') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Add Restaurant</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100">Logout</button>
                                    </form>
                                </div>  
                            </el-menu>
                        </el-dropdown>
                        <p class="text-gray-300 text-xs font-semibold inset-ring-1 inset-ring-white/5">{{ Auth::user()->role }}</p>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-gray-200 transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-red-500 px-4 py-2 rounded-full hover:bg-red-600 transition">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">QuickBite</h3>
                    <p class="text-gray-400">Delicious food delivered fast to your door.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                        <li><a href="{{ route('restaurants.index') }}" class="hover:text-white">Restaurants</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Help Center</a></li>
                        <li><a href="#" class="hover:text-white">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook text-2xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter text-2xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram text-2xl"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 QuickBite. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    </script>
    @stack('scripts')
</body>
</html>