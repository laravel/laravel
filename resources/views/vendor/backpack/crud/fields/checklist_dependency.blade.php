{{-- dependencyJson --}}
@php
  $field['wrapper'] = $field['wrapper'] ?? $field['wrapperAttributes'] ?? [];
  $field['wrapper']['class'] = $field['wrapper']['class'] ?? 'form-group col-sm-12';
  $field['wrapper']['class'] = $field['wrapper']['class'].' checklist_dependency';
  $field['wrapper']['data-entity'] = $field['wrapper']['data-entity'] ?? $field['field_unique_name'];
  $field['wrapper']['data-init-function'] = $field['wrapper']['init-function'] ?? 'bpFieldInitChecklistDependencyElement';
@endphp

@include('crud::fields.inc.wrapper_start')

<label>{!! $field['label'] !!}</label>
<?php
    $entity_model = $crud->getModel();

    //short name for dependency fields
    $primary_dependency = $field['subfields']['primary'];
    $secondary_dependency = $field['subfields']['secondary'];

    //all items with relation
    $dependencies = $primary_dependency['model']::with($primary_dependency['entity_secondary']);

    if(isset($primary_dependency['options']) && $primary_dependency['options'] instanceof \Closure){
        $dependencies = $primary_dependency['options']($dependencies);
    }

    // check if dependencies are a query builder, or the developer already called `->get()` on it.
    if ($dependencies instanceof \Illuminate\Contracts\Database\Query\Builder) {
        $dependencies = $dependencies->get();
    }

    $dependencyArray = [];

    //convert dependency array to simple matrix ( primary id as key and array with secondaries id )
    foreach ($dependencies as $primary) {
        $dependencyArray[$primary->id] = [];
        foreach ($primary->{$primary_dependency['entity_secondary']} as $secondary) {
            $dependencyArray[$primary->id][] = $secondary->id;
        }
    }

    $old_primary_dependency = old_empty_or_null($primary_dependency['name'], false) ?? false;
    $old_secondary_dependency = old_empty_or_null($secondary_dependency['name'], false) ?? false;

    //for update form, get initial state of the entity
    if (isset($id) && $id) {
        //get entity with relations for primary dependency
        $entity_dependencies = $entity_model->with($primary_dependency['entity'])
        ->with($primary_dependency['entity'].'.'.$primary_dependency['entity_secondary'])
        ->find($id);

        $secondaries_from_primary = [];

        //convert relation in array
        $primary_array = $entity_dependencies->{$primary_dependency['entity']}->toArray();

        $secondary_ids = [];
        //create secondary dependency from primary relation, used to check what checkbox must be checked from second checklist
        if ($old_primary_dependency) {
            foreach ($old_primary_dependency as $primary_item) {
                foreach ($dependencyArray[$primary_item] as $second_item) {
                    $secondary_ids[$second_item] = $second_item;
                }
            }
        } else { //create dependencies from relation if not from validate error
            foreach ($primary_array as $primary_item) {
                foreach ($primary_item[$secondary_dependency['entity']] as $second_item) {
                    $secondary_ids[$second_item['id']] = $second_item['id'];
                }
            }
        }
    }

    //json encode of dependency matrix
    $dependencyJson = json_encode($dependencyArray);

    $primaryDependencyOptionQuery = $primary_dependency['model']::query();

    if(isset($primary_dependency['options']) && $primary_dependency['options'] instanceof \Closure){
        $primaryDependencyOptionQuery = $primary_dependency['options']($primaryDependencyOptionQuery);
    }

    $primaryDependencyOptions = $primaryDependencyOptionQuery->get();

    $secondaryDependencyOptionQuery = $secondary_dependency['model']::query();

    if(isset($secondary_dependency['options']) && $secondary_dependency['options'] instanceof \Closure){
        $secondaryDependencyOptionQuery = $secondary_dependency['options']($secondaryDependencyOptionQuery);
    }

    $secondaryDependencyOptions = $secondaryDependencyOptionQuery->get();
    ?>

    <div class="container">

      <div class="row">
          <div class="col-sm-12">
              <label>{!! $primary_dependency['label'] !!}</label>
              @include('crud::fields.inc.translatable_icon', ['field' => $primary_dependency])
          </div>
      </div>

      <div class="row">

          <div class="hidden_fields_primary" data-name = "{{ $primary_dependency['name'] }}">
          <input type="hidden" bp-field-name="{{$primary_dependency['name']}}" name="{{$primary_dependency['name']}}" value="" />
          @if(isset($field['value']))
              @if($old_primary_dependency)
                  @foreach($old_primary_dependency as $item )
                  <input type="hidden" class="primary_hidden" name="{{ $primary_dependency['name'] }}[]" value="{{ $item }}">
                  @endforeach
              @else
                  @foreach( $field['value'][0]->pluck('id', 'id')->toArray() as $item )
                  <input type="hidden" class="primary_hidden" name="{{ $primary_dependency['name'] }}[]" value="{{ $item }}">
                  @endforeach
              @endif
            @endif
          </div>

      @foreach ($primaryDependencyOptions as $connected_entity_entry)
          <div class="col-sm-{{ isset($primary_dependency['number_columns']) ? intval(12/$primary_dependency['number_columns']) : '4'}}">
              <div class="checkbox">
                  <label class="font-weight-normal">
                      <input type="checkbox"
                          data-id = "{{ $connected_entity_entry->id }}"
                          class = 'primary_list'
                          @foreach ($primary_dependency as $attribute => $value)
                              @if (is_string($attribute) && $attribute != 'value')
                                  @if ($attribute=='name')
                                  {{ $attribute }}="{{ $value }}_show[]"
                                  @elseif(! $value instanceof \Closure)
                                  {{ $attribute }}="{{ $value }}"
                                  @endif
                              @endif
                          @endforeach
                          value="{{ $connected_entity_entry->id }}"

                          @if( ( isset($field['value']) && is_array($field['value']) && in_array($connected_entity_entry->id, $field['value'][0]->pluck('id', 'id')->toArray())) || $old_primary_dependency && in_array($connected_entity_entry->id, $old_primary_dependency))
                          checked = "checked"
                          @endif >
                          {{ $connected_entity_entry->{$primary_dependency['attribute']} }}
                  </label>
              </div>
          </div>
      @endforeach
      </div>

      <div class="row">
          <div class="col-sm-12">
              <label>{!! $secondary_dependency['label'] !!}</label>
              @include('crud::fields.inc.translatable_icon', ['field' => $secondary_dependency])
          </div>
      </div>

      <div class="row">
          <div class="hidden_fields_secondary" data-name="{{ $secondary_dependency['name'] }}">
            <input type="hidden" bp-field-name="{{$secondary_dependency['name']}}" name="{{$secondary_dependency['name']}}" value="" />
            @if(isset($field['value']))
              @if($old_secondary_dependency)
                @foreach($old_secondary_dependency as $item )
                  <input type="hidden" class="secondary_hidden" name="{{ $secondary_dependency['name'] }}[]" value="{{ $item }}">
                @endforeach
              @else
                @foreach( $field['value'][1]->pluck('id', 'id')->toArray() as $item )
                  <input type="hidden" class="secondary_hidden" name="{{ $secondary_dependency['name'] }}[]" value="{{ $item }}">
                @endforeach
              @endif
            @endif
          </div>

          @foreach ($secondaryDependencyOptions as $connected_entity_entry)
              <div class="col-sm-{{ isset($secondary_dependency['number_columns']) ? intval(12/$secondary_dependency['number_columns']) : '4'}}">
                  <div class="checkbox">
                      <label class="font-weight-normal">
                      <input type="checkbox"
                              class="secondary_list"
                              data-id="{{ $connected_entity_entry->id }}"
                          @foreach ($secondary_dependency as $attribute => $value)
                              @if (is_string($attribute) && $attribute != 'value')
                                @if ($attribute=='name')
                                  {{ $attribute }}="{{ $value }}_show[]"
                                @elseif(! $value instanceof \Closure)
                                  {{ $attribute }}="{{ $value }}"
                                @endif
                              @endif
                          @endforeach
                           value="{{ $connected_entity_entry->id }}"

                          @if( ( isset($field['value']) && is_array($field['value']) && (  in_array($connected_entity_entry->id, $field['value'][1]->pluck('id', 'id')->toArray()) || isset( $secondary_ids[$connected_entity_entry->id])) || $old_secondary_dependency && in_array($connected_entity_entry->id, $old_secondary_dependency)))
                               checked="checked"
                               @if(isset( $secondary_ids[$connected_entity_entry->id]))
                                disabled="disabled"
                               @endif
                          @endif > {{ $connected_entity_entry->{$secondary_dependency['attribute']} }}
                      </label>
                  </div>
              </div>
          @endforeach
      </div>
    </div>{{-- /.container --}}


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
  <script>
      var  {{ $field['field_unique_name'] }} = {!! $dependencyJson !!};
  </script>

  {{-- include checklist_dependency js --}}
  @bassetBlock('backpack/crud/fields/checklist-dependency-field.js')
    <script>
      function bpFieldInitChecklistDependencyElement(element) {

          var unique_name = element.data('entity');
          var dependencyJson = window[unique_name];
          var thisField = element;
          var handleCheckInput = function(el, field, dependencyJson) {
            let idCurrent = el.data('id');
            //add hidden field with this value
            let nameInput = field.find('.hidden_fields_primary').data('name');
            if(field.find('input.primary_hidden[value="'+idCurrent+'"]').length === 0) {
              let inputToAdd = $('<input type="hidden" class="primary_hidden" name="'+nameInput+'[]" value="'+idCurrent+'">');

              field.find('.hidden_fields_primary').append(inputToAdd);
              field.find('.hidden_fields_primary').find('input.primary_hidden[value="'+idCurrent+'"]').trigger('change');
            }
            $.each(dependencyJson[idCurrent], function(key, value){
              //check and disable secondies checkbox
              field.find('input.secondary_list[value="'+value+'"]').prop( "checked", true );
              field.find('input.secondary_list[value="'+value+'"]').prop( "disabled", true );
              field.find('input.secondary_list[value="'+value+'"]').attr('forced-select', 'true');
              //remove hidden fields with secondary dependency if was set
              var hidden = field.find('input.secondary_hidden[value="'+value+'"]');
              if(hidden)
                hidden.remove();
            });
          };
          
          thisField.find('div.hidden_fields_primary').children('input').first().on('CrudField:disable', function(e) {
              let input = $(e.target);
              input.parent().parent().find('input[type=checkbox]').attr('disabled', 'disabled');
              input.siblings('input').attr('disabled','disabled');
          });

          thisField.find('div.hidden_fields_primary').children('input').first().on('CrudField:enable', function(e) {
              let input = $(e.target);
              input.parent().parent().find('input[type=checkbox]').not('[forced-select]').removeAttr('disabled');
              input.siblings('input').removeAttr('disabled');
          });

          thisField.find('div.hidden_fields_secondary').children('input').first().on('CrudField:disable', function(e) {
              let input = $(e.target);
              input.parent().parent().find('input[type=checkbox]').attr('disabled', 'disabled');
              input.siblings('input').attr('disabled','disabled');
          });

          thisField.find('div.hidden_fields_secondary').children('input').first().on('CrudField:enable', function(e) {
              let input = $(e.target);
              input.parent().parent().find('input[type=checkbox]').not('[forced-select]').removeAttr('disabled');
              input.siblings('input').removeAttr('disabled');
          });

          thisField.find('.primary_list').each(function() {
            var checkbox = $(this);
            // re-check the secondary boxes in case the primary is re-checked from old.
            if(checkbox.is(':checked')){
               handleCheckInput(checkbox, thisField, dependencyJson);
            }
            // register the change event to handle subsequent checkbox state changes.
            checkbox.change(function(){
              if(checkbox.is(':checked')){
                handleCheckInput(checkbox, thisField, dependencyJson);
              }else{
                let idCurrent = checkbox.data('id');
                //remove hidden field with this value.
                thisField.find('input.primary_hidden[value="'+idCurrent+'"]').remove();

                // uncheck and active secondary checkboxs if are not in other selected primary.
                var secondary = dependencyJson[idCurrent];

                var selected = [];
                thisField.find('input.primary_hidden').each(function (index, input){
                  selected.push( $(this).val() );
                });

                $.each(secondary, function(index, secondaryItem){
                  var ok = 1;

                  $.each(selected, function(index2, selectedItem){
                    if( dependencyJson[selectedItem].indexOf(secondaryItem) != -1 ){
                      ok =0;
                    }
                  });

                  if(ok){
                    thisField.find('input.secondary_list[value="'+secondaryItem+'"]').prop('checked', false);
                    thisField.find('input.secondary_list[value="'+secondaryItem+'"]').prop('disabled', false);
                    thisField.find('input.secondary_list[value="'+secondaryItem+'"]').removeAttr('forced-select');
                  }
                });

              }
              });
          });


          thisField.find('.secondary_list').click(function(){

            var idCurrent = $(this).data('id');
            if($(this).is(':checked')){
              //add hidden field with this value
              var nameInput = thisField.find('.hidden_fields_secondary').data('name');
              var inputToAdd = $('<input type="hidden" class="secondary_hidden" name="'+nameInput+'[]" value="'+idCurrent+'">');

              thisField.find('.hidden_fields_secondary').append(inputToAdd);

            }else{
              //remove hidden field with this value
              thisField.find('input.secondary_hidden[value="'+idCurrent+'"]').remove();
            }
          });

      }
    </script>
  @endBassetBlock
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
