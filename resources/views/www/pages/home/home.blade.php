@extends('www.layouts.default')

@section('body-class', 'home')

@section('content')

    <div class="container">

        <div class="starter-template">
        
            <h1>Starter template</h1>
            <p class="lead">Welcome to your new project.</p>
        
        	<h5>What's Included</h5>
        	<ul>
        		<li>Bootstrap</li>
				<li>Font Awesome</li>
				<li>jQuery</li>
				<li>Gulp</li>
				<li>Browserify</li>
				<li>Underscore</li>
			</ul>

			<hr />

			<h4>Known Issues</h4>
			<p>
				<strong>Laravel Elixir</strong><br />

				Gulp build process is semi-broken. <span style="color:#f00">Running the task 'gulp' will error</span>.<br /><br />

				<strong>To start the initial build process run these gulp tasks</strong><br />
<pre style="max-width: 350px;">gulp copy
gulp sass
gulp browserify
gulp version
gulp watch</pre>
			</p>

			<p>
				Gulp watch looks for changes in this directory<br />
<pre style="max-width: 350px;">resources/assets/**</pre>
				and will recompile when changes are detected.
			</p>


        </div>

    </div>
    
@stop