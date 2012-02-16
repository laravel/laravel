<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>Error 500 - Internal Server Error</title>

		<style>
			@import url(http://fonts.googleapis.com/css?family=Droid+Serif);

			body
			{
				background-color:#2F3030;
				font-family:'Droid Serif', serif;
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
				padding-bottom:1em;
			}

			.bottom
			{
				background-image: url(data:image/gif;base64,R0lGODlhGAAKALMAAFtbW1laWi4uLsHBwVpbWyssLMLCwiwtLS0uLiorKy4vLy8wMPHx8QAAAAAAAAAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzA3OURGNzE1OEEzMTFFMTg3MTBFOTBDNDJCNUY1Q0IiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzA3OURGNzI1OEEzMTFFMTg3MTBFOTBDNDJCNUY1Q0IiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozMDc5REY2RjU4QTMxMUUxODcxMEU5MEM0MkI1RjVDQiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDozMDc5REY3MDU4QTMxMUUxODcxMEU5MEM0MkI1RjVDQiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAAAAAAALAAAAAAYAAoAAARFkMlJq712hDCwZwaRjAlhfFSYFAeCHEV5eiqLKLjyxqZVt7iFcIHbySQGwAqoGDqJOlgCYLDdms9scSfIZb/DnABM/kYAADs=);
				background-repeat:repeat-x;
				font-size:0.9em;
				text-shadow:1px 1px 1px #222222;
			}

			.bottom p
			{
				line-height:1.5em;
			}

			.bottom h3
			{
				font-family:Helvetica, "Helvetica Neue", Arial, sans-serif;
				text-transform:uppercase;
				color:#FFFFFF;
			}

			a, a:visited
			{
				font-family:Helvetica, "Helvetica Neue", Arial, sans-serif;
				font-size:0.9em;
				font-weight:bold;
				color:#FBCC62;
				text-transform:uppercase;
				text-decoration:none;
			}

			a:hover
			{
				text-decoration:underline;
			}
		</style>
	</head>
	<body>
		<div class="top">
			<div class="container">
				<?php $messages = array('Ouch.', 'Oh no!', 'Whoops!'); ?>

				<h1><?php echo $messages[mt_rand(0, 2)]; ?></h1>

				<h2>Server Error: 500 (Internal Server Error)</h2>
			</div>
		</div>
		<div class="bottom">
			<div class="container">
				<h3>What does this mean?</h3>

				<p>
					Something went wrong on our servers while we were processing your request.
					We're really sorry about this, and will work hard to get this resolved as
					soon as possible.
				</p>

				<p>
					Perhaps you would like to go to our <?php echo HTML::link('/', 'home page'); ?>?
				</p>
			</div>
		</div>
	</body>
</html>
