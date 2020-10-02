<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#ff585d">

<title>{{ (app('env') !== 'production' ? '[' . app('env') . '] ' : null) . $page['title'] }}</title>
<meta name="description" content="{{ $page['description'] }}">

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="icon" type="image/png" href="/static/img/meta/favicon-32.png">
<link rel="apple-touch-icon" href="/static/img/meta/favicon-180.png">
<link rel="mask-icon" href="/static/img/meta/mask-icon.svg" color="#000000">

<link rel="stylesheet" href="{{ isset($mix_stylesheet) && !$mix_stylesheet ? $stylesheet : mix($stylesheet) }}">

<meta property="og:title" content="{{ $page['share_title'] ?? $page['title'] ?? null }}">
<meta property="og:description" content="{{ $page['share_description'] ?? $page['description'] ?? null }}">
<meta property="og:type" content="{{ $page['type'] ?? null }}">
<meta property="og:url" content="{{ $page['url'] ?? null }}">
<meta property="og:site_name" content="{{ $page['site_name'] ?? null }}">
<meta property="og:image" content="{{ $page['social_image'] ?? null }}">
<meta name="twitter:card" content="summary_large_image">
