@extends('layouts.dashboard')

@section('title', 'Chat - ' . $user->name)

@section('content')
<div class="flex flex-col h-screen">
    <!-- Header -->
    <div class="mb-6 pb-6 border-b border-slate-200">
        <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-slate-900 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-200 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h1>
                <p class="text-sm text-slate-500">{{ ucfirst($user->role) }}</p>
            </div>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="flex-1 mb-6 overflow-y-auto bg-slate-50 p-6 rounded-lg space-y-4">
        @if($messages->count() > 0)
            @foreach($messages as $message)
                <div class="flex {{ $message->sender_id === $authUser->id ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs px-4 py-2 rounded-lg {{ $message->sender_id === $authUser->id ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-900' }}">
                        <p class="text-sm">{{ $message->body }}</p>
                        <p class="text-xs {{ $message->sender_id === $authUser->id ? 'text-slate-300' : 'text-slate-500' }} mt-1">
                            {{ $message->created_at->format('H:i') }}
                        </p>
                    </div>
                </div>
            @endforeach
        @else
            <div class="flex items-center justify-center h-full text-center">
                <div>
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-slate-500 text-sm">Belum ada pesan</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Message Form -->
    <form action="{{ route('messages.store', $user) }}" method="POST" class="space-y-4 border-t border-slate-200 pt-6">
        @csrf
        <div>
            <textarea name="body" placeholder="Tulis pesan..." rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent resize-none text-sm" required></textarea>
            @error('body') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="w-full bg-slate-900 text-white px-4 py-2 rounded-lg font-medium hover:bg-slate-800 transition text-sm">
            Kirim Pesan
        </button>
    </form>
</div>

<style>
    .flex-1 {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
    }
</style>
@endsection
