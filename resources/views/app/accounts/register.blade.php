@extends('layouts/app')

@section('content')

	<register-form
		action="{{ $model['action'] }}"
	></register-form>

@endsection
