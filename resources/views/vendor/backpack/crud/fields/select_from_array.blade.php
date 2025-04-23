@php
    $field['allows_null'] = $field['allows_null'] ?? $crud->model::isColumnNullable($field['name']);
    $field['value'] = old_empty_or_null($field['name'], '') ??  $field['value'] ?? $field['default'] ?? '';
    $field['multiple'] = $field['allows_multiple'] ?? $field['multiple'] ?? false;
@endphp
{{-- select from array --}}
@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')
    @if($field['multiple'])<input type="hidden" name="{{ $field['name'] }}" value="" @if(in_array('disabled', $field['attributes'] ?? [])) disabled @endif />@endif
    <select
        name="{{ $field['name'] }}@if ($field['multiple'])[]@endif"
        @include('crud::fields.inc.attributes', ['default_class' => 'form-control form-select'])
        @if ($field['multiple'])multiple bp-field-main-input @endif
        >

        @if ($field['allows_null'] && !$field['multiple'])
            <option value="">-</option>
        @endif

        @if (count($field['options']))
            @foreach ($field['options'] as $key => $value)
                @if($key == $field['value'] || (is_array($field['value']) && in_array($key, $field['value'])))
                    <option value="{{ $key }}" selected>{{ $value }}</option>
                @else
                    <option value="{{ $key }}">{{ $value }}</option>
                @endif
            @endforeach
        @endif
    </select>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')
