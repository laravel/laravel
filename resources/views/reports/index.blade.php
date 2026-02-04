@extends('layouts.dashboard')

@section('title', 'Laporan Saya')

@section('content')
<div>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 mb-1">Laporan Saya</h1>
            <p class="text-slate-600">Kelola dan pantau semua laporan Anda</p>
        </div>
        <a href="{{ route('reports.create') }}" class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors text-sm font-medium">
            + Buat Laporan
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($reports->count() > 0)
        <div class="space-y-4">
            @foreach($reports as $report)
                <div class="border border-slate-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-slate-900">{{ $report->title }}</h3>
                            <p class="text-sm text-slate-600 mt-1 line-clamp-2">{{ $report->description }}</p>
                        </div>
                        <span class="px-3 py-1 rounded text-xs font-medium whitespace-nowrap
                            @if($report->status === 'open') bg-red-100 text-red-800
                            @elseif($report->status === 'process') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($report->status) }}
                        </span>
                    </div>
                    
                    @if($report->solution)
                        <div class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                            <p class="font-medium text-slate-900 mb-1">Solusi:</p>
                            <p class="text-slate-700">{{ $report->solution }}</p>
                        </div>
                    @endif
                    
                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-xs text-slate-500">{{ $report->created_at->format('d M Y H:i') }}</p>
                        <a href="{{ route('reports.show', $report) }}" class="text-sm text-slate-600 hover:text-slate-900">
                            Lihat detail →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="border border-slate-200 rounded-lg p-12 text-center bg-slate-50">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Belum ada laporan</h3>
            <p class="text-slate-600 mb-4">Buat laporan pertama untuk melacak masalah pembelajaran Anda</p>
            <a href="{{ route('reports.create') }}" class="inline-block px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 text-sm">
                Buat Laporan
            </a>
        </div>
    @endif
</div>
@endsection
