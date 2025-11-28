<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Your Shipment - Freight Corner</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200">
        <div class="mx-auto max-w-6xl px-6 py-4 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded bg-orange-500"></div>
                    <span class="text-xl font-semibold">Freight Corner</span>
                </div>
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-sm hover:text-orange-600">Home</a>
                    <a href="#services" class="text-sm hover:text-orange-600">Services</a>
                    <a href="{{ route('consult') }}" class="text-sm hover:text-orange-600">Consult</a>
                </nav>
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm text-slate-700 hover:text-slate-900">Log in</a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-4xl px-6 py-16 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-slate-900 mb-4">Track Your Shipment</h1>
            <p class="text-lg text-slate-600">Enter your tracking number to get real-time updates on your international shipment.</p>
        </div>

        <!-- Tracking Form -->
        <div class="bg-white rounded-3xl border border-orange-100 p-8 shadow mb-8">
            <form class="space-y-6">
                <div>
                    <label for="tracking" class="block text-sm font-medium text-slate-700 mb-2">Tracking Number</label>
                    <input
                        type="text"
                        id="tracking"
                        name="tracking"
                        placeholder="Enter your tracking number"
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    />
                </div>
                <button
                    type="submit"
                    class="w-full bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition"
                >
                    Track Shipment
                </button>
            </form>
        </div>

        <!-- Tracking Result (Placeholder) -->
        <div class="bg-white rounded-3xl border border-orange-100 p-8 shadow">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-slate-900 mb-2">Enter a tracking number</h3>
                <p class="text-slate-600">Your shipment details and tracking history will appear here.</p>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-12 text-center">
            <h2 class="text-2xl font-bold text-slate-900 mb-4">Need Help?</h2>
            <p class="text-slate-600 mb-6">Can't find your tracking number or need assistance with your shipment?</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('consult') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600">
                    Get Support
                </a>
                <a href="mailto:support@freightcorner.com.pk" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                    Email Support
                </a>
            </div>
        </div>
    </div>
</body>
</html>