@extends('layouts.app')

@section('banner')
<div class="jumbotron">
    <div class="container">
        <h1 class="jumbotron__header">Welcome to Laravel 5!</h1>

        <p class="jumbotron__body">
            Laravel is a web application framework with expressive, elegant syntax. We believe development
            must be an enjoyable, creative experience to be truly fulfilling.
        </p>
    </div>
</div>
@stop

@section('content')
<ol class="steps">
    <li class="steps__item">
        <div class="body">
            <h2>Have a Look Around</h2>

            <p>
                Review <code>app/Http/Controllers/WelcomeController.php</code> to learn
                how this page was constructed.
            </p>
        </div>
    </li>

    <li class="steps__item">
        <div class="body">
            <h2>Harness Your Skills</h2>

            <p>
                Once you've toyed around for a bit, you'll surely want to dig in and
                learn more. Try:
            </p>

            <ul>
                <li><a href="http://laravel.com/docs">Laravel Documentation</a></li>
                <li><a href="https://laracasts.com">Laravel 5 From Scratch (via Laracasts)</a></li>
            </ul>
        </div>
    </li>

    <li class="steps__item">
        <div class="body">
            <h2>Forge Ahead</h2>

            <p>
                Finished building your app? It's time to deploy! Laravel still has your back. Use <a href="https://forge.laravel.com">Laravel Forge</a>.
            </p>
        </div>
    </li>

    <li class="steps__item">
        <div class="body">
            <h2>Profit</h2>

            <p>
                ...and go be with your family.
            </p>
        </div>
    </li>
</ol>
@stop
