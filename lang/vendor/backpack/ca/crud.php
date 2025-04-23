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
    'save_action_save_and_new' => 'Desar i crear nou',
    'save_action_save_and_edit' => 'Desar i continuar editant',
    'save_action_save_and_back' => 'Desar i tornar',
    'save_action_save_and_preview' => 'Desar i vista prèvia',
    'save_action_changed_notification' => 'L\'acció per defecte del botó desar ha sigut modificada.',

    // Create form
    'add' => 'Afegir',
    'back_to_all' => 'Tornar al llistat de',
    'cancel' => 'Cancel·lar',
    'add_a_new' => 'Afegir ',

    // Edit form
    'edit' => 'Editar',
    'save' => 'Desar',

    // Translatable models
    'edit_translations' => 'EDITAR TRADUCCIONS',
    'language' => 'Idioma',

    // CRUD table view
    'all' => 'Tots els registres de ',
    'in_the_database' => 'a la base de dades',
    'list' => 'Llistar',
    'reset' => 'Reiniciar',
    'actions' => 'Accions',
    'preview' => 'Vista prèvia',
    'delete' => 'Eliminar',
    'admin' => 'Administrador',
    'details_row' => 'Aquesta és la fila de detalls. Modificar segons conveniència.',
    'details_row_loading_error' => 'S\'ha produït un error durant la càrrega de dades. Si us plau, intenti-ho de nou.',
    'clone' => 'Clonar',
    'clone_success' => '<strong>Element clonat</strong><br>S\'ha creat un nou element amb la mateixa información que aquest.',
    'clone_failure' => '<strong>La clonació ha fallat</strong><br>No s\'ha pogut crear el nou element. Intenti-ho de nou.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Està segur que desitja eliminar aquest element?',
    'delete_confirmation_title' => 'Element eliminat',
    'delete_confirmation_message' => 'L\'element ha sigut eliminat de manera correcta.',
    'delete_confirmation_not_title' => 'No s\'ha pogut eliminar',
    'delete_confirmation_not_message' => 'Ha ocorregut un error. És probable que l\'element no s\'hagi eliminat.',
    'delete_confirmation_not_deleted_title' => 'No s\'ha pogut eliminar',
    'delete_confirmation_not_deleted_message' => 'No ha ocorregut res. El seu element està segur.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'No hi ha registres seleccionats',
    'bulk_no_entries_selected_message' => 'Seleccioni un o més registres on aplicar l\'operació',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'Està segur que desitja eliminar :number registre/s?',
    'bulk_delete_sucess_title' => 'Registres eliminats',
    'bulk_delete_sucess_message' => ' registre/s eliminat/s',
    'bulk_delete_error_title' => 'Registres no eliminats',
    'bulk_delete_error_message' => 'No s\'han pogut eliminar un o més registres',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'Està segur que desitja clonar :number registre/s?',
    'bulk_clone_sucess_title' => 'Registres clonats',
    'bulk_clone_sucess_message' => ' registre/s clonat/s.',
    'bulk_clone_error_title' => 'Registres no clonats',
    'bulk_clone_error_message' => 'No s\'han pogut clonar una o més entrades. Intenti-ho de nou.',

    // Ajax errors
    'ajax_error_title' => 'Error',
    'ajax_error_text' => 'Error de càrrega de la pàgina. Si us plau, torni a carregar la pàgina.',

    // DataTables translation
    'emptyTable' => 'No hi ha dades disponibles a la taula',
    'info' => 'Mostrant registres _START_ a _END_ d\'un total de _TOTAL_ registres',
    'infoEmpty' => 'Sense entrades',
    'infoFiltered' => '(filtrant de _MAX_ registres totals)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ elements per pàgina',
    'loadingRecords' => 'Carregant...',
    'processing' => 'Processant...',
    'search' => 'Cercar',
    'zeroRecords' => 'No s\'han trobat elements',
    'paginate' => [
        'first' => 'Primer',
        'last' => 'Últim',
        'next' => 'Següent',
        'previous' => 'Anterior',
    ],
    'aria' => [
        'sortAscending' => ': activar per ordenar ascendentement',
        'sortDescending' => ': activar per ordenar descendentement',
    ],
    'export' => [
        'export' => 'Exportar',
        'copy' => 'Copiar',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Imprimir',
        'column_visibility' => 'Visibilitat de columnes',
    ],
    'custom_views' => [
        'title' => 'vistes personalitzades',
        'title_short' => 'vistes',
        'default' => 'per defecte',
    ],

    // global crud - errors
    'unauthorized_access' => 'Accés denegat - vostè no té els permisos necessaris per veure aquesta pàgina.',
    'please_fix' => 'Si us plau, corregeixi els següents errors:',

    // global crud - success / error notification bubbles
    'insert_success' => 'L\'element ha sigut afegit correctament.',
    'update_success' => 'L\'element ha sigut modificat correctament.',

    // CRUD reorder view
    'reorder' => 'Reordenar',
    'reorder_text' => 'Arrossegar i deixar anar per reordenar.',
    'reorder_success_title' => 'Fet',
    'reorder_success_message' => 'L\'ordre ha sigut desat.',
    'reorder_error_title' => 'Error',
    'reorder_error_message' => 'L\'ordre no s\'ha desat.',

    // CRUD yes/no
    'yes' => 'Sí',
    'no' => 'No',

    // CRUD filters navbar view
    'filters' => 'Filtres',
    'toggle_filters' => 'Alternar filtres',
    'remove_filters' => 'Eliminar filtres',
    'apply' => 'Aplicar',

    // filters language strings
    'today' => 'Avui',
    'yesterday' => 'Ahir',
    'last_7_days' => 'Darrers 7 dies',
    'last_30_days' => 'Darrers 30 dies',
    'this_month' => 'Aquest mes',
    'last_month' => 'El mes pasat',
    'custom_range' => 'Rang personalitzat',
    'weekLabel' => 'Setmana',

    // Fields
    'browse_uploads' => 'Pujar arxius',
    'select_all' => 'Seleccionar tot',
    'select_files' => 'Selecciona arxius',
    'select_file' => 'Selecciona un arxiu',
    'clear' => 'Netejar',
    'page_link' => 'Enllaç',
    'page_link_placeholder' => 'http://example.com/la-seva-pagina',
    'internal_link' => 'Enllaç intern',
    'internal_link_placeholder' => 'Slug intern. Exemple: \'admin/page\' (sense cometes) per \':url\'',
    'external_link' => 'Enllaç extern',
    'choose_file' => 'Triar arxiu',
    'new_item' => 'Nou element',
    'select_entry' => 'Selecciona un element',
    'select_entries' => 'Selecciona elements',
    'upload_multiple_files_selected' => 'Files selected. After save, they will show up above.',

    // Table field
    'table_cant_add' => 'No es pot afegir una nova :entity',
    'table_max_reached' => 'S\'ha arribat al nombre màxim de :max',

    // google_map
    'google_map_locate' => 'Obtenir la meva ubicació',

    // File manager
    'file_manager' => 'Administrador d\'arxius',

    // InlineCreateOperation
    'related_entry_created_success' => 'L\'element relacionat ha sigut creat i seleccionat.',
    'related_entry_created_error' => 'No s\'han pogut crear elements relacionats.',
    'inline_saving' => 'Guardant...',

    // returned when no translations found in select inputs
    'empty_translations' => '(buit)',

    // The pivot selector required validation message
    'pivot_selector_required_validation_message' => 'El camp dinàmic és obligatori.',
];
