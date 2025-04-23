{{-- summernote editor --}}
@php
    // make sure that the options array is defined
    // and at the very least, dialogsInBody is true;
    // that's needed for modals to show above the overlay in Bootstrap 4
    $field['options'] = array_merge(['dialogsInBody' => true, 'tooltip' => false], $field['options'] ?? []);
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')
    <textarea
        name="{{ $field['name'] }}"
        data-init-function="bpFieldInitSummernoteElement"
        data-options="{{ json_encode($field['options']) }}"
        bp-field-main-input
        @include('crud::fields.inc.attributes', ['default_class' =>  'form-control summernote'])
        >{{ old_empty_or_null($field['name'], '') ??  $field['value'] ?? $field['default'] ?? '' }}</textarea>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}

{{-- FIELD CSS - will be loaded in the after_styles section --}}
@push('crud_fields_styles')
    {{-- include summernote css --}}
    @basset('https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css')
    @basset('https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/font/summernote.woff2', false)
    @bassetBlock('backpack/crud/fields/summernote-field.css')
    <style type="text/css">
        .note-editor.note-frame .note-status-output, .note-editor.note-airframe .note-status-output {
                height: auto;
        }
    </style>
    @endBassetBlock
@endpush

{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('crud_fields_scripts')
    {{-- include summernote js --}}
    @basset('https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js')
    @bassetBlock('backpack/crud/fields/summernote-field.js')
    <script>
        function bpFieldInitSummernoteElement(element) {
             var summernoteOptions = element.data('options');

            let summernotCallbacks = {
                onChange: function(contents, $editable) {
                    element.val(contents).trigger('change');
                }
            }

            element.on('CrudField:disable', function(e) {
                element.summernote('disable');
            });

            element.on('CrudField:enable', function(e) {
                element.summernote('enable');
            });

            summernoteOptions['callbacks'] = summernotCallbacks;

            element.summernote(summernoteOptions);
        }
    </script>
    @endBassetBlock
@endpush

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
