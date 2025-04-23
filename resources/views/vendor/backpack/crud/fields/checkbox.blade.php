{{-- checkbox field --}}

@php
  $field['value'] = old_empty_or_null($field['name'], '') ?? $field['value'] ?? $field['default'] ?? '';
  $field['attributes']['class'] = $field['attributes']['class'] ?? 'form-check-input';
@endphp

@include('crud::fields.inc.wrapper_start')
    @include('crud::fields.inc.translatable_icon')
      <div class="form-check d-flex mb-0 align-items-center">
        <input type="hidden" name="{{ $field['name'] }}" value="{{ $field['value'] }}">
    	  <input type="checkbox"
          data-init-function="bpFieldInitCheckbox"

          @if ((bool)$field['value'])
            checked="checked"
          @endif

          @if (isset($field['attributes']))
            @foreach ($field['attributes'] as $attribute => $value)
    			  {{ $attribute }}="{{ $value }}"
        	  @endforeach
          @endif
          >
    	  <label class="font-weight-normal fw-normal mb-0 ml-2 ms-2">{!! $field['label'] !!}</label>

        {{-- HINT --}}
        @if (isset($field['hint']))
            <p class="help-block ml-2 ms-2">{!! $field['hint'] !!}</p>
        @endif
      </div>
@include('crud::fields.inc.wrapper_end')

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        @bassetBlock('backpack/crud/fields/checkbox-field.js')
        <script>
            function bpFieldInitCheckbox(element) {
                var hidden_element = element.siblings('input[type=hidden]');
                var id = 'checkbox_'+Math.floor(Math.random() * 1000000);

                // make sure the value is a boolean (so it will pass validation)
                if (hidden_element.val() === '') hidden_element.val(0).trigger('change');

                // set unique IDs so that labels are correlated with inputs
                element.attr('id', id);
                element.siblings('label').attr('for', id);

                // set the default checked/unchecked state
                // if the field has been loaded with javascript
                if (hidden_element.val() != 0) {
                  element.prop('checked', 'checked');
                } else {
                  element.prop('checked', false);
                }

                hidden_element.on('CrudField:disable', function(e) {
                  element.prop('disabled', true);
                });
                hidden_element.on('CrudField:enable', function(e) {
                  element.removeAttr('disabled');
                });

                // when the checkbox is clicked
                // set the correct value on the hidden input
                element.change(function() {
                  if (element.is(":checked")) {
                    hidden_element.val(1).trigger('change');
                  } else {
                    hidden_element.val(0).trigger('change');
                  }
                })
            }
        </script>
        @endBassetBlock
    @endpush

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
