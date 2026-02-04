@extends('layouts.dashboard')

@section('title', 'Buat Laporan')

@section('content')
<div>
    <div class="mb-8">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-slate-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <h1 class="text-3xl font-bold text-slate-900 mb-1">Buat Laporan</h1>
        <p class="text-slate-600">Lapor kendala, pertanyaan, atau keluhan tentang pembelajaran</p>
    </div>

    <div class="border border-slate-200 rounded-lg p-6 max-w-2xl">
        <form action="{{ route('reports.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-slate-900 mb-2">Judul Laporan</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required 
                    placeholder="Ringkas masalah Anda..."
                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent text-sm">
                @error('title') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-900 mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="6" required
                    placeholder="Jelaskan masalah Anda secara detail..."
                    class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent text-sm resize-none">{{ old('description') }}</textarea>
                <p class="text-xs text-slate-500 mt-1">Minimum 10 karakter</p>
                @error('description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors text-sm font-medium">
                    Kirim Laporan
                </button>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
