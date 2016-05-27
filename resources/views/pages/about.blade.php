@extends('main')
@section('title', ' | About')
@section('content')
        <div class="row">
            <div class="col-md-8 col-md-offset-1 well">
                <h4>About Me</h1>
	                <p>Name:<strong>{{ $data['fullname'] }}</strong></p>
	                <p>E-mail:<strong>{{ $data['email'] }}</strong></p>
            </div>
            @include('partials._sidebar')
        </div>
@endsection