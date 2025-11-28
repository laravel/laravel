<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Freight Corner - International Shipping Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-900">
    <!-- Header -->
    <header class="bg-orange-500 px-6 py-3 text-center">
        <div class="mx-auto max-w-4xl flex items-center justify-center gap-3 text-sm">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white">
                Live
            </span>
            <span class="text-white">
                New express international routes now available to Asia and Europe.
                <a href="#" class="ml-2 underline hover:no-underline">Learn more</a>
            </span>
        </div>
    </header>

    <header class="border-b border-slate-200">
        <div class="mx-auto max-w-6xl px-6 py-4 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded bg-orange-500"></div>
                    <span class="text-xl font-semibold">Freight Corner</span>
                </div>
                <nav class="hidden md:flex items-center gap-6">
                    <a href="#services" class="text-sm hover:text-orange-600">Services</a>
                    <a href="#about" class="text-sm hover:text-orange-600">About</a>
                    <a href="#contact" class="text-sm hover:text-orange-600">Contact</a>
                </nav>
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm text-slate-700 hover:text-slate-900">Log in</a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600">
                        Get started
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="mx-auto max-w-6xl px-6 py-24 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
            <div class="space-y-8">
                <div class="space-y-4">
                    <p class="text-sm font-medium uppercase tracking-wider text-orange-600">
                        Global Courier & Logistics
                    </p>
                    <h1 class="text-4xl font-bold leading-tight text-slate-900 sm:text-5xl lg:text-6xl">
                        Connecting the world through reliable shipping.
                    </h1>
                    <p class="text-lg text-slate-600 max-w-xl">
                        Fast, secure, and affordable international shipping solutions for businesses and individuals worldwide.
                    </p>
                </div>
                <div class="flex flex-col gap-4 sm:flex-row">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-8 py-4 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600">
                        Get Started
                    </a>
                    <a href="#services" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-8 py-4 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                        Learn More
                    </a>
                </div>
            </div>
            <div class="relative">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg">
                    <!-- Placeholder for image or component -->
                    <div class="h-64 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg flex items-center justify-center">
                        <span class="text-orange-600 font-semibold">Booking Interface</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section id="services" class="bg-slate-50 py-20">
        <div class="mx-auto max-w-6xl px-6 lg:px-8">
            <div class="space-y-12">
                <div class="text-center space-y-4">
                    <p class="text-sm font-medium uppercase tracking-wider text-orange-600">Our Services</p>
                    <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">
                        Comprehensive courier and freight solutions.
                    </h2>
                    <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                        From small packages to large freight, we handle all your international shipping needs with reliability and care.
                    </p>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @php
                        $services = [
                            ['title' => 'Express Courier', 'description' => 'Time-critical international courier and express delivery services.', 'stat' => 'Express'],
                            ['title' => 'Air Freight', 'description' => 'Fast and reliable air cargo transportation and consolidated air freight services.', 'stat' => 'Air freight'],
                            ['title' => 'Ocean Freight', 'description' => 'Full container load (FCL) and less than container load (LCL) sea shipping services.', 'stat' => 'Sea freight'],
                            ['title' => 'Customs Clearance', 'description' => 'Licensed customs brokerage and regulatory compliance services.', 'stat' => 'Customs'],
                            ['title' => 'Road Freight', 'description' => 'Domestic and international road transportation and cross-border trucking.', 'stat' => 'Road freight'],
                            ['title' => 'Warehousing', 'description' => 'Secure storage facilities and distribution center operations.', 'stat' => 'Warehousing'],
                        ];
                    @endphp
                    @foreach($services as $service)
                        <article class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="space-y-2">
                                <h3 class="text-xl font-semibold text-slate-900">{{ $service['title'] }}</h3>
                                <p class="text-slate-600">{{ $service['description'] }}</p>
                            </div>
                            <span class="inline-flex w-fit rounded-lg border border-orange-200 bg-orange-50 px-3 py-1 text-xs font-medium uppercase tracking-wider text-orange-700">
                                {{ $service['stat'] }}
                            </span>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Process -->
    <section class="py-20">
        <div class="mx-auto max-w-6xl px-6 lg:px-8">
            <div class="space-y-12">
                <div class="text-center space-y-4">
                    <p class="text-sm font-medium uppercase tracking-wider text-orange-600">How it works</p>
                    <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">
                        Getting started with our services is easy.
                    </h2>
                    <p class="text-lg text-slate-600 max-w-3xl mx-auto">
                        Follow these simple steps to ship with confidence.
                    </p>
                </div>
                <div class="grid gap-8 md:grid-cols-3">
                    @php
                        $steps = [
                            ['step' => '01', 'title' => 'Get international quote', 'detail' => 'Contact us with shipment details and destination for estimated rates.'],
                            ['step' => '02', 'title' => 'Prepare documentation', 'detail' => 'Complete customs forms and provide all required shipping documents.'],
                            ['step' => '03', 'title' => 'Track globally', 'detail' => 'Monitor your international shipment with real-time updates.'],
                        ];
                    @endphp
                    @foreach($steps as $step)
                        <article class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-6">
                            <span class="text-sm font-medium uppercase tracking-wider text-orange-600">{{ $step['step'] }}</span>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $step['title'] }}</h3>
                            <p class="text-slate-600">{{ $step['detail'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-6xl px-6 lg:px-8">
            <div class="space-y-12">
                <div class="text-center space-y-4">
                    <p class="text-sm font-medium uppercase tracking-wider text-orange-600">Testimonials</p>
                    <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">
                        What our customers say.
                    </h2>
                </div>
                <div class="grid gap-8 md:grid-cols-2">
                    @php
                        $testimonials = [
                            ['quote' => 'Great freight service for our manufacturing supplies. Professional and efficient.', 'name' => 'Sara Ahmed', 'role' => 'Operations Manager, Factory', 'metric' => 'Reliable service'],
                            ['quote' => 'The tracking system gives us complete peace of mind for our shipments.', 'name' => 'Mohammed Ali', 'role' => 'Logistics Coordinator, Retail Chain', 'metric' => 'Peace of mind'],
                        ];
                    @endphp
                    @foreach($testimonials as $testimonial)
                        <article class="rounded-lg border border-slate-200 bg-white p-6">
                            <blockquote class="text-lg text-slate-900 mb-4">
                                "{{ $testimonial['quote'] }}"
                            </blockquote>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $testimonial['name'] }}</p>
                                    <p class="text-sm text-slate-600">{{ $testimonial['role'] }}</p>
                                </div>
                                <span class="text-xs font-medium uppercase tracking-wider text-orange-600">
                                    {{ $testimonial['metric'] }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="text-center space-y-6">
                <div class="space-y-3">
                    <p class="text-sm font-medium uppercase tracking-wider text-orange-600">Get Started</p>
                    <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">
                        Ready to ship with us?
                    </h2>
                    <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                        Contact us today for a free quote and experience reliable international shipping services.
                    </p>
                </div>
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('consult') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-8 py-4 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600">
                        Get a Quote
                    </a>
                    <a href="#services" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-8 py-4 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-12">
        <div class="mx-auto max-w-6xl px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-4">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="h-8 w-8 rounded bg-orange-500"></div>
                        <span class="text-xl font-semibold">Freight Corner</span>
                    </div>
                    <p class="text-slate-400">Connecting the world through reliable shipping.</p>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Services</h3>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#" class="hover:text-white">Express Courier</a></li>
                        <li><a href="#" class="hover:text-white">Air Freight</a></li>
                        <li><a href="#" class="hover:text-white">Ocean Freight</a></li>
                        <li><a href="#" class="hover:text-white">Customs Clearance</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Company</h3>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#" class="hover:text-white">About</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                        <li><a href="#" class="hover:text-white">Careers</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Support</h3>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#" class="hover:text-white">Help Center</a></li>
                        <li><a href="#" class="hover:text-white">Track Shipment</a></li>
                        <li><a href="#" class="hover:text-white">API Docs</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-8 pt-8 text-center text-slate-400">
                <p>&copy; 2025 Freight Corner. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>