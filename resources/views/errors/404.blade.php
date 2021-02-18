@extends('base')

@section('title', 'Pagina no encontrada')

@section('content')

<div class="h-screen flex justify-center items-center">
    <div class="flex flex-col items-center mb-20 px-5">
        <img src="https://via.placeholder.com/404" alt="Error 404">
        <p class="mt-5">Error 404</p>
        <button class="btn btn-primary mt-5">Volver a la home</button>
    </div>
</div>

@endsection
