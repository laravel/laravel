{{-- switch field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? $field['value'] ?? $field['default'] ?? '0';
    $field['onLabel'] = $field['onLabel'] ?? '';
    $field['offLabel'] = $field['offLabel'] ?? '';
    $field['color'] = $field['color'] ?? 'var(--bg-switch-checked-color, black)';
    $switchClass = (str_starts_with($field['color'], 'var(') || str_starts_with($field['color'], '#')) ? '' : 'switch-'.$field['color'];
@endphp

{{-- Wrapper --}}
@include('crud::fields.inc.wrapper_start')

    {{-- Translatable icon --}}
    @include('crud::fields.inc.translatable_icon')

    <div class="d-inline-flex align-items-center">
        {{-- Switch --}}
        <label class="form-switch switch switch-sm switch-label switch-pill mb-0 {{$switchClass}}" @if($field['color'] !== 'var(--bg-switch-checked-color, black)') style="--bg-switch-checked-color: {{ $field['color'] }};"  @endif>
            <input
                type="hidden"
                name="{{ $field['name'] }}"
                value="{{ (int) $field['value'] }}" />
            <input
                type="checkbox"
                data-init-function="bpFieldInitSwitch"
                {{ (bool) $field['value'] ? 'checked' : '' }}
                class="switch-input form-check-input"
                />
            <span
                class="switch-slider"
                data-checked="{{ $field['onLabel'] ?? '' }}"
                data-unchecked="{{ $field['offLabel'] ?? '' }}">
            </span>
        </label>

        {{-- Label --}}
        <label class="font-weight-normal mb-0 ml-2">{!! $field['label'] !!}</label>
    </div>

    {{-- Label for the required * --}}
    <label class="d-inline-flex m-0">&nbsp;</label>

    {{-- Hint --}}
    @isset($field['hint'])
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endisset
@include('crud::fields.inc.wrapper_end')

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}

{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('crud_fields_scripts')
    @bassetBlock('backpack/crud/fields/switch-field.js')
    <script>
        function bpFieldInitSwitch($element) {
            let element = $element[0];
            let hiddenElement = element.previousElementSibling;
            let id = `switch_${hiddenElement.name}_${Math.random() * 1e18}`;

            // set unique IDs so that labels are correlated with inputs
            element.setAttribute('id', id);
            element.parentElement.nextElementSibling.setAttribute('for', id);

            // set the default checked/unchecked state
            // if the field has been loaded with javascript
            hiddenElement.value !== '0'
                ? element.setAttribute('checked', true)
                : element.removeAttribute('checked');

            // JS Field API
            $(hiddenElement).on('CrudField:disable', () => element.setAttribute('disabled', true));
            $(hiddenElement).on('CrudField:enable', () => element.removeAttribute('disabled'));

            // when the checkbox is clicked
            // set the correct value on the hidden input
            $element.on('change', () => {
                hiddenElement.value = element.checked ? 1 : 0;
                hiddenElement.dispatchEvent(new Event('change'));
            });
        }
    </script>
    @endBassetBlock
@endpush

@push('crud_fields_styles')
    @bassetBlock('backpack/crud/fields/switch-field.css')
    <style>
        .switch-input:checked+.switch-slider {
            background-color: var(--bg-switch-checked-color, black);
        }
        .form-switch .form-check-input:checked {
            background-color: var(--bg-switch-checked-color, black);
        }
    </style>
    @endBassetBlock
@endpush

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
