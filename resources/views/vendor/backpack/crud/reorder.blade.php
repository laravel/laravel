@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack::crud.reorder') => false,
    ];

    $columns = $crud->getOperationSetting('reorderColumnNames');

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex align-items-baseline d-print-none" bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">{!! $crud->getSubheading() ?? trans('backpack::crud.reorder').' '.$crud->entity_name_plural !!}</p>
        @if ($crud->hasAccess('list'))
            <p class="ms-2 ml-2 mb-0" bp-section="page-subheading-back-button">
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            </p>
        @endif
    </section>
@endsection

@section('content')
<?php
if(!function_exists('tree_element')) {
    function tree_element($entry, $key, $all_entries, $crud) {
        $columns = $crud->getOperationSetting('reorderColumnNames');

        if (! isset($entry->tree_element_shown)) {
            // mark the element as shown
            $all_entries[$key]->tree_element_shown = true;
            $entry->tree_element_shown = true;
            $entryLabel = $crud->get('reorder.escaped') ? e(object_get($entry, $crud->get('reorder.label'))) : object_get($entry, $crud->get('reorder.label'));
            // show the tree element
            echo '<li id="list_'.$entry->getKey().'">';
            echo '<div><span class="disclose"><span></span></span>'.$entryLabel.'</div>';

            // see if this element has any children
            $children = [];
            foreach ($all_entries as $key => $subentry) {
                if ($subentry->{$columns['parent_id']} == $entry->getKey()) {
                    $children[] = $subentry;
                }
            }

            $children = collect($children)->sortBy($columns['lft']);

            // if it does have children, show them
            if (count($children)) {
                echo '<ol>';
                foreach ($children as $key => $child) {
                    $children[$key] = tree_element($child, $child->getKey(), $all_entries, $crud);
                }
                echo '</ol>';
            }
            echo '</li>';
        }

        return $entry;
    }
}

    ?>

    <div class="row mt-4" bp-section="crud-operation-reorder">
        <div class="{{ $crud->getReorderContentClass() }}">
            <div class="card p-4">
                <p>{{ trans('backpack::crud.reorder_text') }}</p>

                <ol class="sortable mt-0 mb-0">
                <?php
                    $all_entries = collect($entries->all())->sortBy($columns['lft'])->keyBy($crud->getModel()->getKeyName());
                    $root_entries = $all_entries->filter(function ($item) use ($columns) {
                        return $item->{$columns['parent_id']} == 0;
                    });
                    foreach ($root_entries as $key => $entry) {
                        $root_entries[$key] = tree_element($entry, $key, $all_entries, $crud);
                    }
                ?>
                </ol>

            </div>{{-- /.card --}}

            <div class="mt-3">
                <button id="toArray" class="btn btn-success text-light" data-style="zoom-in"><i class="la la-save"></i> {{ trans('backpack::crud.save') }}</button>
                <a href="{{ $crud->hasAccess('list') ? url($crud->route) : url()->previous() }}" class="btn btn-secondary text-decoration-none"><span class="la la-ban"></span> &nbsp;{{ trans('backpack::crud.cancel') }}</a>
            </div>

        </div>
    </div>
@endsection


@section('after_styles')
    <style>
        .ui-sortable .placeholder {
            outline: 1px dashed #4183C4;
            /*-webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            margin: -1px;*/
        }

        .ui-sortable .mjs-nestedSortable-error {
            background: #fbe3e4;
            border-color: transparent;
        }

        .ui-sortable ol {
            margin: 0;
            padding: 0;
            padding-left: 30px;
        }

        ol.sortable, ol.sortable ol {
            margin: 0 0 0 25px;
            padding: 0;
            list-style-type: none;
        }

        ol.sortable {
            margin: 2em 0;
        }

        .sortable li {
            margin: 5px 0 0 0;
            padding: 0;
        }

        .sortable li div  {
            border: 1px solid #ddd;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            padding: 6px;
            margin: 0;
            cursor: move;
            background-color: #f4f4f4;
            color: #444;
            border-color: #00acd6;
        }

        .sortable li.mjs-nestedSortable-branch div {
            /*background-color: #00c0ef;*/
            /*border-color: #00acd6;*/
        }

        .sortable li.mjs-nestedSortable-leaf div {
            /*background-color: #00c0ef;*/
            border: 1px solid #ddd;
        }

        li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div {
            border-color: #999;
            background: #fafafa;
        }

        .ui-sortable .disclose {
            cursor: pointer;
            width: 10px;
            display: none;
        }

        .sortable li.mjs-nestedSortable-collapsed > ol {
            display: none;
        }

        .sortable li.mjs-nestedSortable-branch > div > .disclose {
            display: inline-block;
        }

        .sortable li.mjs-nestedSortable-collapsed > div > .disclose > span:before {
            content: '+ ';
        }

        .sortable li.mjs-nestedSortable-expanded > div > .disclose > span:before {
            content: '- ';
        }

        .ui-sortable h1 {
            font-size: 2em;
            margin-bottom: 0;
        }

        .ui-sortable h2 {
            font-size: 1.2em;
            font-weight: normal;
            font-style: italic;
            margin-top: .2em;
            margin-bottom: 1.5em;
        }

        .ui-sortable h3 {
            font-size: 1em;
            margin: 1em 0 .3em;;
        }

        .ui-sortable p, .ui-sortable ol, .ui-sortable ul, .ui-sortable pre, .ui-sortable form {
            margin-top: 0;
            margin-bottom: 1em;
        }

        .ui-sortable dl {
            margin: 0;
        }

        .ui-sortable dd {
            margin: 0;
            padding: 0 0 0 1.5em;
        }

        .ui-sortable code {
            background: #e5e5e5;
        }

        .ui-sortable input {
            vertical-align: text-bottom;
        }

        .ui-sortable .notice {
            color: #c33;
        }
    </style>
@endsection

@section('after_scripts')
    @basset('https://cdn.jsdelivr.net/npm/jquery-ui@1.13.2/dist/jquery-ui.min.js')
    @basset(base_path('vendor/backpack/crud/src/resources/assets/libs/jquery.mjs.nestedSortable2.js'))

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var isRtl = Boolean("{{ (config('backpack.base.html_direction') === 'rtl') ? true : false }}");
            if(isRtl) {
                $( " <style> .ui-sortable ol {margin: 0;padding: 0;padding-right: 30px;}ol.sortable, ol.sortable ol {margin: 0 25px 0 0;padding: 0;list-style-type: none;}.ui-sortable dd {margin: 0;padding: 0 1.5em 0 0;}</style>" ).appendTo( "head" )
            }
            // initialize the nested sortable plugin
            $('.sortable').nestedSortable({
                forcePlaceholderSize: true,
                handle: 'div',
                helper: 'clone',
                items: 'li',
                opacity: .6,
                placeholder: 'placeholder',
                revert: 250,
                tabSize: 25,
                rtl: isRtl,
                tolerance: 'pointer',
                toleranceElement: '> div',
                maxLevels: {{ $crud->get('reorder.max_level') ?? 3 }},
                isTree: true,
                expandOnHover: 700,
                startCollapsed: false
            });

            $('.disclose').on('click', function() {
                $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
            });

            $('#toArray').click(function(e){
                // get the current tree order
                arraied = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0, expression: /(.+)_(.+)/ });

                // log it
                //console.log(arraied);

                // send it with POST
                $.ajax({
                    url: '{{ url(Request::path()) }}',
                    type: 'POST',
                    data: { tree: JSON.stringify(arraied) },
                })
                    .done(function() {
                        new Noty({
                            type: "success",
                            text: "<strong>{{ trans('backpack::crud.reorder_success_title') }}</strong><br>{{ trans('backpack::crud.reorder_success_message') }}"
                        }).show();
                    })
                    .fail(function() {
                        new Noty({
                            type: "error",
                            text: "<strong>{{ trans('backpack::crud.reorder_error_title') }}</strong><br>{{ trans('backpack::crud.reorder_error_message') }}"
                        }).show();
                    })
                    .always(function() {
                        console.log("complete");
                    });

            });

            $.ajaxPrefilter(function(options, originalOptions, xhr) {
                var token = $('meta[name="csrf_token"]').attr('content');

                if (token) {
                    return xhr.setRequestHeader('X-XSRF-TOKEN', token);
                }
            });

        });
    </script>
@endsection
