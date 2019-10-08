@extends('layouts/base')

@section('head')
	@include('layouts/partials/meta', [
		'stylesheet' => 'https://cdn.jsdelivr.net/npm/tailwindcss@1.0.4/dist/tailwind.min.css',
		'mix_stylesheet' => false,
	])
@endsection

@section('app')
	<div class="container mx-auto py-12 px-5">
		<h1 class="text-4xl xl:text-5xl tracking-tighter text-gray-900 font-bold">Templates</h1>

		<div class="mt-3">
			<ul class="-mt-2">
				@foreach ($templates as $template)
					<li class="mt-2"><a href="/{{ $template }}">{{ $template }}</a></li>
				@endforeach
			</ul>
		</div>
	</div>
@endsection
