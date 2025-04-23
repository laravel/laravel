@includeWhen(!empty($widget['wrapper']), backpack_view('widgets.inc.wrapper_start'))

@php
	$dismissible = isset($widget['close_button']) && $widget['close_button'];
@endphp

<div class="{{ $widget['class'] ?? 'alert alert-primary mb-3' }} {{ $dismissible ? 'alert-dismissible' : '' }}" role="alert">

	@if ($dismissible)	
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	@endif

	@if (isset($widget['heading']))
	<h4 class="alert-heading">{!! $widget['heading'] !!}</h4>
	@endif

	@if (isset($widget['content']))
	<p>{!! $widget['content'] !!}</p>
	@endif

</div>

@includeWhen(!empty($widget['wrapper']), backpack_view('widgets.inc.wrapper_end'))