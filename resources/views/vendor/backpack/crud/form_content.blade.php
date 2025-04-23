<input type="hidden" name="_http_referrer" value={{ session('referrer_url_override') ?? old('_http_referrer') ?? \URL::previous() ?? url($crud->route) }}>

{{-- See if we're using tabs --}}
@if ($crud->tabsEnabled() && count($crud->getTabs()))
    @include('crud::inc.show_tabbed_fields')
    <input type="hidden" name="_current_tab" value="{{ Str::slug($crud->getTabs()[0]) }}" />
@else
  <div class="card">
    <div class="card-body row">
      @include('crud::inc.show_fields', ['fields' => $crud->fields()])
    </div>
  </div>
@endif


{{-- Define blade stacks so css and js can be pushed from the fields to these sections. --}}

@section('after_styles')

    {{-- CRUD FORM CONTENT - crud_fields_styles stack --}}
    @stack('crud_fields_styles')

@endsection

@section('after_scripts')

    {{-- CRUD FORM CONTENT - crud_fields_scripts stack --}}
    @stack('crud_fields_scripts')

    <script>
    function initializeFieldsWithJavascript(container) {
      var selector;
      if (container instanceof jQuery) {
        selector = container;
      } else {
        selector = $(container);
      }
      selector.find("[data-init-function]").not("[data-initialized=true]").each(function () {
        var element = $(this);
        var functionName = element.data('init-function');

        if (typeof window[functionName] === "function") {
          window[functionName](element);

          // mark the element as initialized, so that its function is never called again
          element.attr('data-initialized', 'true');
        }
      });
    }

    /**
     * Auto-discover first focusable input
     * @param {jQuery} form
     * @return {jQuery}
     */
    function getFirstFocusableField(form) {
        return form.find('input, select, textarea, button')
            .not('.close')
            .not('[disabled]')
            .filter(':visible:first');
    }

    /**
     *
     * @param {jQuery} firstField
     */
    function triggerFocusOnFirstInputField(firstField) {
        if (firstField.hasClass('select2-hidden-accessible')) {
            return handleFocusOnSelect2Field(firstField);
        }

        firstField.trigger('focus');
    }

    /**
     * 1- Make sure no other select2 input is open in other field to focus on the right one
     * 2- Check until select2 is initialized
     * 3- Open select2
     *
     * @param {jQuery} firstField
     */
    function handleFocusOnSelect2Field(firstField){
        firstField.select2('focus');
    }

    /*
    * Hacky fix for a bug in select2 with jQuery 3.6.0's new nested-focus "protection"
    * see: https://github.com/select2/select2/issues/5993
    * see: https://github.com/jquery/jquery/issues/4382
    *
    */
    $(document).on('select2:open', () => {
        setTimeout(() => document.querySelector('.select2-container--open .select2-search__field').focus(), 100);
    });

    jQuery('document').ready(function($){

      // trigger the javascript for all fields that have their js defined in a separate method
      initializeFieldsWithJavascript('form');

      // Retrieves the current form data
      function getFormData() {
        let formData = new FormData(document.querySelector("main form"));
        // remove internal inputs from formData, the ones that start with "_", like _token, _http_referrer, etc.
        let pairs = [...formData].map(pair => pair[0]);
        for (let pair of pairs) {
          if (pair.startsWith('_')) {
            formData.delete(pair);
          }
        }
        return new URLSearchParams(formData).toString();
      }

      // Prevents unloading of page if form data was changed
      function preventUnload(event) {
        if (initData !== getFormData()) {
          // Cancel the event as stated by the standard.
          event.preventDefault();
          // Older browsers supported custom message
          event.returnValue = '';
        }
      }

      @if($crud->getOperationSetting('warnBeforeLeaving'))
        const initData = getFormData();
        window.addEventListener('beforeunload', preventUnload);
      @endif

      // Save button has multiple actions: save and exit, save and edit, save and new
      var saveActions = $('#saveActions')
      crudForm        = saveActions.parents('form')

      // Ctrl+S and Cmd+S trigger Save button click
      $(document).keydown(function(e) {
          if ((e.which == '115' || e.which == '83' ) && (e.ctrlKey || e.metaKey))
          {
              e.preventDefault();
              $("button[type=submit]").trigger('click');
              return false;
          }
          return true;
      });

      // prevent duplicate entries on double-clicking the submit form
      crudForm.submit(function (event) {
        window.removeEventListener('beforeunload', preventUnload);
        $("button[type=submit]").prop('disabled', true);
      });

      // Place the focus on the first element in the form
      @if( $crud->getAutoFocusOnFirstField() )
        @php
          $focusField = Arr::first($fields, function($field) {
              return isset($field['auto_focus']) && $field['auto_focus'] === true;
          });
        @endphp

        let focusField, focusFieldTab;

        @if ($focusField)
          @php
            $focusFieldName = isset($focusField['value']) && is_iterable($focusField['value']) ? $focusField['name'] . '[]' : $focusField['name'];
            $focusFieldTab = $focusField['tab'] ?? null;
          @endphp
            focusFieldTab = '{{ Str::slug($focusFieldTab) }}';

            // if focus is not 'null' navigate to that tab before focusing.
            if(focusFieldTab !== 'null'){
              $('#form_tabs a[tab_name="'+focusFieldTab+'"]').tab('show');
            }
            focusField = $('[name="{{ $focusFieldName }}"]').eq(0);
        @else
            focusField = getFirstFocusableField($('form'));
        @endif

        const fieldOffset = focusField.offset().top;
        const scrollTolerance = $(window).height() / 2;

        triggerFocusOnFirstInputField(focusField);

        if( fieldOffset > scrollTolerance ){
            $('html, body').animate({scrollTop: (fieldOffset - 30)});
        }
      @endif

      // Add inline errors to the DOM
      @if ($crud->inlineErrorsEnabled() && session()->get('errors'))

        window.errors = {!! json_encode(session()->get('errors')->getBags()) !!};

        $.each(errors, function(bag, errorMessages){
          $.each(errorMessages,  function (inputName, messages) {
            var normalizedProperty = inputName.split('.').map(function(item, index){
                    return index === 0 ? item : '['+item+']';
                }).join('');

            var field = $('[name="' + normalizedProperty + '[]"]').length ?
                        $('[name="' + normalizedProperty + '[]"]') :
                        $('[name="' + normalizedProperty + '"]'),
                        container = field.closest('.form-group');

            // iterate the inputs to add invalid classes to fields and red text to the field container.
            container.find('input, textarea, select').each(function() {
                let containerField = $(this);
                // add the invalid class to the field.
                containerField.addClass('is-invalid');
                // get field container
                let container = containerField.closest('.form-group');

                // TODO: `repeatable-group` should be deprecated in future version as a BC in favor of a more generic class `no-error-display`
                if(!container.hasClass('repeatable-group') && !container.hasClass('no-error-display')){
                  container.addClass('text-danger');
                }
            });

            $.each(messages, function(key, msg){
                // highlight the input that errored
                var row = $('<div class="invalid-feedback d-block">' + msg + '</div>');

                // TODO: `repeatable-group` should be deprecated in future version as a BC in favor of a more generic class `no-error-display`
                if(!container.hasClass('repeatable-group') && !container.hasClass('no-error-display')){
                  row.appendTo(container);
                }


                // highlight its parent tab
                @if ($crud->tabsEnabled())
                var tab_id = $(container).closest('[role="tabpanel"]').attr('id');
                $("#form_tabs [aria-controls="+tab_id+"]").addClass('text-danger');
                @endif
            });
        });
      });
      @endif

      $("a[data-bs-toggle='tab']").click(function(){
          currentTabName = $(this).attr('tab_name');
          $("input[name='_current_tab']").val(currentTabName);
      });

      if (window.location.hash) {
          $("input[name='_current_tab']").val(window.location.hash.substr(1));
      }
      });
    </script>

    @include('crud::inc.form_fields_script')
@endsection
