@extends('layouts/base')

@section('app')
	<main class="flex items-center h-full m-auto max-w-copy p-3 text-center">
		<div class="e-copy">
			@yield('content')
		</div>
	</main>
@endsection
