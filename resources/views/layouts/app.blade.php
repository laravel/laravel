<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<meta name="theme-color" content="#ff585d">

	<title>{{ $page['title'] }}</title>
	<meta name="description" content="{{ $page['description'] }}">

	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="icon" type="image/png" href="/assets/img/meta/favicon-32.png">
	<link rel="apple-touch-icon" href="/assets/img/meta/favicon-180.png">
	<link rel="mask-icon" href="/assets/img/meta/mask-icon.svg" color="#000000">

	<link rel="stylesheet" href="{{ mix('/assets/css/app.css') }}">

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="{{ $page['share_title'] }}">
	<meta name="twitter:site" content="{{ $page['site_twitter_handle'] }}">
	<meta name="twitter:image:src" content="{{ $page['social_image'] }}">
	<meta name="twitter:description" content="{{ $page['share_description'] }}">
	<meta name="twitter:creator" content="{{ $page['creator_twitter_handle'] }}">

	<meta property="og:title" content="{{ $page['share_title'] }}">
	<meta property="og:type" content="article">
	<meta property="og:url" content="{{ $page['url'] }}">
	<meta property="og:site_name" content="{{ $page['site_name'] }}">
	<meta property="og:image" content="{{ $page['social_image'] }}">
	<meta property="og:description" content="{{ $page['share_description'] }}">
</head>
<body>
	<div id="app">
		@include('layouts/partials/site-header')

		@yield('content')

		@include('layouts/partials/site-footer')
	</div>

	<script src="https://cdn.polyfill.io/v2/polyfill.js?features=Array.from,Array.prototype.find,Element.prototype.classList,IntersectionObserver,Promise"></script>
	<script src="{{ mix('/assets/js/app.js') }}" async></script>
</body>
</html>
