<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href='https://fonts.googleapis.com/css?family=Raleway:100,400,300,600' rel='stylesheet' type='text/css'>

        <!-- Styles -->
        <style>
            html, body {
                font-family: 'Raleway';
                font-weight: 100;
                margin: 0;
                padding: 10px;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 0;
                top: 0;
            }

            .content {
                text-align:center;
            }

            .title {
                font-size: 84px;
            }

            button {
                color: #555;
                background-color: transparent;
                border: 1px solid #bbb;
                border-radius: 4px;
                box-sizing: border-box;
                cursor: pointer;
                display: inline-block;
                font-family: 'Raleway';
                font-size: 11px;
                font-weight: 600;
                height: 38px;
                letter-spacing: .1rem;
                line-height: 38px;
                padding: 0 20px;
                text-align: center;
                text-transform: uppercase;
                white-space: nowrap;
            }

            button.button-primary {
                color: #FFF;
                background-color: #3097D1;
                border: 1px solid #3097D1;
            }

            button.borderless {
                border: 0;
            }

            .m-r-md {
                margin-right: 20px;
            }

            .m-b-md {
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="buttons top-right">
                    <a href="/login"><button class="m-r-md">Login</button></a>
                    <a href="/register"><button class="button-primary">Register</button></a>
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Laravel
                </div>

                <div>
                    <a href="https://laravel.com/docs"><button class="borderless">Documentation</button></a>
                    <a href="https://laracasts.com"><button class="borderless">Laracasts</button></a>
                    <a href="https://github.com/laravel/laravel"><button class="borderless">GitHub</button></a>
                    <a href="https://twitter.com/laravelphp"><button class="borderless">Twitter</button></a>
                </div>
            </div>
        </div>
    </body>
</html>
