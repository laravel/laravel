@extends('main')
@section('title', "| $post->title")
@section('content')
	<div class="row">
		<div class="col-md-8 col-md-offset-1">
			<div class="well">
				<h1>{{ $post->title }}</h1>
				<p>{{ $post->body }}</p>
			</div>
		</div>
		@include('partials._sidebar')
	</div>
@endsection