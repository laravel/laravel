{{-- Show the inputs --}}
@foreach ($fields as $field)
    @include($crud->getFirstFieldView($field['type'], $field['view_namespace'] ?? false), $field)
@endforeach

