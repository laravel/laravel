<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Clique</title>
	<style>
		@import url(//fonts.googleapis.com/css?family=Lato:700);

		body {
			margin:0;
			font-family:'Lato', sans-serif;
			text-align:center;
			color: #999;
		}

		.welcome {
			width: 300px;
			height: 200px;
			position: absolute;
			left: 50%;
			top: 30%;
			margin-left: -150px;
			margin-top: -100px;
		}

		a, a:visited {
			text-decoration:none;
		}

		h1 {
			font-size: 32px;
			margin: 16px 0 0 0;
		}
	</style>
</head>
<body>
	<div class="welcome">
		<a href="http://www.clique.dev" title="Clique">
			{{ 	HTML::image(
					'img/whatcomisthisagain.jpg',
				 	'Some Icon', 
				 	array(
				 		'width' 	=> '250', 
				 		'height' 	=> '250',
				 		'align' 	=>	'middle'
					)
				) 
			}}
		</a>
		<h1>Welcome to Clique.</h1>
		</br>

		{{ $greeting . ' ' . $person . ', you have entered Clique:'}}

		@forelse($statements as $item)
			<li> {{ $item}}
		@empty
			<li> {{ 'an online org-tracking tool' }}
		@endforelse
		</br>
		</br>

		@if($test)
			<i>{{ 'Calm your tits and come back when we\'re ready.' }}</i>
		@endif
	
	</div>
</body>
</html>
