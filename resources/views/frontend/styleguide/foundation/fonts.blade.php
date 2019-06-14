@foreach ($weights as $weight)
	<p style="font-weight: {{ $weight }}">The quick brown fox jumps over the lazy dog</p>
	<p style="font-weight: {{ $weight }}; font-style: italic">The quick brown fox jumps over the lazy dog</p>
@endforeach
