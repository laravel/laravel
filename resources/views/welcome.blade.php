<x-public-layout>
    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 sm:pt-40 sm:pb-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white mb-6">
                <span class="block">{{ landing_content('hero_title_line1') }}</span>
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">
                    {{ landing_content('hero_title_line2') }}
                </span>
            </h1>
            <p class="mt-4 text-xl text-slate-300 max-w-2xl mx-auto mb-10">
                {{ landing_content('hero_subtitle') }}
            </p>
            <div class="flex justify-center gap-4">
                <a href="#pricing" class="px-8 py-3 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-semibold transition-all shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50">
                    {{ landing_content('hero_btn_primary') }}
                </a>
                <a href="#services" class="px-8 py-3 rounded-full bg-slate-800 hover:bg-slate-700 text-white font-semibold border border-slate-700 transition-all">
                    {{ landing_content('hero_btn_secondary') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div id="services" class="py-20 bg-slate-900/50 backdrop-blur-sm relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-white mb-4">{{ landing_content('services_heading') }}</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">{{ landing_content('services_subheading') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="card-hover p-8 rounded-2xl bg-slate-800/50 border border-white/5 hover:border-blue-500/30 transition-all group">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-6 group-hover:bg-blue-500/30 transition-colors">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">{{ landing_content('service1_title') }}</h3>
                    <p class="text-slate-400">{{ landing_content('service1_description') }}</p>
                </div>

                <!-- Service 2 -->
                <div class="card-hover p-8 rounded-2xl bg-slate-800/50 border border-white/5 hover:border-purple-500/30 transition-all group">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mb-6 group-hover:bg-purple-500/30 transition-colors">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">{{ landing_content('service2_title') }}</h3>
                    <p class="text-slate-400">{{ landing_content('service2_description') }}</p>
                </div>

                <!-- Service 3 -->
                <div class="card-hover p-8 rounded-2xl bg-slate-800/50 border border-white/5 hover:border-teal-500/30 transition-all group">
                    <div class="w-12 h-12 bg-teal-500/20 rounded-lg flex items-center justify-center mb-6 group-hover:bg-teal-500/30 transition-colors">
                        <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">{{ landing_content('service3_title') }}</h3>
                    <p class="text-slate-400">{{ landing_content('service3_description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Section -->
    <div id="pricing" class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-white mb-4">{{ __('Simple Pricing') }}</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">{{ __('Choose the plan that fits your needs. No hidden fees.') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Starter -->
                <div class="card-hover p-8 rounded-2xl bg-slate-800/30 border border-white/10 hover:border-white/20 transition-all">
                    <h3 class="text-lg font-semibold text-slate-300 mb-2">{{ __('Starter') }}</h3>
                    <div class="text-4xl font-bold text-white mb-6">$29<span class="text-lg text-slate-500 font-normal">/mo</span></div>
                    <ul class="space-y-4 mb-8 text-slate-400">
                        <li class="flex items-center"><svg class="w-5 h-5 text-green-400 mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 1 Website</li>
                        <li class="flex items-center"><svg class="w-5 h-5 text-green-400 mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Managed Hosting</li>
                        <li class="flex items-center"><svg class="w-5 h-5 text-green-400 mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Basic Support</li>
                    </ul>
                    <a href="#" class="block w-full py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white text-center rounded-lg transition-colors">{{ __('Choose Plan') }}</a>
                </div>

                <!-- Business -->
                <div class="card-hover p-8 rounded-2xl bg-gradient-to-b from-blue-900/20 to-slate-800/30 border border-blue-500/50 relative transform md:-translate-y-4">
                    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-blue-600 text-white px-4 py-1 rounded-full text-sm font-bold">{{ __('Popular') }}</div>
                    <h3 class="text-lg font-semibold text-blue-400 mb-2">{{ __('Business') }}</h3>
                    <div class="text-4xl font-bold text-white mb-6">$99<span class="text-lg text-slate-500 font-normal">/mo</span></div>
                    <ul class="space-y-4 mb-8 text-slate-300">
                        <li class="flex items-center"><svg class="w-5 h-5 text-blue-400 mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 5 Websites</li>
                        <li class="flex items-center"><svg class="w-5 h-5 text-blue-400 mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Priority Support</li>
                        <li class="flex items-center"><svg class="w-5 h-5 text-blue-400 mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Monthly SEO Audit</li>
                    </ul>
                    <a href="#" class="block w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white text-center rounded-lg transition-colors shadow-lg shadow-blue-500/25">{{ __('Choose Plan') }}</a>
                </div>

                <!-- Enterprise -->
                <div class="card-hover p-8 rounded-2xl bg-slate-800/30 border border-white/10 hover:border-white/20 transition-all">
                    <h3 class="text-lg font-semibold text-slate-300 mb-2">{{ __('Enterprise') }}</h3>
                    <div class="text-4xl font-bold text-white mb-6">{{ __('Custom') }}</div>
                    <ul class="space-y-4 mb-8 text-slate-400">
                        <li class="flex items-center"><svg class="w-5 h-5 text-green-400 mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Unlimited Websites</li>
                        <li class="flex items-center"><svg class="w-5 h-5 text-green-400 mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Dedicated Account Manager</li>
                        <li class="flex items-center"><svg class="w-5 h-5 text-green-400 mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Custom Development</li>
                    </ul>
                    <a href="#" class="block w-full py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white text-center rounded-lg transition-colors">{{ __('Contact Sales') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Grid Section -->
    <div id="features" class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-white mb-4">{{ __('Why Choose inClouding') }}</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">{{ __('Everything you need to build, manage, and scale your digital presence') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="card-hover p-6 rounded-xl bg-slate-800/30 border border-white/5">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('Lightning Fast') }}</h3>
                    <p class="text-slate-400 text-sm">{{ __('Blazing fast load times with optimized infrastructure and CDN integration') }}</p>
                </div>

                <!-- Feature 2 -->
                <div class="card-hover p-6 rounded-xl bg-slate-800/30 border border-white/5">
                    <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('Enterprise Security') }}</h3>
                    <p class="text-slate-400 text-sm">{{ __('SSL certificates, DDoS protection, and regular security updates included') }}</p>
                </div>

                <!-- Feature 3 -->
                <div class="card-hover p-6 rounded-xl bg-slate-800/30 border border-white/5">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('24/7 Support') }}</h3>
                    <p class="text-slate-400 text-sm">{{ __('Round-the-clock expert support via chat, email, and phone') }}</p>
                </div>

                <!-- Feature 4 -->
                <div class="card-hover p-6 rounded-xl bg-slate-800/30 border border-white/5">
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('Auto Backups') }}</h3>
                    <p class="text-slate-400 text-sm">{{ __('Daily automated backups with one-click restore functionality') }}</p>
                </div>

                <!-- Feature 5 -->
                <div class="card-hover p-6 rounded-xl bg-slate-800/30 border border-white/5">
                    <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('99.9% Uptime') }}</h3>
                    <p class="text-slate-400 text-sm">{{ __('Guaranteed uptime backed by SLA and redundant infrastructure') }}</p>
                </div>

                <!-- Feature 6 -->
                <div class="card-hover p-6 rounded-xl bg-slate-800/30 border border-white/5">
                    <div class="w-12 h-12 bg-teal-500/20 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('Scalable Resources') }}</h3>
                    <p class="text-slate-400 text-sm">{{ __('Easily scale resources up or down as your business grows') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div id="how-it-works" class="py-20 bg-slate-900/50 backdrop-blur-sm relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-white mb-4">{{ __('How It Works') }}</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">{{ __('Get started in minutes with our simple onboarding process') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-500/20 border-2 border-blue-500 text-blue-400 font-bold text-xl mb-4">1</div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('Choose Plan') }}</h3>
                    <p class="text-slate-400 text-sm">{{ __('Select the perfect plan for your needs') }}</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-500/20 border-2 border-purple-500 text-purple-400 font-bold text-xl mb-4">2</div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('Set Up Account') }}</h3>
                    <p class="text-slate-400 text-sm">{{ __('Quick 2-minute account creation process') }}</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-500/20 border-2 border-green-500 text-green-400 font-bold text-xl mb-4">3</div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('Configure Services') }}</h3>
                    <p class="text-slate-400 text-sm">{{ __('We handle the technical setup for you') }}</p>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-teal-500/20 border-2 border-teal-500 text-teal-400 font-bold text-xl mb-4">4</div>
                    <h3 class="text-lg font-bold text-white mb-2">{{ __('Go Live') }}</h3>
                    <p class="text-slate-400 text-sm">{{ __('Launch your site and start growing') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Counter Section -->
    <div class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-2">500+</div>
                    <div class="text-slate-400">{{ __('Happy Clients') }}</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-500 mb-2">99.9%</div>
                    <div class="text-slate-400">{{ __('Uptime SLA') }}</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-teal-500 mb-2">24/7</div>
                    <div class="text-slate-400">{{ __('Support') }}</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mb-2">5+</div>
                    <div class="text-slate-400">{{ __('Years Experience') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div id="about" class="py-20 bg-slate-900/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-6">{{ __('About inClouding') }}</h2>
                    <p class="text-slate-400 mb-4">
                        {{ __('We are a new generation of cloud service providers. We don\'t just sell you server space; we provide a complete digital ecosystem.') }}
                    </p>
                    <p class="text-slate-400 mb-6">
                        {{ __('By leveraging top-tier infrastructure from providers like Hostinger, we add a layer of premium management, support, and development services. You get the reliability of a giant with the personalized care of a boutique agency.') }}
                    </p>
                    <a href="#contact" class="text-blue-400 hover:text-blue-300 font-semibold flex items-center">
                        {{ __('Learn more about our story') }} <svg class="w-4 h-4 ml-2 rtl:mr-2 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl transform rotate-3 opacity-20 blur-lg"></div>
                    <div class="relative bg-slate-800 p-8 rounded-2xl border border-white/10">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 font-bold text-xl">IC</div>
                            <div class="ml-4 rtl:mr-4 rtl:ml-0">
                                <div class="text-white font-bold">inClouding Team</div>
                                <div class="text-slate-500 text-sm">Cloud Experts</div>
                            </div>
                        </div>
                        <p class="text-slate-300 italic">"{{ __('Our mission is to make the cloud accessible, secure, and powerful for every business, without the technical headache.') }}"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-white mb-4">{{ __('What Our Clients Say') }}</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">{{ __('Trusted by businesses worldwide') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="card-hover p-8 rounded-2xl bg-slate-800/30 border border-white/5">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold">JD</div>
                        <div class="ml-4 rtl:mr-4 rtl:ml-0">
                            <div class="text-white font-semibold">John Davidson</div>
                            <div class="text-slate-500 text-sm">CEO, TechCorp</div>
                        </div>
                    </div>
                    <p class="text-slate-300 italic">"{{ __('inClouding transformed our digital infrastructure. Their managed services saved us countless hours and headaches.') }}"</p>
                    <div class="flex mt-4 text-yellow-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="card-hover p-8 rounded-2xl bg-slate-800/30 border border-white/5">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-400 to-teal-500 flex items-center justify-center text-white font-bold">SM</div>
                        <div class="ml-4 rtl:mr-4 rtl:ml-0">
                            <div class="text-white font-semibold">Sarah Martinez</div>
                            <div class="text-slate-500 text-sm">Founder, GrowthLab</div>
                        </div>
                    </div>
                    <p class="text-slate-300 italic">"{{ __('The team at inClouding is exceptional. They handle everything so we can focus on our business. Highly recommended!') }}"</p>
                    <div class="flex mt-4 text-yellow-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="card-hover p-8 rounded-2xl bg-slate-800/30 border border-white/5">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold">MK</div>
                        <div class="ml-4 rtl:mr-4 rtl:ml-0">
                            <div class="text-white font-semibold">Michael Kim</div>
                            <div class="text-slate-500 text-sm">CTO, DataFlow</div>
                        </div>
                    </div>
                    <p class="text-slate-300 italic">"{{ __('Best hosting decision we ever made. The performance and support are unmatched. Our site is faster than ever!') }}"</p>
                    <div class="flex mt-4 text-yellow-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technology Stack Section -->
    <div class="py-20 bg-slate-900/50 backdrop-blur-sm relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-white mb-4">{{ __('Powered By Industry Leaders') }}</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">{{ __('We partner with the best cloud providers to deliver exceptional service') }}</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="card-hover flex items-center justify-center h-24 bg-white/5 rounded-xl border border-white/10">
                    <span class="text-2xl font-bold text-slate-300">Hostinger</span>
                </div>
                <div class="card-hover flex items-center justify-center h-24 bg-white/5 rounded-xl border border-white/10">
                    <span class="text-2xl font-bold text-slate-300">Cloudflare</span>
                </div>
                <div class="card-hover flex items-center justify-center h-24 bg-white/5 rounded-xl border border-white/10">
                    <span class="text-2xl font-bold text-slate-300">Laravel</span>
                </div>
                <div class="card-hover flex items-center justify-center h-24 bg-white/5 rounded-xl border border-white/10">
                    <span class="text-2xl font-bold text-slate-300">React</span>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="py-20 relative" x-data="{ activeQuestion: null }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-white mb-4">{{ __('Frequently Asked Questions') }}</h2>
                <p class="text-slate-400">{{ __('Everything you need to know about our services') }}</p>
            </div>

            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="card-hover bg-slate-800/30 border border-white/5 rounded-xl overflow-hidden">
                    <button @click="activeQuestion = activeQuestion === 1 ? null : 1" class="w-full p-6 text-left flex justify-between items-center">
                        <span class="text-lg font-semibold text-white">{{ __('What makes inClouding different from other hosting providers?') }}</span>
                        <svg class="w-5 h-5 text-slate-400 transition-transform" :class="{ 'rotate-180': activeQuestion === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeQuestion === 1" x-collapse class="px-6 pb-6">
                        <p class="text-slate-400">{{ __('We act as a premium layer between you and cloud providers, handling all technical complexity. You get enterprise-grade infrastructure with boutique-level service and support. No technical knowledge required.') }}</p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="card-hover bg-slate-800/30 border border-white/5 rounded-xl overflow-hidden">
                    <button @click="activeQuestion = activeQuestion === 2 ? null : 2" class="w-full p-6 text-left flex justify-between items-center">
                        <span class="text-lg font-semibold text-white">{{ __('Can I upgrade or downgrade my plan?') }}</span>
                        <svg class="w-5 h-5 text-slate-400 transition-transform" :class="{ 'rotate-180': activeQuestion === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeQuestion === 2" x-collapse class="px-6 pb-6">
                        <p class="text-slate-400">{{ __('Absolutely! You can change your plan at any time. Upgrades take effect immediately, and downgrades will be applied at your next billing cycle.') }}</p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="card-hover bg-slate-800/30 border border-white/5 rounded-xl overflow-hidden">
                    <button @click="activeQuestion = activeQuestion === 3 ? null : 3" class="w-full p-6 text-left flex justify-between items-center">
                        <span class="text-lg font-semibold text-white">{{ __('What is your uptime guarantee?') }}</span>
                        <svg class="w-5 h-5 text-slate-400 transition-transform" :class="{ 'rotate-180': activeQuestion === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeQuestion === 3" x-collapse class="px-6 pb-6">
                        <p class="text-slate-400">{{ __('We guarantee 99.9% uptime backed by our SLA. In the rare event of downtime, we provide service credits as compensation.') }}</p>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="card-hover bg-slate-800/30 border border-white/5 rounded-xl overflow-hidden">
                    <button @click="activeQuestion = activeQuestion === 4 ? null : 4" class="w-full p-6 text-left flex justify-between items-center">
                        <span class="text-lg font-semibold text-white">{{ __('Do you offer custom development services?') }}</span>
                        <svg class="w-5 h-5 text-slate-400 transition-transform" :class="{ 'rotate-180': activeQuestion === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeQuestion === 4" x-collapse class="px-6 pb-6">
                        <p class="text-slate-400">{{ __('Yes! Our team specializes in Laravel, React, and modern web technologies. Contact us for a custom quote tailored to your project needs.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter Section -->
    <div class="py-20 bg-gradient-to-r from-blue-900/20 to-purple-900/20 relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">{{ __('Stay Updated') }}</h2>
            <p class="text-slate-300 mb-8">{{ __('Subscribe to our newsletter for the latest cloud tips, tutorials, and exclusive offers') }}</p>
            
            <form class="max-w-md mx-auto flex gap-4">
                <input type="email" placeholder="{{ __('Enter your email') }}" class="flex-1 px-6 py-3 bg-slate-800/50 border border-white/10 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg transition-colors">{{ __('Subscribe') }}</button>
            </form>
        </div>
    </div>

    <!-- Contact Section -->
    <div id="contact" class="py-20 relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-white mb-4">{{ landing_content('contact_heading') }}</h2>
                <p class="text-slate-400">{{ landing_content('contact_subheading') }}</p>
            </div>

            @if(session('success'))
                <div class="mb-8 p-4 bg-green-600/20 border border-green-500/50 rounded-lg text-green-300 text-center">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-300 mb-2">{{ landing_content('contact_name_label') }}</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 bg-slate-800/50 border @error('name') border-red-500 @else border-white/10 @enderror rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">{{ landing_content('contact_email_label') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 bg-slate-800/50 border @error('email') border-red-500 @else border-white/10 @enderror rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-slate-300 mb-2">{{ landing_content('contact_subject_label') }}</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                        class="w-full px-4 py-3 bg-slate-800/50 border @error('subject') border-red-500 @else border-white/10 @enderror rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-slate-300 mb-2">{{ landing_content('contact_message_label') }}</label>
                    <textarea id="message" name="message" rows="5" required
                        class="w-full px-4 py-3 bg-slate-800/50 border @error('message') border-red-500 @else border-white/10 @enderror rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="text-center">
                    <button type="submit" class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white transition-all duration-200 bg-blue-600 rounded-full hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 focus:ring-offset-slate-900">
                        {{ landing_content('contact_submit_btn') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>
