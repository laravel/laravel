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
    'save_action_save_and_new' => 'Speichern und neu',
    'save_action_save_and_edit' => 'Speichern und bearbeiten',
    'save_action_save_and_back' => 'Speichern und zurück',
    'save_action_save_and_preview' => 'Speichern & Vorschau',
    'save_action_changed_notification' => 'Standardverhalten nach dem Speichern wurde geändert.',

    // Create form
    'add' => 'Anlegen: ',
    'back_to_all' => 'Zurück zur Übersicht ',
    'cancel' => 'Abbrechen',
    'add_a_new' => 'Neu anlegen: ',

    // Edit form
    'edit' => 'Bearbeiten',
    'save' => 'Speichern',

    // Translatable models
    'edit_translations' => 'ÜBERSETZUNG BEARBEITEN',
    'language' => 'Sprache',

    // CRUD table view
    'all' => 'Alle ',
    'in_the_database' => 'in der Datenbank',
    'list' => 'Liste',
    'reset' => 'Zurücksetzen',
    'actions' => 'Aktionen',
    'preview' => 'Vorschau',
    'delete' => 'Löschen',
    'admin' => 'Admin',
    'details_row' => 'Diese Zeile zeigt die Details und kann nach Belieben geändert werden.',
    'details_row_loading_error' => 'Beim Laden der Details ist ein Fehler aufgetreten. Bitte versuche es noch einmal.',
    'clone' => 'Duplizieren',
    'clone_success' => '<strong>Eintrag dupliziert</strong><br>Ein neuer Eintrag wurde mit den selben Informationen erstellt.',
    'clone_failure' => '<strong>Duplizieren fehlgeschlagen</strong><br>Der neue Eintrag konnte nicht erstellt werden. Bitte versuche es erneut.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Soll dieser Eintrag wirklich gelöscht werden?',
    'delete_confirmation_title' => 'Eintrag gelöscht',
    'delete_confirmation_message' => 'Der Eintrag wurde erfolgreich gelöscht.',
    'delete_confirmation_not_title' => 'NICHT gelöscht',
    'delete_confirmation_not_message' => 'Ein Fehler ist aufgetreten. Der Eintrag wurde möglicherweise nicht gelöscht.',
    'delete_confirmation_not_deleted_title' => 'Nicht gelöscht',
    'delete_confirmation_not_deleted_message' => 'Nichts passiert. Der Eintrag wurde nicht gelöscht. Bitte versuche es erneut.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Keine Einträge ausgewählt',
    'bulk_no_entries_selected_message' => 'Bitte wählen Sie einen oder mehrere Einträge aus, um eine Sammelaktion für diese durchzuführen.',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'Sind Sie sicher, dass Sie diese :number Einträge löschen wollen?',
    'bulk_delete_sucess_title' => 'Einträge gelöscht',
    'bulk_delete_sucess_message' => ' Einträge wurden gelöscht.',
    'bulk_delete_error_title' => 'Löschen fehlgeschlagen',
    'bulk_delete_error_message' => 'Ein oder mehrere Einträge konnten nicht gelöscht werden',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'Sind Sie sicher, dass Sie diese :number Einträge dupliziert wollen?',
    'bulk_clone_sucess_title' => 'Einträge dupliziert',
    'bulk_clone_sucess_message' => ' Einträge wurden dupliziert.',
    'bulk_clone_error_title' => 'Duplizierung fehlgeschlagen',
    'bulk_clone_error_message' => 'Ein oder mehrere Einträge konnten nicht dupliziert werden. Bitte versuche es erneut.',

    // Ajax errors
    'ajax_error_title' => 'Fehler',
    'ajax_error_text' => 'Fehler beim Laden der Seite. Bitte aktualisieren Sie die Seite.',

    // DataTables translation
    'emptyTable' => 'Keine Einträge vorhanden',
    'info' => 'Zeigt _START_ bis _END_ von _TOTAL_ Einträgen',
    'infoEmpty' => '',
    'infoFiltered' => '(gefiltert von insgesamt _MAX_ Einträgen)',
    'infoPostFix' => '.',
    'thousands' => '.',
    'lengthMenu' => '_MENU_ Einträge pro Seite',
    'loadingRecords' => 'Laden...',
    'processing' => 'Verarbeiten...',
    'search' => 'Suchen',
    'zeroRecords' => 'Keine passenden Einträge gefunden',
    'paginate' => [
        'first' => 'Erste',
        'last' => 'Letzte',
        'next' => 'Nächste',
        'previous' => 'Vorherige',
    ],
    'aria' => [
        'sortAscending' => ': aktivieren um aufsteigend zu Sortieren',
        'sortDescending' => ': aktivieren um absteigend zu Sortieren',
    ],
    'export' => [
        'export' => 'Export',
        'copy' => 'Kopieren',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Drucken',
        'column_visibility' => 'Sichtbarkeit der Spalte',
    ],

    // global crud - errors
    'unauthorized_access' => 'Unbefugter Zugriff - Sie haben nicht die notwendigen Rechte um diese Seite anzuzeigen.',
    'please_fix' => 'Bitte beheben Sie die folgenden Fehler:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Der Eintrag wurde erfolgreich angelegt.',
    'update_success' => 'Der Eintrag wurde erfolgreich geändert.',

    // CRUD reorder view
    'reorder' => 'Sortiere',
    'reorder_text' => 'Zum Ändern der Reihenfolge Einträge verschieben. (Drag&Drop)',
    'reorder_success_title' => 'Fertig',
    'reorder_success_message' => 'Die Reihenfolge wurde gespeichert',
    'reorder_error_title' => 'Fehler',
    'reorder_error_message' => 'Die Reihenfolge konnte nicht gespeichert werden',

    // CRUD yes/no
    'yes' => 'Ja',
    'no' => 'Nein',

    // CRUD filters navbar view
    'filters' => 'Filter',
    'toggle_filters' => 'Filter umschalten',
    'remove_filters' => 'Filter entfernen',
    'apply' => 'Anwenden',

    //filters language strings
    'today' => 'Heute',
    'yesterday' => 'Gestern',
    'last_7_days' => 'Letzte 7 Tage',
    'last_30_days' => 'Letzte 30 Tage',
    'this_month' => 'Dieser Monat',
    'last_month' => 'Letzter Monat',
    'custom_range' => 'Eigene Auswahl',
    'weekLabel' => 'W',

    // Fields
    'browse_uploads' => 'Uploads durchsuchen',
    'select_all' => 'Alle auswählen',
    'select_files' => 'Dateien auswählen',
    'select_file' => 'Datei auswählen',
    'clear' => 'Löschen',
    'page_link' => 'Link zur Seite',
    'page_link_placeholder' => 'http://example.com/ihre-gewuenschte-seite',
    'internal_link' => 'Interner Link',
    'internal_link_placeholder' => 'URL-sicherer Name ("Slug"). Bsp: \'admin/page\' (ohne Anführungszeichen) für \':url\'',
    'external_link' => 'Externer Link',
    'choose_file' => 'Datei auswählen',
    'new_item' => 'Neuer Eintrag',
    'select_entry' => 'Eintrag auswählen',
    'select_entries' => 'Einträge auswählen',
    'upload_multiple_files_selected' => 'Dateien ausgewählt. Nach dem Speichern werden sie oben angezeigt.',

    //Table field
    'table_cant_add' => 'Kann :entity nicht hinzufügen',
    'table_max_reached' => 'Maximale Anzahl von :max erreicht',

    // google_map
    'google_map_locate' => 'Meinen Standort ermitteln',

    // File manager
    'file_manager' => 'Datei-Manager',

    // InlineCreateOperation
    'related_entry_created_success' => 'Der zugehörige Eintrag wurde erstellt und ausgewählt.',
    'related_entry_created_error' => 'Es konnte kein verwandter Eintrag erstellt werden.',
    'inline_saving' => 'Speichern...',

    // returned when no translations found in select inputs
    'empty_translations' => '(empty)',

    // The pivot selector required validation message
    'pivot_selector_required_validation_message' => 'Das Pivot-Feld ist erforderlich.',
];
