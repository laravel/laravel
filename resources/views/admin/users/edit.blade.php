@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        @include('pages.header')
        <div class="col-md-8">
            <div class="card">
            @if ($errors->any())
                     <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
              @endif
                    <div class="card-body">
                    <form method="POST" action="{{ URL::to('/users-update/'.$users->id)}}">
                        @csrf
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">Tên người dùng</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $users->name }}" required autocomplete="name" >
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $users->email }}" required autocomplete="email">

                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Role_Id</label>
                            <div class="col-md-6">
                            <select class="form-control"name ="role_id">
                               @foreach($role as $role_list)
                                    <option {{ $role_list->id == $users->role_id ? 'selected': ''}}
                                     value="{{$role_list->id }}">{{$role_list->name}}</option>
                               @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" value="{{ $users->password }}"  name="password" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Edit User
                                </button>
                            </div>
                        </div>
                    </form>
        </div>
</div>
</div>
@endsection