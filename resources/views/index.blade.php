@extends('layouts.master')

@section('content')
{!! Form::open(['enctype' => 'multipart/form-data']) !!}
<h3>Send without selecting any file</h3>

@if($errors->any())
<ul>
	@foreach($errors->all() as $error)
	<li>{{ $error }}</li>
	@endforeach
</ul>
@endif

<input type="file" name="myFile">
<input type="submit" name="submit" value="Send"/>
{!! Form::close() !!}
@stop