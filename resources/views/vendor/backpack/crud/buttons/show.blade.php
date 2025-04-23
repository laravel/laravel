@if ($crud->hasAccess('show', $entry))
	@if (!$crud->model->translationEnabled())

	{{-- Single edit button --}}
	<a href="{{ url($crud->route.'/'.$entry->getKey().'/show') }}" bp-button="show" class="btn btn-sm btn-link">
		<i class="la la-eye"></i> <span>{{ trans('backpack::crud.preview') }}</span>
	</a>

	@else

	{{-- show button group --}}
	<div class="btn-group">
	  <a href="{{ url($crud->route.'/'.$entry->getKey().'/show') }}" class="btn btn-sm btn-link pr-0">
	  	<span><i class="la la-eye"></i> {{ trans('backpack::crud.preview') }}</span>
	  </a>
	  <a class="btn btn-sm btn-link dropdown-toggle text-primary pl-1" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    <span class="caret"></span>
	  </a>
	  <ul class="dropdown-menu dropdown-menu-right">
  	    <li class="dropdown-header">{{ trans('backpack::crud.preview') }}:</li>
	  	@foreach ($crud->model->getAvailableLocales() as $key => $locale)
		  	<a class="dropdown-item" href="{{ url($crud->route.'/'.$entry->getKey().'/show') }}?_locale={{ $key }}">{{ $locale }}</a>
	  	@endforeach
	  </ul>
	</div>

	@endif
@endif
