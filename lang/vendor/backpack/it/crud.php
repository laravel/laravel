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
    'save_action_save_and_new' => 'Salva ed aggiungi un nuovo elemento',
    'save_action_save_and_edit' => 'Salva e modifica questo elemento',
    'save_action_save_and_back' => 'Salva e torna indietro',
    'save_action_save_and_preview' => 'Salva e vai all\'anteprima',
    'save_action_changed_notification' => 'Azione predefinita cambiata',

    // Create form
    'add' => 'Aggiungi',
    'back_to_all' => 'Torna alla lista di ',
    'cancel' => 'Annulla',
    'add_a_new' => 'Aggiungi nuovo/a ',

    // Edit form
    'edit' => 'Modifica',
    'save' => 'Salva',

    // Translatable models
    'edit_translations' => 'Modifica traduzioni',
    'language' => 'Lingua',

    // CRUD table view
    'all' => 'Tutti i ',
    'in_the_database' => 'nel database',
    'list' => 'Lista',
    'reset' => 'Reimposta',
    'actions' => 'Azioni',
    'preview' => 'Anteprima',
    'delete' => 'Elimina',
    'admin' => 'Amministrazione',
    'details_row' => 'Questa è la riga dei dettagli. Modificala a tuo piacimento.',
    'details_row_loading_error' => "C'è stato un errore caricando i dettagli. Riprova.",
    'clone' => 'Duplica',
    'clone_success' => '<strong>Elemento duplicato</strong><br>Un nuovo elemento è stato creato con le stesse informazioni di questo.',
    'clone_failure' => '<strong>Duplicazione fallita</strong><br>Il nuovo elemento non può essere creato. Per favore, riprova.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Sei sicuro di eliminare questo elemento?',
    'delete_confirmation_title' => 'Elemento eliminato',
    'delete_confirmation_message' => "L'elemento è stato eliminato con successo.",
    'delete_confirmation_not_title' => 'NON eliminato',
    'delete_confirmation_not_message' => "C'è stato un errore. L'elemento potrebbe non essere stato eliminato.",
    'delete_confirmation_not_deleted_title' => 'Non eliminato',
    'delete_confirmation_not_deleted_message' => "Non è successo niente. L'elemento è al sicuro.",

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Nessun record selezionato',
    'bulk_no_entries_selected_message' => 'Seleziona uno o più record su cui effettuare l\'operazione.',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'Sei sicuro di voler eliminare :number record?',
    'bulk_delete_sucess_title' => 'Record eliminati',
    'bulk_delete_sucess_message' => ' record sono stati eliminati',
    'bulk_delete_error_title' => 'Record non eliminati',
    'bulk_delete_error_message' => 'Non è stato possibile eliminare uno o più record',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'Sei sicuro di voler clonare :number record?',
    'bulk_clone_sucess_title' => 'Record clonati',
    'bulk_clone_sucess_message' => ' record sono stati clonati.',
    'bulk_clone_error_title' => 'Record non clonati',
    'bulk_clone_error_message' => 'Non è stato possibile clonare uno o più record. Per favore, riprova.',

    // Ajax errors
    'ajax_error_title' => 'Errore',
    'ajax_error_text' => 'Errore durante il caricamento della pagina. Per favore ricarica la pagina.',

    // DataTables translation
    'emptyTable' => 'Nessun record da visualizzare',
    'info' => 'Visualizzando da _START_ a _END_ record di _TOTAL_',
    'infoEmpty' => 'Non vi sono elementi',
    'infoFiltered' => '(filtrati da _MAX_ record totali)',
    'infoPostFix' => '.',
    'thousands' => '.',
    'lengthMenu' => '_MENU_ record per pagina',
    'loadingRecords' => 'Caricamento...',
    'processing' => 'Elaborazione...',
    'search' => 'Cerca',
    'zeroRecords' => 'Nessun record corrispondente',
    'paginate' => [
        'first' => 'Primo',
        'last' => 'Ultimo',
        'next' => 'Prossimo',
        'previous' => 'Precedente',
    ],
    'aria' => [
        'sortAscending' => ': attiva per ordinare la colonna ascendentemente',
        'sortDescending' => ': attiva per ordinare la colonna discendentemente',
    ],
    'export' => [
        'export' => 'Esporta',
        'copy' => 'Copia',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Stampa',
        'column_visibility' => 'Visibilità colonne',
    ],

    // global crud - errors
    'unauthorized_access' => 'Accesso non autorizzato - non hai i permessi necessari per vedere questa pagina.',
    'please_fix' => 'Per favore correggi i seguenti errori:',

    // global crud - success / error notification bubbles
    'insert_success' => "L'elemento è stato aggiunto correttamente.",
    'update_success' => "L'elemento è stato aggiornato correttamente.",

    // CRUD reorder view
    'reorder' => 'Riordina',
    'reorder_text' => 'Seleziona e trascina per riordinare.',
    'reorder_success_title' => 'Fatto',
    'reorder_success_message' => 'Il tuo ordinamento è stato salvato.',
    'reorder_error_title' => 'Errore',
    'reorder_error_message' => 'Il tuo ordinamento non è stato salvato.',

    // CRUD yes/no
    'yes' => 'Sì',
    'no' => 'No',

    // CRUD filters navbar view
    'filters' => 'Filtri',
    'toggle_filters' => 'Attiva/disattiva filtri',
    'remove_filters' => 'Rimuovi filtri',
    'apply' => 'Applica',

    //filters language strings
    'today' => 'Oggi',
    'yesterday' => 'Domani',
    'last_7_days' => 'Ultimi 7 giorni',
    'last_30_days' => 'Ultimi 30 giorni',
    'this_month' => 'Questo mese',
    'last_month' => 'Mese precedente',
    'custom_range' => 'Intervallo di date',
    'weekLabel' => 'W',

    // Fields
    'browse_uploads' => 'Sfoglia file caricati',
    'select_all' => 'Seleziona tutti',
    'select_files' => 'Seleziona i files',
    'select_file' => 'Seleziona un file',
    'clear' => 'Pulisci',
    'page_link' => 'Link Pagina',
    'page_link_placeholder' => 'http://esempio.com/pagina-desiderata',
    'internal_link' => 'Link Interno',
    'internal_link_placeholder' => 'Slug interno. Es: \'admin/page\' (no quotes) for \':url\'',
    'external_link' => 'Link Esterno',
    'choose_file' => 'Scegli file',
    'new_item' => 'Nuovo elemento',
    'select_entry' => 'Seleziona un elemento',
    'select_entries' => 'Select degli elementi',

    //Table field
    'table_cant_add' => 'Impossibile aggiungere una nuova :entity',
    'table_max_reached' => 'Numero massimo di :max raggiunto',

    // File manager
    'file_manager' => 'File Manager',

    // InlineCreateOperation
    'related_entry_created_success' => 'L\'elemento correlato è stato creato e selezionato.',
    'related_entry_created_error' => 'Non è possibile creare elementi correlati.',

    // returned when no translations found in select inputs
    'empty_translations' => '(nessuna voce)',

    // The pivot selector required validation message
    'pivot_selector_required_validation_message' => 'Il campo pivot è obbligatorio.',
];
