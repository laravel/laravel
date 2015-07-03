<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
    <title>Laravue Application</title>
    {{-- This is the token Laravel requires for non-GET requests --}}
    <meta id="_token" value="{{ csrf_token() }}"> 
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
            font-family: 'Lato';
        }
    </style>
</head>
<body>
    <div id="app"></div>
    <script src="/js/bundle.js"></script>
</body>
</html>