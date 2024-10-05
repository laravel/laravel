<style>
    .colored {
    background-color: white; 
    transition: background-color 0.8s;
    height: 10rem;
    line-height: 10rem;

}

</style>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link grid</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    @vite('resources/css/app.css')
</head>
<body class="mx-auto mt-10 max-w-2xl">

    <div class="grid grid-cols-3 text-center">
        @foreach($links as $link)
        <!-- echo {{$link->color}} -->
            <div class="colored size-44 rounded-lg border border-black gap-4 mb-6 relative "
             onmouseover="this.style.backgroundColor='{{ $link->color }}';"
             onmouseout="this.style.backgroundColor='#FFFFFF';">
            
            <a class="text-md font-bold" href="https://{{$link->page}}">{{$link->title}}</a>

                <a title="Edit Link" href="{{route('links.edit',['id'=>$link->id])}}" class="absolute bottom-2 right-1
                    "><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                     </svg>
                </a>
                <form action="{{ route('links.destroy', ['link' => $link->id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button title="Delete Link" class="absolute top-2 right-1 hover:
                    "><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </button>
                </form>
            </div>
        @endforeach
        <div class="size-44 rounded-lg border border-black gap-4 mb-6  relative">
                <a class="absolute top-16 right-16 " href="{{route('links.create')}}">
                    <div >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                </a>
        </div>


    </div>

</body>
</html>


