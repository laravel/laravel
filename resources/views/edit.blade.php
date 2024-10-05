<html lang="en">
<head>

    <title>Grid Edit</title>
</head>
<body>
    <form method="POST" action="{{ route('links.update',['id'=>$link->id]) }}">
    @csrf
    @method('PUT')
        <div class="mb-2">
            Set a Title: <input value="{{$link->title}}" type="text" name="title" id="title"></input> <br>
        </div>
        <div class="mb-2">
            <br> Set a Link: <input value="{{$link->page}}" type="text" name="page" id="title"> <br>
        </div>
        <div class="">
            <label class="mb-4" >
            <br>Set a Color: <input type="color" name="color" value="{{$link->color}}" id="color">
            </label>
            </div>
<br>

<button type="submit">Save Changes</button>

    </form>
</body>
</html>