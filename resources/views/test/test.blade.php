@extends('layouts.master')

@section('content')
<div>
This content is Ok
<input type="text" name="testInput">
</div>

<div>
This Content not parsed by the browser
  <div class="pull-right">
        {{ Form::open(['url' => 'auth/login', 'class' => 'form-inline', 'role' => 'form']) }}
            <!-- Email Form Input -->
            <div class="form-group">
                {{ Form::text('email', null, ['class' => 'form-control input-sm', 'placeholder' => trans('generics.email')]) }}
            </div>
            <!-- Password Form Input -->
            <div class="form-group">
                {{ Form::password('password', ['class' => 'form-control input-sm', 'placeholder' => trans('generics.password')]) }}
            </div>
            <div class="checkbox">
                <label class="radio-inline">
                    {{Form::checkbox('remember_me')}} {{ trans('auth.remember_me') }}
                </label>
            </div>
            {{ Form::submit(trans('auth.login'), ['class' => 'btn btn-primary btn-xs']) }}
            {{ HTML::link('auth/register', trans('auth.register'),['class' => 'btn btn-default btn-xs']) }}
        {{ Form::close() }}
    </div>
</div>
@endsection