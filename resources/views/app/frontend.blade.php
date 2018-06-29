<h1>Templates</h1>

<ul>
	@foreach ($templates as $template)
		<li><a href="/{{ $template }}">{{ $template }}</a></li>
	@endforeach
</ul>
