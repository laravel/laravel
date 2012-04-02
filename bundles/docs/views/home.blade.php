@layout('docs::template')

@section('content')
	<h3>Learn the terrain.</h3>

	<p>
		You've landed yourself on our <code>default</code> home page. The route that
		is generating this page lives at:
	</p>

	<pre>APP_PATH/routes.php</pre>

<pre class="prettyprint lang-php linenums">
return array(
     'welcome' => 'Welcome to our website!',
);
</pre>

	<p>And the view sitting before you can be found at:</p>

	<pre>APP_PATH/views/home/index.php</pre>

	<h3>Create something beautiful.</h3>

	<p>
		Now that you're up and running, it's time to start creating!
		Here are some links to help you get started:
	</p>

	<ul>
		<li><a href="http://laravel.com">Official Website</a></li>
		<li><a href="http://forums.laravel.com">Laravel Forums</a></li>
		<li><a href="http://github.com/laravel/laravel">GitHub Repository</a></li>
	</ul>
@endsection
