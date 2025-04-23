@if ($crud->exportButtons())
  @basset('https://cdn.datatables.net/buttons/2.3.3/css/buttons.bootstrap5.min.css')
  @basset('https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js')
  @basset('https://cdn.datatables.net/buttons/2.3.3/js/buttons.bootstrap5.min.js')
  @basset('https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js')
  @basset('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.18/pdfmake.min.js')
  @basset('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.18/vfs_fonts.js')
  @basset('https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js')
  @basset('https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js')
  @basset('https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js')
  <script>
    let dataTablesExportStrip = text => {
        if ( typeof text !== 'string' ) {
            return text;
        }

        return text
            .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
            .replace(/<!\-\-.*?\-\->/g, '')
            .replace(/<[^>]*>/g, '')
            .replace(/^\s+|\s+$/g, '')
            .replace(/\s+([,.;:!\?])/g, '$1')
            .replace(/\s+/g, ' ')
            .replace(/[\n|\r]/g, ' ');
    };

    let dataTablesExportFormat = {
        body: (data, row, column, node) => 
            node.querySelector('input[type*="text"]')?.value ??
            node.querySelector('input[type*="checkbox"]:not(.crud_bulk_actions_line_checkbox)')?.checked ??
            node.querySelector('select')?.selectedOptions[0]?.value ??
            dataTablesExportStrip(data),
    };

    window.crud.dataTableConfiguration.buttons = [
        @if($crud->get('list.showExportButton'))
        {
            extend: 'collection',
            text: '<i class="la la-download"></i> {{ trans('backpack::crud.export.export') }}',
            dropup: true,
            buttons: [
                {
                    name: 'copyHtml5',
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            var $column = crud.table.column( idx );
                                return  ($column.visible() && $(node).attr('data-visible-in-export') != 'false') || $(node).attr('data-force-export') == 'true';
                        },
                        format: dataTablesExportFormat,
                    },
                    action: function(e, dt, button, config) {
                        crud.responsiveToggle(dt);
                        $.fn.DataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                        crud.responsiveToggle(dt);
                    }
                },
                {
                    name: 'excelHtml5',
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            var $column = crud.table.column( idx );
                                return  ($column.visible() && $(node).attr('data-visible-in-export') != 'false') || $(node).attr('data-force-export') == 'true';
                        },
                        format: dataTablesExportFormat,
                    },
                    action: function(e, dt, button, config) {
                        crud.responsiveToggle(dt);
                        $.fn.DataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                        crud.responsiveToggle(dt);
                    }
                },
                {
                    name: 'csvHtml5',
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            var $column = crud.table.column( idx );
                                return  ($column.visible() && $(node).attr('data-visible-in-export') != 'false') || $(node).attr('data-force-export') == 'true';
                        },
                        format: dataTablesExportFormat,
                    },
                    action: function(e, dt, button, config) {
                        crud.responsiveToggle(dt);
                        $.fn.DataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                        crud.responsiveToggle(dt);
                    }
                },
                {
                    name: 'pdfHtml5',
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            var $column = crud.table.column( idx );
                                return  ($column.visible() && $(node).attr('data-visible-in-export') != 'false') || $(node).attr('data-force-export') == 'true';
                        },
                        format: dataTablesExportFormat,
                    },
                    orientation: 'landscape',
                    action: function(e, dt, button, config) {
                        crud.responsiveToggle(dt);
                        $.fn.DataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                        crud.responsiveToggle(dt);
                    }
                },
                {
                    name: 'print',
                    extend: 'print',
                    exportOptions: {
                        columns: function ( idx, data, node ) {
                            var $column = crud.table.column( idx );
                                return  ($column.visible() && $(node).attr('data-visible-in-export') != 'false') || $(node).attr('data-force-export') == 'true';
                        },
                        format: dataTablesExportFormat,
                    },
                    action: function(e, dt, button, config) {
                        crud.responsiveToggle(dt);
                        $.fn.DataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                        crud.responsiveToggle(dt);
                    }
                }
            ]
        }
        @endif
        @if($crud->get('list.showTableColumnPicker'))
        ,{
            extend: 'colvis',
            text: '<i class="la la-eye-slash"></i> {{ trans('backpack::crud.export.column_visibility') }}',
            columns: function ( idx, data, node ) {
                return $(node).attr('data-can-be-visible-in-table') == 'true';
            },
            dropup: true
        }
        @endif
    ];

    // move the datatable buttons in the top-right corner and make them smaller
    function moveExportButtonsToTopRight() {
      crud.table.buttons().each(function(button) {
        if (button.node.className.indexOf('buttons-columnVisibility') == -1 && button.node.nodeName=='BUTTON')
        {
          button.node.className = button.node.className + " btn-sm";
        }
      })
      $(".dt-buttons").appendTo($('#datatable_button_stack' ));
      $('.dt-buttons').addClass('d-xs-block')
                      .addClass('d-sm-inline-block')
                      .addClass('d-md-inline-block')
                      .addClass('d-lg-inline-block');
    }

    crud.addFunctionToDataTablesDrawEventQueue('moveExportButtonsToTopRight');
  </script>
@endif
