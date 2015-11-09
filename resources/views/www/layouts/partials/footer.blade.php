<footer>

	<div class="container">
		<p>&copy; {{ date('Y') }} Project</p>
	</div>

</footer>


<script src="{{ elixir('dist/www/js/bundle.js') }}"></script>

@yield ('page-scripts')