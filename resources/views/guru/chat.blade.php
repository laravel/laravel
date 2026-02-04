@extends('layouts.dashboard')

@section('title', 'Chat - Guru')

@section('content')
<script>
    window.location.href = "{{ route('messages.index') }}";
</script>

<div class="text-center py-12">
    <p class="text-slate-600">Redirecting to messages...</p>
</div>
@endsection