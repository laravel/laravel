@extends('layouts.app')

@section('content')
<div id="welcome">
    <div class="jumbotron">
        <div class="container">
            <h1 class="jumbotron__header">You have arrived.</h1>

            <p class="jumbotron__body">
                Laravel is a web application framework with expressive, elegant syntax. We believe development
                must be an enjoyable, creative experience. Enjoy the fresh air.
            </p>
        </div>
    </div>

    <div class="container">
        <ol class="steps">
            <li class="steps__item">
                <div class="body">
                    <h2>Go Exploring</h2>

                    <p>
                        Review <code>app/Http/routes.php</code> to learn how HTTP requests are
                        routed to controllers.
                    </p>

                    <p>
                        We've included simple login and registration screens to get you started.
                    </p>
                </div>
            </li>

            <li class="steps__item">
                <div class="body">
                    <h2>Master Your Craft</h2>

                    <p>
                        Ready to keep learning more about Laravel? Start here:
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
                        When you're finished building your application, Laravel still has your back. Check out <a href="https://forge.laravel.com">Laravel Forge</a>.
                    </p>
                </div>
            </li>
        </ol>
    </div>
</div>
@stop
