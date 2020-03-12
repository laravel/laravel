@extends('layouts/app')

@section('content')

	<e-form v-bind='@json($model)'></e-form>

@endsection
