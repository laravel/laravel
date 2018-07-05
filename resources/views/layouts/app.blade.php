<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, user-scalable=no">

	<title>{{ $page['title'] }}</title>
	<meta name="description" content="{{ $page['description'] }}">

	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="icon" type="image/png" href="/assets/img/meta/favicon-32.png">
	<link rel="apple-touch-icon" href="/assets/img/meta/favicon-180.png">

	<link rel="stylesheet" href="{{ mix('/assets/css/app.css') }} }}">

	<!-- twitter card data (https://dev.twitter.com/docs/cards, https://dev.twitter.com/docs/cards/large-image-summary-card) -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="{{ $page['site_twitter_handle'] }}">
	<meta name="twitter:creator" content="{{ $page['creator_twitter_handle'] }}"><!-- if page has an author (like a blog post), pass in twitter handle -->
	<meta name="twitter:title" content="{{ $page['title'] }}">
	<meta name="twitter:description" content="{{ $page['meta_description'] }}">
	<meta name="twitter:image:src" content="{{ $page['social_image'] }}"><!-- share image, min dimensions: 280x150, file size < 8MB -->

	<!-- open graph data -->
	<meta property="og:title" content="{{ $page['title'] }}">
	<meta property="og:type" content="article">
	<meta property="og:url" content="{{ $page['url'] }}"><!-- current page URL -->
	<meta property="og:image" content="{{ $page['social_image'] }}"><!-- share image, min dimensions: 1200x630, file size < 8MB -->
	<meta property="og:description" content="{{ $page['meta_description'] }}">
	<meta property="og:site_name" content="{{ $page['site_name'] }}">
</head>
<body>
	<div id="app">
		@include('layouts/partials/site-header')

		@yield('content')

		@include('layouts/partials/site-footer')
	</div>

	<script src="{{ mix('/assets/js/app.js') }}" async></script>
</body>
</html>
