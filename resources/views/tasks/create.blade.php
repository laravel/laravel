@extends('layout')

@section('content')
    <h2>Create Task</h2>

    <form action="{{ route('tasks.store') }}" method="POST" id="task-form">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" class="form-control" id="title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Add Task</button>
    </form>

    <script>
        $(document).ready(function(){
            $('#task-form').submit(function(e){
                if($('#title').val().trim() === ''){
                    alert("Title cannot be empty.");
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
