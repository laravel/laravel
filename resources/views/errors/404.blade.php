@extends('errors/layout', [
	'page' => [
		'title' => 'Page not found',
		'description' => 'Sorry, the page you are looking for could not be found.',
	],
])

@section('content')

	<h1>Page Not Found</h1>

	<p>It looks like the page you're looking for is no longer&nbsp;here.</p>

	<p><a href="/">Return to the homepage</a></p>

@endsection
