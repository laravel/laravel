<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>Laravel - A Framework For Web Artisans</title>

		<style>
			@import url(http://fonts.googleapis.com/css?family=Droid+Serif);

			body
			{
				background-color:#2F3030;
				font-family:"Droid Serif", "Georgia", "Times New Roman", "Palatino", "Hoefler Text", "Baskerville", serif;
				color:#C8CCCC;
				margin:0;
			}

			h1
			{
				font-family:Helvetica, "Helvetica Neue", Arial, sans-serif;
				font-size:2em;
				color:#333333;
				margin:0.5em 0 0.3em 0;
			}

			h1 em
			{
				font-family:"Droid Serif", "Georgia", "Times New Roman", "Palatino", "Hoefler Text", "Baskerville", serif;
				color:#E33922;
			}

			h2
			{
				font-family:Helvetica, "Helvetica Neue", Arial, sans-serif;
				font-size:1.3em;
				color:#6B848A;
				margin:0;
			}

			.container
			{
				width:720px;
				margin:0 auto;
				padding:1em 0;
			}

			.top
			{
				background-color:#E9EDEE;
				color:#7A7D7D;
				text-shadow:1px 1px 0px #FFFFFF;
			}

			.top p
			{
				font-size:1.2em;
			}

			.top p strong
			{
				color:#676B6B;
			}

			.top p em
			{
				color:#658787;
			}

			.bottom
			{
				font-size:0.9em;
				text-shadow:1px 1px 1px #222222;
				background-image: url(data:image/gif;base64,R0lGODlhGAAKALMAAFtbW1laWi4uLsHBwVpbWyssLMLCwiwtLS0uLiorKy4vLy8wMPHx8QAAAAAAAAAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzA3OURGNzE1OEEzMTFFMTg3MTBFOTBDNDJCNUY1Q0IiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzA3OURGNzI1OEEzMTFFMTg3MTBFOTBDNDJCNUY1Q0IiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozMDc5REY2RjU4QTMxMUUxODcxMEU5MEM0MkI1RjVDQiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDozMDc5REY3MDU4QTMxMUUxODcxMEU5MEM0MkI1RjVDQiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAAAAAAALAAAAAAYAAoAAARFkMlJq712hDCwZwaRjAlhfFSYFAeCHEV5eiqLKLjyxqZVt7iFcIHbySQGwAqoGDqJOlgCYLDdms9scSfIZb/DnABM/kYAADs=);
				background-repeat:repeat-x;
			}

			.bottom h3
			{
				font-family:Helvetica, "Helvetica Neue", Arial, sans-serif;
				text-transform:uppercase;
				color:#FFFFFF;
			}

			.links
			{
				padding:0;
				margin-top:1.3em;
			}

			.links li
			{
				display:inline;
				padding-right:2em;
			}

			.links a, .links a:visited
			{
				font-family:Helvetica, "Helvetica Neue", Arial, sans-serif;
				font-size:0.9em;
				font-weight:bold;
				color:#FBCC62;
				text-transform:uppercase;
				text-decoration:none;
			}

			.links a:hover
			{
				text-decoration:underline;
			}

			code
			{
				display:block;
				background-color:#222222;
				font-family:Monaco, "Bitstream Vera Sans Mono", "Courier New", Courier, monospace;
				font-size:0.9em;
				color:#FFFFFF;
				border:1px solid #555555;
				margin:2em 0;
				padding:1em 1em 0.8em 1em;
			}

			.sep
			{
				margin-top:3em;
			}
		</style>
	</head>
	<body>
		<div class="top">
			<div class="container">
				<h1>Welcome To <em>Laravel</em></h1>

				<h2>A Framework For Web Artisans</h2>

				<p>
					You have successfully installed the Laravel framework. Laravel is a simple framework
					that helps web artisans create <strong>beautiful</strong>, <em>creative</em> applications using <strong>elegant</strong>, <em>expressive</em>
					syntax. You'll love using it.
				</p>
			</div>
		</div>
		<div class="bottom">
			<div class="container">

				<h3>Learn the terrain.</h3>

				<p>
					You've landed yourself on our default home page. The route that
					is generating this page lives at:
				</p>

				<pre><code>APP_PATH/routes.php</code></pre>

				<p>And the view sitting before you can be found at:</p>

				<pre><code>APP_PATH/views/home/index.php</code></pre>

				<div class="sep"></div>

				<h3>Create something beautiful.</h3>

				<p>
					Now that you're up and running, it's time to start creating!
					Here are some links to help you get started:
				</p>

				<ul class="links">
					<li><a href="http://laravel.com">Official Website &raquo;</a></li>
					<li><a href="http://forums.laravel.com">Laravel Forums &raquo;</a></li>
					<li><a href="http://github.com/laravel/laravel">GitHub Repository &raquo;</a></li>
				</ul>

			</div>
		</div>
	</body>
</html>
