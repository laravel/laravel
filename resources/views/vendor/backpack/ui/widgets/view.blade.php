{{-- view field --}}
@includeWhen(!empty($widget['wrapper']), backpack_view('widgets.inc.wrapper_start'))
	
	@include($widget['view'], ['widget' => $widget])

@includeWhen(!empty($widget['wrapper']), backpack_view('widgets.inc.wrapper_end'))