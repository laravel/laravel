@if ($crud->get('reorder.enabled') && $crud->hasAccess('reorder'))
    <a href="{{ url($crud->route.'/reorder') }}" bp-button="reorder" class="btn btn-outline-primary" data-style="zoom-in">
        <i class="la la-arrows"></i> <span>{{ trans('backpack::crud.reorder') }} {{ $crud->entity_name_plural }}</span>
    </a>
@endif