<html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Grid</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
</head>
<body class="mx-auto mt-10 max-w-2xl ">
<br>

 <form action="get"> 
    <div class=" grid grid-cols-3 gap-20 ">
    @foreach($links as $link)
    <div class="relative">
    <x-button>{{$link->title}}</x-button>
      <button title="Delete Link" class="absolute top-2 right-1 hover:
    "><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
  <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>
</button>
    </div>

     @endforeach
    
     </form> 
    
    <br>
    <br>
    <br>
<!-- 
    <div class="  relative">
    @foreach($links as $link)
    <div class="grid grid-cols-3 gap-20"></div>
        <x-button>{{ $link->title}}</x-button>

        <form action="{{ route('grid.destroy', ['link' => $link->id]) }}" method="POST">
      @csrf
      @method('DELETE')
      <button type="submit">Delete</button>
    </form>

     @endforeach -->
    