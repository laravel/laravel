<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('tasks.index') }}">Task Manager</a>
        </div>
    </nav>
    
    <div class="container mt-4">
        @yield('content')
    </div>

    <script>
        $(document).ready(function(){
            $('.delete-task').click(function(e){
                e.preventDefault();
                if(confirm("Are you sure you want to delete this task?")){
                    $(this).closest('form').submit();
                }
            });
        });
    </script>
</body>
</html>
