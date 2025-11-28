<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Freight Corner - Merchant Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200">
        <div class="mx-auto max-w-6xl px-6 py-4 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded bg-orange-500"></div>
                    <span class="text-xl font-semibold">Freight Corner</span>
                </div>
                <nav class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-orange-600">Dashboard</a>
                    <a href="#" class="text-sm hover:text-orange-600">Shipments</a>
                    <a href="#" class="text-sm hover:text-orange-600">Pickups</a>
                    <a href="#" class="text-sm hover:text-orange-600">Payments</a>
                    <a href="#" class="text-sm hover:text-orange-600">Support</a>
                </nav>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-600">Welcome, User</span>
                    <a href="{{ route('home') }}" class="text-sm text-slate-700 hover:text-slate-900">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-6xl px-6 py-8 lg:px-8">
        <!-- Hero Section -->
        <header class="rounded-3xl border border-orange-100 bg-white p-6 shadow mb-8">
            <div class="flex items-center gap-3 text-orange-600 mb-4">
                <div class="h-5 w-5 rounded bg-orange-500"></div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em]">Dashboard</p>
            </div>
            <h1 class="text-3xl font-semibold text-slate-900 mb-2">Manage international and domestic deliveries</h1>
            <p class="text-sm text-slate-600 mb-4">
                Handle customs clearance, streamline global fulfillment, and scale operations with Freight Corner's worldwide network.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="#" class="inline-flex items-center gap-2 rounded-full bg-orange-500 px-4 py-2 font-semibold text-white shadow-md shadow-orange-200/80 transition hover:bg-orange-400">
                    New shipment
                </a>
                <a href="#" class="inline-flex items-center gap-2 rounded-full border border-orange-200 px-4 py-2 text-orange-700 transition hover:border-orange-300 hover:text-orange-800">
                    Track parcels
                </a>
            </div>
        </header>

        <!-- Metrics -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-8">
            @php
                $metrics = [
                    ['label' => 'Total parcels', 'value' => '1,250', 'icon' => 'package'],
                    ['label' => 'Delivered', 'value' => '1,180', 'accent' => 'text-emerald-700', 'icon' => 'package-check'],
                    ['label' => 'In transit', 'value' => '45', 'icon' => 'truck'],
                    ['label' => 'Pending', 'value' => '25', 'icon' => 'clock'],
                ];
            @endphp
            @foreach($metrics as $metric)
                <div class="rounded-3xl border border-orange-100 bg-gradient-to-b from-white to-orange-50 p-4 shadow-sm">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 mb-2">
                        @if($metric['icon'] == 'package')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        @elseif($metric['icon'] == 'package-check')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif($metric['icon'] == 'truck')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        @elseif($metric['icon'] == 'clock')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                        {{ $metric['label'] }}
                    </div>
                    <p class="text-3xl font-semibold {{ $metric['accent'] ?? 'text-slate-900' }}">{{ $metric['value'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Shipment Stats and Recent Parcels -->
        <div class="grid gap-8 lg:grid-cols-2 mb-8">
            <!-- Shipment Stats -->
            <div class="rounded-3xl border border-orange-100 bg-white p-6 shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600">Shipment stats</h3>
                    <a href="#" class="text-xs text-orange-700 transition-colors hover:text-orange-900">Customize</a>
                </div>
                <div class="space-y-3">
                    <div class="rounded-2xl border border-dashed border-orange-100 bg-orange-50 p-6 text-center text-sm text-slate-600">
                        It's empty now—your stats will show up here once shipments start moving.
                    </div>
                </div>
            </div>

            <!-- Recent Parcels -->
            <div class="rounded-3xl border border-orange-100 bg-white p-6 shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600">Recent parcels</h3>
                    <a href="#" class="text-xs text-orange-700 transition-colors hover:text-orange-900">View all</a>
                </div>
                <div class="space-y-3">
                    @php
                        $parcels = [
                            ['tracking' => 'JOY123456789', 'status' => 'Delivered', 'destination' => 'New York, USA', 'updated' => '2 hours ago'],
                            ['tracking' => 'JOY987654321', 'status' => 'In Transit', 'destination' => 'London, UK', 'updated' => '1 day ago'],
                            ['tracking' => 'JOY456789123', 'status' => 'Pending', 'destination' => 'Tokyo, Japan', 'updated' => '3 days ago'],
                        ];
                    @endphp
                    @foreach($parcels as $parcel)
                        <div class="flex items-center justify-between rounded-2xl border border-orange-100 bg-orange-50/60 p-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $parcel['tracking'] }}</p>
                                <p class="text-xs text-slate-600">{{ $parcel['destination'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200/60 bg-emerald-50 px-3 py-1 text-[0.7rem] font-semibold uppercase tracking-wide text-emerald-700">
                                    {{ $parcel['status'] }}
                                </span>
                                <p class="text-xs text-slate-500 mt-1">{{ $parcel['updated'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Getting Started -->
        <section>
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Getting started</h2>
            <p class="text-sm text-slate-600 mb-4">Learn the basics of using Freight Corner in just a few minutes.</p>
            <div class="grid gap-4 lg:grid-cols-3">
                <article class="space-y-3 rounded-3xl border border-orange-100 bg-white p-5 text-sm text-slate-700 shadow">
                    <p class="text-base font-semibold text-slate-900">Explore the dashboard</p>
                    <p>Use the navigation to manage shipments, pickups, drop-offs, and billing.</p>
                    <div class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-orange-700">
                        Use left nav
                    </div>
                </article>
                <article class="space-y-3 rounded-3xl border border-orange-100 bg-white p-5 text-sm text-slate-700 shadow">
                    <p class="text-base font-semibold text-slate-900">Create your first shipment</p>
                    <p>Book a pickup or drop off packages at one of our locations.</p>
                    <a href="#" class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-orange-700">
                        New shipment
                    </a>
                </article>
                <article class="space-y-3 rounded-3xl border border-orange-100 bg-white p-5 text-sm text-slate-700 shadow">
                    <p class="text-base font-semibold text-slate-900">Set up payments</p>
                    <p>Add payment methods and billing contacts to keep shipments moving.</p>
                    <a href="#" class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-orange-700">
                        Add payment
                    </a>
                </article>
            </div>
        </section>
    </div>
</body>
</html>