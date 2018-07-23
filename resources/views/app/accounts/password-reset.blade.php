@extends('layouts/app')

@section('content')

	<password-reset-form
		action="{{ $model['action'] }}"
		token="{{ $model['token'] }}"
	></password-reset-form>

@endsection
