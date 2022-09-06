@extends('layouts.master')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="mt-2">
                @include('layouts.partials.messages')
            </div>
            <a href="{{ route('games.create') }}" class="btn btn-primary btn-sm float-right">Add Game</a>
            {{-- <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6> --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col" width="1%">#</th>
                            <th scope="col" width="5%">Title</th>
                            <th scope="col" width="10%">Video URL</th>
                            {{-- <th scope="col" width="10%">Username</th> --}}
                            <th scope="col" width="10%">Image</th>

                            <th scope="col" width="10%">Description</th>
                            <th scope="col" width="1%" colspan="3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($games as $i => $game)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $game->title }}</td>
                                <td>{{ $game->video_url }}</td>
                                <td> <img src="{{asset('assets/img/'.$game->image)}}" width="70%;" height="50%;;" alt="image">
                                              
                                </td>
                                <td>{{ $game->description }}</td>

                                <td>
                                    <a class="btn btn-info" href="{{ route('games.show', $game->id) }}">Show</a>
                                </td>
                                <td>
                                    <a class="btn btn-primary" href="{{ route('games.edit', $game->id) }}">Edit</a>
                                </td>
                                <td>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['games.destroy', $game->id], 'style' => 'display:inline']) !!}
                                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                    {!! Form::close() !!}

                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <div class="d-flex">
                    {!! $games->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
