@extends('layouts.master')

@section('title', 'Home Page')

@section('content')
    <p>Welcome, {{ $name }}!</p>

    @if($loggedIn)
        <p>You are logged in.</p>
    @else
        <p>Please log in.</p>
    @endif
@endsection