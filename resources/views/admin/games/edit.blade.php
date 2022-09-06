@extends('layouts.master')


@section('content')
    
    <div class="card">
        <div class="card-body">
            {!! Form::model($game, ['method' => 'PATCH','enctype'=>'multipart/form-data' ,'route' => ['games.update', $game->id]]) !!}
                <div class="mb-3">
                    <div class="form-group">
                        <strong>Title:</strong>
                        {!! Form::text('title', null, array('placeholder' => 'Title','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-group">
                        <strong>Video URL:</strong>
                        {!! Form::text('video_url', null, array('placeholder' => 'Video Url','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" >
                    <img src="{{asset('assets/img/'.$game->image)}}" width="70px;" height="70px;" alt="pic">
                </div>
                <div class="mb-3">
                    <div class="form-group">
                        <strong>Description:</strong>
                        {!! Form::text('description', null, array('placeholder' => 'Description','class' => 'form-control')) !!}

                    </div>
                </div>
               
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('games.index') }}" class="btn btn-default">Back</a>
            
            {!! Form::close() !!}

        </div>
    </div>

@endsection
