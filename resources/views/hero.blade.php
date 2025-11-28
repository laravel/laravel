@extends('layouts.app')
@section('title', 'Fast, Reliable Freight — FreightCorner')
@section('content')
<section class="py-16 bg-slate-50">
  <div class="grid md:grid-cols-2 gap-8 items-center">
    <div>
      <h1 class="text-4xl font-bold tracking-tight text-slate-900">Move freight with confidence.</h1>
      <p class="mt-4 text-slate-700 text-lg">Instant quotes, seamless pickups, and live tracking — all in one simple portal.</p>
      <div class="mt-6 space-x-4">
        <a href="/auth/login" class="inline-block bg-orange-600 hover:bg-orange-700 text-white px-5 py-3 rounded">Sign in</a>
        <a href="/consult" class="inline-block border border-slate-300 px-5 py-3 rounded text-slate-800">Talk to an expert</a>
      </div>
      <div class="mt-8 text-sm text-slate-600">Trusted by merchants across Pakistan.</div>
    </div>
    <div class="bg-white rounded border p-6">
      <div class="aspect-[16/10] bg-slate-100 rounded overflow-hidden">
        <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Parcel delivery illustration" class="w-full h-full object-cover" />
      </div>
      <div class="mt-4 grid grid-cols-3 gap-4 text-center">
        <div class="card">
          <div class="text-2xl font-semibold">99.9%</div>
          <div class="text-sm text-slate-600">On-time pickups</div>
        </div>
        <div class="card">
          <div class="text-2xl font-semibold">24/7</div>
          <div class="text-sm text-slate-600">Support</div>
        </div>
        <div class="card">
          <div class="text-2xl font-semibold">Worldwide</div>
          <div class="text-sm text-slate-600">Coverage</div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection