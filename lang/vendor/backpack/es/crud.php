<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backpack Crud Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    // Forms
    'save_action_save_and_new' => 'Guardar y crear nuevo',
    'save_action_save_and_edit' => 'Guardar y continuar editando',
    'save_action_save_and_back' => 'Guardar y regresar',
    'save_action_save_and_preview' => 'Guardar y vista previa',
    'save_action_changed_notification' => 'La acción por defecto del botón guardar ha sido modificada.',

    // Create form
    'add' => 'Añadir',
    'back_to_all' => 'Volver al listado de',
    'cancel' => 'Cancelar',
    'add_a_new' => 'Añadir ',

    // Edit form
    'edit' => 'Editar',
    'save' => 'Guardar',

    // Translatable models
    'edit_translations' => 'EDITAR TRADUCCIONES',
    'language' => 'Idioma',

    // CRUD table view
    'all' => 'Todos los registros de ',
    'in_the_database' => 'en la base de datos',
    'list' => 'Listar',
    'reset' => 'Reiniciar',
    'actions' => 'Acciones',
    'preview' => 'Vista previa',
    'delete' => 'Eliminar',
    'admin' => 'Admin',
    'details_row' => 'Esta es la fila de detalles. Modificar a su gusto.',
    'details_row_loading_error' => 'Se ha producido un error al cargar los datos. Por favor, intente de nuevo.',
    'clone' => 'Clonar',
    'clone_success' => '<strong>Elemento clonado</strong><br>Se creó un nuevo elemento con la misma información que este.',
    'clone_failure' => '<strong>La clonación falló</strong><br>No se puede crear el nuevo elemento. Inténtalo de nuevo.',

    // Confirmation messages and bubbles
    'delete_confirm' => '¿Está seguro que desea eliminar este elemento?',
    'delete_confirmation_title' => 'Elemento eliminado',
    'delete_confirmation_message' => 'El elemento ha sido eliminado de manera correcta.',
    'delete_confirmation_not_title' => 'No se pudo eliminar',
    'delete_confirmation_not_message' => 'Ha ocurrido un error. Puede que el elemento no haya sido eliminado.',
    'delete_confirmation_not_deleted_title' => 'No se pudo eliminar',
    'delete_confirmation_not_deleted_message' => 'No ha ocurrido nada. Su elemento está seguro.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'No hay registros seleccionados',
    'bulk_no_entries_selected_message' => 'Seleccione uno o más registros en los que realizar la operación',

    // Bulk delete
    'bulk_delete_are_you_sure' => '¿Estás seguro de que deseas eliminar :number registro/s?',
    'bulk_delete_sucess_title' => 'Registros eliminados',
    'bulk_delete_sucess_message' => ' los registros han sido eliminados',
    'bulk_delete_error_title' => 'Registros no eliminados',
    'bulk_delete_error_message' => 'No se pudieron eliminar uno o más registros',

    // Bulk clone
    'bulk_clone_are_you_sure' => '¿Está seguro de que desea clonar :number registro/s?',
    'bulk_clone_sucess_title' => 'Registros clonados',
    'bulk_clone_sucess_message' => ' registros han sido clonados.',
    'bulk_clone_error_title' => 'Registros no clonados',
    'bulk_clone_error_message' => 'No se pudieron crear una o más entradas. Inténtalo de nuevo.',

    // Ajax errors
    'ajax_error_title' => 'Error',
    'ajax_error_text' => 'Error al cargar la página. Por favor, vuelva a cargar la página.',

    // DataTables translation
    'emptyTable' => 'No hay datos disponibles en la tabla',
    'info' => 'Mostrando registros _START_ a _END_ de un total de _TOTAL_ registros',
    'infoEmpty' => '',
    'infoFiltered' => '(filtrando de _MAX_ registros totales)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ elementos por página',
    'loadingRecords' => 'Cargando...',
    'processing' => 'Procesando...',
    'search' => 'Buscar',
    'zeroRecords' => 'No se encontraron elementos',
    'paginate' => [
        'first' => 'Primero',
        'last' => 'Último',
        'next' => 'Siguiente',
        'previous' => 'Anterior',
    ],
    'aria' => [
        'sortAscending' => ': activar para ordenar ascendentemente',
        'sortDescending' => ': activar para ordenar descendentemente',
    ],
    'export' => [
        'export' => 'Exportar',
        'copy' => 'Copiar',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Imprimir',
        'column_visibility' => 'Visibilidad de columnas',
    ],
    'custom_views' => [
        'title' => 'vistas personalizadas',
        'title_short' => 'vistas',
        'default' => 'por defecto',
    ],

    // global crud - errors
    'unauthorized_access' => 'Acceso denegado - usted no tiene los permisos necesarios para ver esta página.',
    'please_fix' => 'Por favor corrija los siguientes errores:',

    // global crud - success / error notification bubbles
    'insert_success' => 'El elemento ha sido añadido de manera correcta.',
    'update_success' => 'El elemento ha sido modificado de manera correcta.',

    // CRUD reorder view
    'reorder' => 'Reordenar',
    'reorder_text' => 'Arrastrar y soltar para reordenar.',
    'reorder_success_title' => 'Hecho',
    'reorder_success_message' => 'El orden ha sido guardado.',
    'reorder_error_title' => 'Error',
    'reorder_error_message' => 'El orden no se ha guardado.',

    // CRUD yes/no
    'yes' => 'Sí',
    'no' => 'No',

    // CRUD filters navbar view
    'filters' => 'Filtros',
    'toggle_filters' => 'Alternar filtros',
    'remove_filters' => 'Remover filtros',
    'apply' => 'Apply',

    // filters language strings
    'today' => 'Hoy',
    'yesterday' => 'Ayer',
    'last_7_days' => 'Los últimos 7 días',
    'last_30_days' => 'Últimos 30 días',
    'this_month' => 'Este mes',
    'last_month' => 'El mes pasado',
    'custom_range' => 'Rango personalizado',
    'weekLabel' => 'W',

    // Fields
    'browse_uploads' => 'Subir archivos',
    'select_all' => 'Seleccionar todo',
    'select_files' => 'Selecciona archivos',
    'select_file' => 'Selecciona un archivo',
    'clear' => 'Limpiar',
    'page_link' => 'Enlace',
    'page_link_placeholder' => 'http://example.com/su-pagina',
    'internal_link' => 'Enlace interno',
    'internal_link_placeholder' => 'Slug interno. Ejplo: \'admin/page\' (sin comillas) para \':url\'',
    'external_link' => 'Enlace externo',
    'choose_file' => 'Elegir archivo',
    'new_item' => 'Nuevo elemento',
    'select_entry' => 'Selecciona un elemento',
    'select_entries' => 'Selecciona elementos',

    // Table field
    'table_cant_add' => 'No se puede agregar una nueva :entity',
    'table_max_reached' => 'El número máximo de :max alcanzado',

    // google_map
    'google_map_locate' => 'Obtener mi ubicación',

    // File manager
    'file_manager' => 'Administrador de archivos',

    // InlineCreateOperation
    'related_entry_created_success' => 'El elemento relacionado ha sido creado y seleccionado.',
    'related_entry_created_error' => 'No se pueden crear elementos relacionados.',
    'inline_saving' => 'Guardando...',

    // returned when no translations found in select inputs
    'empty_translations' => '(vacío)',

    // The pivot selector required validation message
    'pivot_selector_required_validation_message' => 'El campo dinámico es obligatorio.',
];
