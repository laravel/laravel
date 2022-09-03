@extends('layouts.master')

@section('content')
    <div class="row">
        @foreach ($games as $game)
            <div class="col-lg-3 mb-2">
                <div class="card" style="width: 18rem;">
                    {{-- <div class="card-img-top bg-register-image"></div> --}}

                    <img class="card-img-top" src="{{ asset('assets/img/logo.png') }}" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">{{ $game->title }}</h5>
                        <p class="card-text">{!! $game->description !!}</p>
                    </div>
                    <div class="card-footer text-muted">
                        Created By: {{ $game->user->name }}
                    </div>
                </div>
            </div>
        @endforeach


    </div>
@endsection
