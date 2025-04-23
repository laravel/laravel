{{-- checklist --}}
@php
  $key_attribute = (new $field['model'])->getKeyName();
  $field['attribute'] ??= (new $field['model'])->identifiableAttribute();
  $field['number_of_columns'] ??= 3;
  $field['show_select_all'] ??= false;

  // calculate the checklist options
  if (!isset($field['options'])) {
      $field['options'] = $field['model']::all()->pluck($field['attribute'], $key_attribute)->toArray();
  } else {
      $field['options'] = call_user_func($field['options'], $field['model']::query());

      if(is_a($field['options'], \Illuminate\Contracts\Database\Query\Builder::class, true)) {
          $field['options'] = $field['options']->pluck($field['attribute'], $key_attribute)->toArray();
      }
  }

  // calculate the value of the hidden input
  $field['value'] = old_empty_or_null($field['name'], []) ??  $field['value'] ?? $field['default'] ?? [];
  if(!empty($field['value'])) {
      if (is_a($field['value'], \Illuminate\Support\Collection::class)) {
          $field['value'] = ($field['value'])->pluck($key_attribute)->toArray();
      } elseif (is_string($field['value'])){
        $field['value'] = json_decode($field['value']);
      }
  }

  // define the init-function on the wrapper
  $field['wrapper']['data-init-function'] ??= 'bpFieldInitChecklist';
@endphp

@include('crud::fields.inc.wrapper_start')

    <label>{!! $field['label'] !!}

    @if($field['show_select_all'] ?? false)
    <span class="fs-6 small checklist-select-all-inputs">
        <a href="javascript:void(0)" href="#" class="select-all-inputs">{{trans('backpack::crud.select_all')}}</a>
        <a href="javascript:void(0)" href="#" class="unselect-all-inputs d-none">{{trans('backpack::crud.unselect_all')}}</a> 
    </span>
    @endif
    </label>
    
    @include('crud::fields.inc.translatable_icon')

    <input type="hidden" data-show-select-all="{{var_export($field['show_select_all'])}}" value='@json($field['value'])' name="{{ $field['name'] }}">

    <div class="row checklist-options-container">
        @foreach ($field['options'] as $key => $option)
            <div class="col-sm-{{ intval(12/$field['number_of_columns']) }}">
                <div class="checkbox">
                  <label class="font-weight-normal">
                    <input type="checkbox" value="{{ $key }}"> {{ $option }}
                  </label>
                </div>
            </div>
        @endforeach
    </div>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        @bassetBlock('backpack/crud/fields/checklist-field.js')
        <script>
            function bpFieldInitChecklist(element) {
                let hidden_input = element.find('input[type=hidden]');
                let selected_options = JSON.parse(hidden_input.val() || '[]');
                let container = element.find('.row.checklist-options-container');
                let checkboxes = container.find(':input[type=checkbox]');                
                let showSelectAll = hidden_input.data('show-select-all');
                let selectAllAnchor = element.find('.checklist-select-all-inputs').find('a.select-all-inputs');
                let unselectAllAnchor = element.find('.checklist-select-all-inputs').find('a.unselect-all-inputs');

                // set the default checked/unchecked states on checklist options
                checkboxes.each(function(key, option) {
                  var id = $(this).val();

                  if (selected_options.map(String).includes(id)) {
                    $(this).prop('checked', 'checked');
                  } else {
                    $(this).prop('checked', false);
                  }
                });

                // when a checkbox is clicked
                // set the correct value on the hidden input
                checkboxes.click(function() {
                  var newValue = [];

                  checkboxes.each(function() {
                    if ($(this).is(':checked')) {
                      var id = $(this).val();
                      newValue.push(id);
                    }
                  });

                  hidden_input.val(JSON.stringify(newValue)).trigger('change');

                  toggleAllSelectAnchor();
                });
                  
                let selectAll = function() {
                  checkboxes.prop('checked', 'checked');
                  hidden_input.val(JSON.stringify(checkboxes.map(function() { return $(this).val(); }).get())).trigger('change');
                  selectAllAnchor.toggleClass('d-none');
                  unselectAllAnchor.toggleClass('d-none');
                };

                let unselectAll = function() {
                  checkboxes.prop('checked', false);
                  hidden_input.val(JSON.stringify([])).trigger('change');
                  selectAllAnchor.toggleClass('d-none');
                  unselectAllAnchor.toggleClass('d-none');
                };

                let toggleAllSelectAnchor = function() {
                  if(showSelectAll === false) {
                    return;
                  }

                  if (checkboxes.length === selected_options.length) {
                    selectAllAnchor.toggleClass('d-none');
                    unselectAllAnchor.toggleClass('d-none');
                  }
                };

                if(showSelectAll) {
                  selectAllAnchor.click(selectAll);
                  unselectAllAnchor.click(unselectAll);

                  toggleAllSelectAnchor();
                }

                hidden_input.on('CrudField:disable', function(e) {
                      checkboxes.attr('disabled', 'disabled');
                  });

                hidden_input.on('CrudField:enable', function(e) {
                    checkboxes.removeAttr('disabled');
                });

            }
        </script>
        @endBassetBlock
    @endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
