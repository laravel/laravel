@extends('layouts.dashboard')

@section('title', 'Chat - Pesan')

@section('content')
<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-1">Pesan</h1>
        <p class="text-slate-600">Chat dengan guru atau siswa</p>
    </div>

    @if($chatUsers->count() > 0)
        <div class="border border-slate-200 rounded-lg overflow-hidden">
            <div class="divide-y divide-slate-100">
                @foreach($chatUsers as $chatUser)
                    <a href="{{ route('messages.show', $chatUser) }}" class="block px-6 py-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-900">{{ $chatUser->name }}</h3>
                                    <p class="text-sm text-slate-500">{{ ucfirst($chatUser->role) }}</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="border border-slate-200 rounded-lg p-12 text-center bg-slate-50">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Belum ada percakapan</h3>
            <p class="text-slate-600">Mulai chat dengan guru atau siswa</p>
        </div>
    @endif
</div>
@endsection
