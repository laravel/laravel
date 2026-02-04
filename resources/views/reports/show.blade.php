@extends('layouts.dashboard')

@section('title', 'Detail Laporan - ' . $report->title)

@section('content')
<div>
    <div class="mb-8">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-slate-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-1">{{ $report->title }}</h1>
                <p class="text-slate-600 text-sm">Dibuat oleh {{ $report->user->name }} pada {{ $report->created_at->format('d M Y H:i') }}</p>
            </div>
            <span class="px-3 py-1 rounded text-xs font-medium whitespace-nowrap
                @if($report->status === 'open') bg-red-100 text-red-800
                @elseif($report->status === 'process') bg-yellow-100 text-yellow-800
                @else bg-green-100 text-green-800
                @endif">
                {{ ucfirst($report->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="border border-slate-200 rounded-lg p-6">
                <h2 class="font-semibold text-slate-900 mb-3">Deskripsi Laporan</h2>
                <p class="text-slate-700 leading-relaxed">{{ $report->description }}</p>
            </div>

            <!-- Solution (jika ada) -->
            @if($report->solution)
                <div class="border border-green-200 bg-green-50 rounded-lg p-6">
                    <h2 class="font-semibold text-slate-900 mb-3">Solusi</h2>
                    <p class="text-slate-700 leading-relaxed">{{ $report->solution }}</p>
                </div>
            @endif

            <!-- Admin Update Form (hanya untuk admin) -->
            @if(auth()->user()->role === 'admin' && $report->status !== 'solved')
                <div class="border border-slate-200 rounded-lg p-6">
                    <h2 class="font-semibold text-slate-900 mb-4">Update Laporan</h2>
                    
                    <form action="{{ route('reports.update', $report) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-900 mb-2">Status</label>
                            <select id="status" name="status" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent text-sm">
                                <option value="open" {{ $report->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="process" {{ $report->status === 'process' ? 'selected' : '' }}>Process</option>
                                <option value="solved" {{ $report->status === 'solved' ? 'selected' : '' }}>Solved</option>
                            </select>
                        </div>

                        <div>
                            <label for="solution" class="block text-sm font-medium text-slate-900 mb-2">Solusi</label>
                            <textarea id="solution" name="solution" rows="4"
                                placeholder="Jelaskan solusi untuk laporan ini..."
                                class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent text-sm resize-none">{{ $report->solution }}</textarea>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors text-sm font-medium">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Report Info -->
            <div class="border border-slate-200 rounded-lg p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Informasi Laporan</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-slate-500">Status</p>
                        <p class="font-medium text-slate-900 mt-1">
                            @if($report->status === 'open')
                                <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Terbuka</span>
                            @elseif($report->status === 'process')
                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Dalam Proses</span>
                            @else
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Terselesaikan</span>
                            @endif
                        </p>
                    </div>
                    <div class="border-t border-slate-100 pt-3">
                        <p class="text-slate-500">Pembuat</p>
                        <p class="font-medium text-slate-900 mt-1">{{ $report->user->name }}</p>
                    </div>
                    <div class="border-t border-slate-100 pt-3">
                        <p class="text-slate-500">Dibuat</p>
                        <p class="font-medium text-slate-900 mt-1">{{ $report->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="border-t border-slate-100 pt-3">
                        <p class="text-slate-500">Diperbarui</p>
                        <p class="font-medium text-slate-900 mt-1">{{ $report->updated_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
