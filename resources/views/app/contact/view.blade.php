@extends('layouts/app')

@section('content')

	<e-form
		action="{{ $model['action'] }}"
		:fields="{{ json_encode($model['fields'] ?? []) }}"
	></e-form>

@endsection
