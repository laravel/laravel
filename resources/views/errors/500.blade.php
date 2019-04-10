@extends('errors/layout', [
	'page' => [
		'title' => 'Server error',
		'description' => 'Sorry, it looks like something has broken.',
	],
])

@section('content')

	<h1>Server error</h1>

	<p>Sorry, it looks like something has&nbsp;broken.</p>

	<p><a href="/">Return to the homepage</a></p>

@endsection
