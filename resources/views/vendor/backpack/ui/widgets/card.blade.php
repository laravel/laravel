@php
	// preserve backwards compatibility with Widgets in Backpack 4.0
	$widget['wrapper']['class'] = $widget['wrapper']['class'] ?? $widget['wrapperClass'] ?? 'col-sm-6 col-md-4';
@endphp

@includeWhen(!empty($widget['wrapper']), backpack_view('widgets.inc.wrapper_start'))
	<div class="{{ $widget['class'] ?? 'card' }}">
		@if (isset($widget['content']))
			@if (isset($widget['content']['header']))
				<div class="card-header">{!! $widget['content']['header'] !!}</div>
			@endif
			<div class="card-body">{!! $widget['content']['body'] !!}</div>
	  	@endif
	</div>
@includeWhen(!empty($widget['wrapper']), backpack_view('widgets.inc.wrapper_end'))