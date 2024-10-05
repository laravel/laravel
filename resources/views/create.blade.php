<html lang="en">
<head>

    <title>Grid New Link</title>
</head>
<body>
    <form method="POST" action="{{ route('links.store') }}">
    @csrf
        <div class="mb-2">
            Set a Title: <input type="text" name="title" id="title"> <br>
        </div>
        <div class="mb-2">
            <br> Set a Link: <input type="text" name="page" id="title"> <br>
        </div>
        <div class="">
            <label class="mb-4" >
            <br>Set a Color: <input type="color" name="color" value="" id="color">
            </label>
            </div>
<br>

<button type="submit">Add Link</button>

    </form>
</body>
</html>