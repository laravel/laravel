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
            height: 100%;
            color: #a2a8aa;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
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
            margin-bottom: 40px;
        }
        .quote {
            font-size: 24px
        }
    </style>
</head>
<body>
        <div class="container" id="app">
            <div class="content">
                <component is="@{{laravue.currentView}}" app="@{{@ laravue.app }}"></component>
            </div>
        </div>
    <script src="/js/bundle.js"></script>
</body>
</html>