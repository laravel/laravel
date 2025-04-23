@php
    $field['wrapper'] = $field['wrapper'] ?? $field['wrapperAttributes'] ?? [];
    $field['wrapper']['data-init-function'] = $field['wrapper']['data-init-function'] ?? 'bpFieldInitUploadElement';
    $field['wrapper']['data-field-name'] = $field['wrapper']['data-field-name'] ?? $field['name'];
@endphp

{{-- text input --}}
@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')

	{{-- Show the file name and a "Clear" button on EDIT form. --}}
    @if (!empty($field['value']))
    <div class="existing-file">
        @if (isset($field['disk']))
        @if (isset($field['temporary']))
            <a target="_blank" href="{{ (asset(\Storage::disk($field['disk'])->temporaryUrl(Arr::get($field, 'prefix', '').$field['value'], Carbon\Carbon::now()->addMinutes($field['temporary'])))) }}">
        @else
            <a target="_blank" href="{{ (asset(\Storage::disk($field['disk'])->url(Arr::get($field, 'prefix', '').$field['value']))) }}">
        @endif
        @else
            <a target="_blank" href="{{ (asset(Arr::get($field, 'prefix', '').$field['value'])) }}">
        @endif
            {{ $field['value'] }}
        </a>
    	<a href="#" class="file_clear_button btn btn-light btn-sm float-right" title="Clear file" data-filename="{{ $field['value'] }}"><i class="la la-remove"></i></a>
    	<div class="clearfix"></div>
    </div>
    @endif

	{{-- Show the file picker on CREATE form. --}}
    <div class="backstrap-file {{ isset($field['value']) && $field['value']!=null?'d-none':'' }}">
        <input
            type="file"
            name="{{ $field['name'] }}"
            data-filename="{{ $field['value'] ?? '' }}"
            @include('crud::fields.inc.attributes', ['default_class' => 'file_input backstrap-file-input'])
        >
        <label class="backstrap-file-label" for="customFile"></label>
    </div>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')



{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}

@push('crud_fields_styles')
  @bassetBlock('backpack/crud/fields/upload-field.css')
    <style type="text/css">
        .existing-file {
            border: 1px solid rgba(0,40,100,.12);
            border-radius: 5px;
            padding-left: 10px;
            vertical-align: middle;
        }
        .existing-file a {
            padding-top: 5px;
            display: inline-block;
            font-size: 0.9em;
        }
        .backstrap-file {
          position: relative;
          display: inline-block;
          width: 100%;
          height: calc(1.5em + 0.75rem + 2px);
          margin-bottom: 0;
        }

        .backstrap-file-input {
          position: relative;
          z-index: 2;
          width: 100%;
          height: calc(1.5em + 0.75rem + 2px);
          margin: 0;
          opacity: 0;
        }

        .backstrap-file-input:focus ~ .backstrap-file-label {
          border-color: #acc5ea;
          box-shadow: 0 0 0 0rem rgba(70, 127, 208, 0.25);
        }

        .backstrap-file-input:disabled ~ .backstrap-file-label {
          background-color: #e4e7ea;
        }

        .backstrap-file-input:lang(en) ~ .backstrap-file-label::after {
          content: "Browse";
        }

        .backstrap-file-input ~ .backstrap-file-label[data-browse]::after {
          content: attr(data-browse);
        }

        .backstrap-file-label {
          position: absolute;
          top: 0;
          right: 0;
          left: 0;
          z-index: 1;
          height: calc(1.5em + 0.75rem + 2px);
          padding: 0.375rem 0.75rem;
          font-weight: 400;
          line-height: 1.5;
          color: #5c6873;
          background-color: #fff;
          border: 1px solid #e4e7ea;
          border-radius: 0.25rem;
          font-weight: 400!important;
        }

        .backstrap-file-label::after {
          position: absolute;
          top: 0;
          right: 0;
          bottom: 0;
          z-index: 3;
          display: block;
          height: calc(1.5em + 0.75rem);
          padding: 0.375rem 0.75rem;
          line-height: 1.5;
          color: #5c6873;
          content: "Browse";
          background-color: #f0f3f9;
          border-left: inherit;
          border-radius: 0 0.25rem 0.25rem 0;
        }
    </style>
  @endBassetBlock
@endpush

@push('crud_fields_scripts')
  @bassetBlock('backpack/crud/fields/upload-field.js')
    <script>
        function bpFieldInitUploadElement(element) {
            var fileInput = element.find(".file_input");
            var fileClearButton = element.find(".file_clear_button");
            var fieldName = element.attr('data-field-name');
            var inputWrapper = element.find(".backstrap-file");
            var inputLabel = element.find(".backstrap-file-label");

            if(fileInput.attr('data-row-number')) {
              $('<input type="hidden" class="order_uploads" name="_order_'+fieldName+'" value="'+fileInput.data('filename')+'">').insertAfter(fileInput);

              var observer = new MutationObserver(function(mutations) {
                
                mutations.forEach(function(mutation) {
                  if(mutation.attributeName == 'data-row-number') {                    
                    let field = $(mutation.target);
                    field = field.next('input[name="'+mutation.target.getAttribute('name')+'"]');
                    field.attr('name', '_order_'+mutation.target.getAttribute('name'));
                    field.val(mutation.target.getAttribute('data-filename'));                  
                  }
                });
              });

              observer.observe(fileInput[0], {
                attributes: true,
              });
            }

            fileClearButton.click(function(e) {
                e.preventDefault();
                $(this).parent().addClass('d-none');
                // if the file input has a data-row-number attribute, it means it's inside a repeatable field
                // in that case, will send the value of the cleared input to the server
                if(fileInput.attr('data-row-number')) {
                  $("<input type='hidden' name='_clear_"+fieldName+"' value='"+fileInput.data('filename')+"'>").insertAfter(fileInput);
                  fileInput.siblings('.order_uploads').remove();
                }
                fileInput.parent().removeClass('d-none');
                fileInput.attr("value", "").replaceWith(fileInput.clone(true));

                // redo the selector, so we can use the same fileInput variable going forward
                fileInput = element.find(".file_input");

                // add a hidden input with the same name, so that the setXAttribute method is triggered
                $("<input type='hidden' name='"+fieldName+"' value=''>").insertAfter(fileInput);
            });

            fileInput.change(function() {
                var path = $(this).val();
                var path = path.replace("C:\\fakepath\\", "");
                inputLabel.html(path);
                // remove the hidden input
                $(this).next("input[type=hidden]").remove();
            });

            element.on('CrudField:disable', function(e) {
              element.children('.backstrap-file').find('input').prop('disabled', 'disabled');

              let $deleteButton = element.children('.existing-file').children('a.file_clear_button');

              if($deleteButton.length > 0) {
                $deleteButton.on('click.prevent', function(e) {
                    e.stopImmediatePropagation();
                    return false;
                  });
                  // make the event we just registered, the first to be triggered
                  $._data($deleteButton.get(0), "events").click.reverse();
              }
          });

          element.on('CrudField:enable', function(e) {
            element.children('.backstrap-file').find('input').removeAttr('disabled');
            element.children('.existing-file').children('a.file_clear_button').unbind('click.prevent');
          });

        }
    </script>
  @endBassetBlock
@endpush
