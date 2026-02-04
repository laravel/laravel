@extends('layouts.dashboard')

@section('title', 'Materi Pembelajaran')

@section('content')
<div>
    <h1 class="text-3xl font-bold mb-1">Materi Pembelajaran</h1>
    <p class="text-slate-600 mb-8">Akses materi pembelajaran dari guru Anda</p>

    @if($materials->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($materials as $material)
                <div class="border border-slate-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="w-full h-24 bg-slate-100 rounded-md mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"/>
                        </svg>
                    </div>
                    
                    <h3 class="font-semibold text-slate-900 line-clamp-2 mb-2">{{ $material->title }}</h3>
                    @if($material->description)
                        <p class="text-sm text-slate-600 line-clamp-2 mb-3">{{ $material->description }}</p>
                    @endif
                    
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-4">
                        <span>{{ $material->created_at->format('d M Y') }}</span>
                        <span>{{ $material->user->name ?? 'Guru' }}</span>
                    </div>

                    <a href="{{ route('siswa.materials.download', $material) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm rounded-lg hover:bg-slate-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download
                    </a>
                </div>
            @endforeach
        </div>

        @if($materials->hasPages())
            <div class="mt-8">
                {{ $materials->links() }}
            </div>
        @endif
    @else
        <div class="border border-slate-200 rounded-lg p-12 text-center bg-slate-50">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Belum ada materi</h3>
            <p class="text-slate-600">Guru akan membagikan materi pembelajaran di sini</p>
        </div>
    @endif
</div>
@endsection
