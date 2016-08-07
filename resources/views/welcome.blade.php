<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato', sans-serif;
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }

            .problem {
                color: red;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Laravel 5</div>

                @if (version_compare(PHP_VERSION, '5.6.4', '>='))
                    <p>Your version of PHP is 5.6.4 or higher.</p>
                @else
                    <p class="problem">Your version of PHP is too low. You need PHP 5.6.4 or higher to use CakePHP.</p>
                @endif

                @if (extension_loaded('openssl'))
                    <p>Your version of PHP has the openssl extension loaded.</p>
                @else
                    <p class="problem">Your version of PHP does NOT have the openssl extension loaded.</p>
                @endif

                @if (extension_loaded('pdo'))
                    <p>Your version of PHP has the pdo extension loaded.</p>
                @else
                    <p class="problem">Your version of PHP does NOT have the pdo extension loaded.</p>
                @endif

                @if (extension_loaded('pdo'))
                    <p>Your version of PHP has the pdo extension loaded.</p>
                @else
                    <p class="problem">Your version of PHP does NOT have the pdo extension loaded.</p>
                @endif

                @if (extension_loaded('mbstring'))
                    <p>Your version of PHP has the mbstring extension loaded.</p>
                @else
                    <p class="problem">Your version of PHP does NOT have the mbstring extension loaded.</p>;
                @endif

                @if (extension_loaded('tokenizer'))
                    <p>Your version of PHP has the tokenizer extension loaded.</p>
                @else
                    <p class="problem">Your version of PHP does NOT have the tokenizer extension loaded.</p>;
                @endif
            </div>
        </div>
    </body>
</html>
