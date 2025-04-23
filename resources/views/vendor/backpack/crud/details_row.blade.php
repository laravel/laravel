<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10" bp-section="crud-details-row">
	<div class="row text-wrap">
		@php
			$widgets = app('widgets')->where('section', 'details_row');
		@endphp
		@if($widgets->count() > 0)
			@include(backpack_view('inc.widgets'), ['widgets' => $widgets])
		@else
			<div class="col-md-12">
				{{ trans('backpack::crud.details_row') }}
			</div>
		@endif
	</div>
</div>
<div class="clearfix"></div>
