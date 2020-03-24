@extends('layouts/base')

@section('app')
	<main class="flex items-center min-h-screen p-3 text-center">
		<div class="e-copy m-auto max-w-copy">
			@yield('content')
		</div>
	</main>
@endsection
