<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>International Shipping Consultation - Freight Corner</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-50 to-orange-50 min-h-screen">
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
                    <a href="#contact" class="text-sm hover:text-orange-600">Contact</a>
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

    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-4 py-2 text-sm font-semibold text-orange-700 mb-6">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Expert Consultation
            </div>
            <h1 class="text-4xl font-bold text-slate-900 sm:text-5xl mb-4">
                International Shipping Consultation
            </h1>
            <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                Connect with Freight Corner specialists for personalized international courier,
                air freight, and sea freight solutions tailored to your global shipping needs.
            </p>
        </div>

        <!-- Contact Methods -->
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4 mb-16">
            @php
                $methods = [
                    ['icon' => '<svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>', 'title' => 'Live Chat', 'detail' => 'Instant support for urgent shipping inquiries', 'contact' => 'Available on website', 'availability' => 'Mon-Sun 8AM-8PM PKT'],
                    ['icon' => '<svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>', 'title' => 'Dedicated Account Manager', 'detail' => 'Personal relationship manager for high-volume shippers', 'contact' => 'Request via email', 'availability' => 'Custom scheduling'],
                    ['icon' => '<svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>', 'title' => 'Phone Support', 'detail' => 'Direct line to our international shipping experts', 'contact' => '+92 300 123 4567', 'availability' => 'Mon-Fri 9AM-6PM PKT'],
                    ['icon' => '<svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>', 'title' => 'Email Consultation', 'detail' => 'Detailed consultation requests and complex inquiries', 'contact' => 'consult@freightcorner.com.pk', 'availability' => 'Response within 24 hours'],
                ];
            @endphp
            @foreach($methods as $method)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        {!! $method['icon'] !!}
                        <h3 class="font-semibold text-slate-900">{{ $method['title'] }}</h3>
                    </div>
                    <p class="text-sm text-slate-600 mb-4">{{ $method['detail'] }}</p>
                    <p class="font-medium text-slate-900 mb-1">{{ $method['contact'] }}</p>
                    <p class="text-xs text-slate-500">{{ $method['availability'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Services We Consult On -->
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm mb-16">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Services We Provide Consultation For</h2>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                @php
                    $services = [
                        'Express Courier Services',
                        'Air Freight Solutions',
                        'Sea Freight & Consolidation',
                        'Customs Clearance & Documentation',
                        'Warehousing & Distribution',
                        'Project Cargo & Specialized Shipping',
                        'E-commerce Integrations',
                        'Insurance & Risk Management',
                    ];
                @endphp
                @foreach($services as $service)
                    <div class="flex items-center gap-3">
                        <div class="h-2 w-2 rounded-full bg-orange-500"></div>
                        <span class="text-slate-700">{{ $service }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Consultation Process -->
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm mb-16">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Our Consultation Process</h2>
            <div class="grid gap-8 md:grid-cols-3">
                <div class="text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 mb-4">
                        <span class="text-lg font-bold text-orange-600">1</span>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2">Initial Assessment</h3>
                    <p class="text-sm text-slate-600">
                        We analyze your shipping volume, destinations, and specific requirements to understand your needs.
                    </p>
                </div>
                <div class="text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 mb-4">
                        <span class="text-lg font-bold text-orange-600">2</span>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2">Customized Solution</h3>
                    <p class="text-sm text-slate-600">
                        Our experts design a tailored international shipping solution with competitive rates and optimal routes.
                    </p>
                </div>
                <div class="text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 mb-4">
                        <span class="text-lg font-bold text-orange-600">3</span>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2">Implementation & Support</h3>
                    <p class="text-sm text-slate-600">
                        We handle setup, training, and provide ongoing support for seamless international shipping operations.
                    </p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 p-8 text-center text-white shadow-lg">
            <h2 class="text-3xl font-bold mb-4">Ready to Ship Globally?</h2>
            <p class="text-orange-100 mb-8 max-w-2xl mx-auto">
                Get started with a free consultation and discover how Freight Corner can optimize your international shipping operations.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="mailto:consult@freightcorner.com.pk" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-6 py-3 font-semibold text-orange-600 hover:bg-orange-50 transition-colors">
                    Email Consultation
                </a>
                <a href="tel:+923001234567" class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/20 bg-white/10 px-6 py-3 font-semibold text-white hover:bg-white/20 transition-colors">
                    Call Now
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="text-center mt-16">
            <p class="text-slate-600">
                Freight Corner serves businesses worldwide with comprehensive international shipping solutions.
                <a href="{{ route('home') }}" class="text-orange-600 hover:text-orange-700 font-medium ml-1">
                    Learn more about our services →
                </a>
            </p>
        </div>
    </div>
</body>
</html>