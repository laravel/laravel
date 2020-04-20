@extends('layouts/minimal')

@section('title')
	{{ $title }}
@endsection

@section('message')
	<h1>{{ $heading ?? $title }}</h1>

	<p>{{ $message }}</p>

	@if ($cta ?? true)
		<p><a href="{{ $url ?? '/' }}">{{ $cta ?? 'Return to the homepage' }}</a></p>
	@endif
@endsection
