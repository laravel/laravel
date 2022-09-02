@extends('layouts.master')

@section('content')
    <div class="mt-4">
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info">Edit</a>
        <a href="{{ route('users.index') }}" class="btn btn-default">Back</a>
    </div>
    <div class="row">

        <!-- Border Left Utilities -->
        <div class="col-lg-12">

            <div class="card mb-4 py-3 border-left-primary">
                <div class="card-body">
                    <div class="container mt-4">
                        <div>
                            Name: {{ $user->name }}
                        </div>
                        <div>
                            Email: {{ $user->email }}
                        </div>
                        <div>
                            Username: {{ $user->username }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
