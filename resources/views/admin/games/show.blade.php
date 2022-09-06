@extends('layouts.master')

@section('content')
    <div class="mt-4">
        <a href="{{ route('games.edit', $game->id) }}" class="btn btn-info">Edit</a>
        <a href="{{ route('games.index') }}" class="btn btn-default">Back</a>
    </div>
    <hr>
    <div class="row">

        <!-- Border Left Utilities -->
        <div class="col-lg-12">

            <div class="card mb-4 py-3 border-left-primary">
                <div class="card-body">
                    <div class="container mt-4">
                        <div>
                            Name: {{ $game->title }}
                        </div>
                        <div class="row">
                        <div col="col-md-8">
                            <div class="media">
                                <div class="media-body">
                                    <iframe src="{{ url('https://www.youtube.com/embed/5Peo-ivmupE') }}" width="560" height="315" frameborder="0" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                        <div col="col-md-4" style="margin-left: 33px;">
                            <img src="{{asset('assets/img/'.$game->image)}}" alt="" style="width: 488px;max-width: 488px;">
                        </div>
                        </div>
                        <div>
                            Decription: {{ $game->description }}
                        </div>
                    
                        <div>
                            {{-- Decription: {{ $game->image }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
