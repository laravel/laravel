@extends('layouts/app')

@section('content')

	<login-form
		action="{{ $model['action'] }}"
	></login-form>

@endsection
