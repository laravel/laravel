@props(['disabled' => false, 'checked' => false, 'value', 'label'])

    <input {{ $disabled ? 'disabled' : '' }}  {{ $checked ? 'checked' : '' }} value="{{ $value }}" id="{{ $label }}" type="radio" {!! $attributes->merge(['class' => 'with-gap']) !!}/>
    <x-input-label for="{{ $label }}" class="fw-normal" :value="$label"/>
