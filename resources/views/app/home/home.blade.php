@extends('layouts/app')

@section('content')

	<h1 class="h1">test <a href="#">h1</a></h1>
	<h1 class="h2">test <a href="#">h2</a></h1>
	<h1 class="h3">test <a href="#">h3</a></h1>
	<h1 class="h4">test <a href="#">h4</a></h1>
	<h1 class="h5">test <a href="#">h5</a></h1>
	<h1 class="h6 w-23/24 tablet:w-12/24 leading-xs">test <a href="#">h6</a> <a href="#" class="text-inherit">h6</a></h1>

	{{-- <div class="overlay">overlay</div> --}}

	<e-button class="button" text="test"></e-button>
	<e-button class="button" text="test" disabled></e-button>

	<img src="//placehold.it/100" alt="">

	<e-label text="test"></e-label>

@endsection
