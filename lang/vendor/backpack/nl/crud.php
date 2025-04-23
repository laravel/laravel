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
    'save_action_save_and_new' => 'Opslaan en nieuw item toevoegen',
    'save_action_save_and_edit' => 'Opslaan en item bewerken',
    'save_action_save_and_back' => 'Opslaan en terug',
    'save_action_save_and_preview' => 'Opslaan en voorbeeld weergeven',
    'save_action_changed_notification' => 'Standaard gedrag na opslaan is aangepast.',

    // Create form
    'add' => 'Toevoegen',
    'back_to_all' => 'Terug naar alle items ',
    'cancel' => 'Annuleren',
    'add_a_new' => 'Nieuwe toevoegen ',

    // Edit form
    'edit' => 'Bewerken',
    'save' => 'Opslaan',

    // Translatable models
    'edit_translations' => 'Vertaling wijzigen',
    'language' => 'Taal',

    // CRUD table view
    'all' => 'Alles ',
    'in_the_database' => 'in de database',
    'list' => 'Lijst',
    'reset' => 'Reset',
    'actions' => 'Acties',
    'preview' => 'Voorbeeld',
    'delete' => 'Verwijderen',
    'admin' => 'Admin',
    'details_row' => 'Dit is de detail rij. Bewerk als gewenst.',
    'details_row_loading_error' => 'Er is een fout opgetreden bij het laden van de details. Probeer het opnieuw.',
    'clone' => 'Klonen',
    'clone_success' => '<strong>Item gekloond</strong><br>Een nieuw item is aangemaakt, met dezelfde eigenschappen als dit item.',
    'clone_failure' => '<strong>Klonen mislukt</strong><br>Er kon geen nieuw item worden aangemaakt. Probeer het opnieuw.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Weet je zeker dat je dit item wilt verwijderen?',
    'delete_confirmation_title' => 'Item verwijderd',
    'delete_confirmation_message' => 'Het item is succesvol verwijderd.',
    'delete_confirmation_not_title' => 'NIET verwijderd',
    'delete_confirmation_not_message' => 'Er is een fout opgetreden. Het item is misschien niet verwijderd.',
    'delete_confirmation_not_deleted_title' => 'Niet verwijderd',
    'delete_confirmation_not_deleted_message' => 'Er is niks gebeurd. Je item is veilig.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Geen items geselecteerd.',
    'bulk_no_entries_selected_message' => 'Selecteer tenminste een item om een bulkactie uit te voeren.',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'Weet je zeker dat je deze :number items wilt verwijderen?',
    'bulk_delete_sucess_title' => 'Items verwijderd',
    'bulk_delete_sucess_message' => ' items zijn verwijderd',
    'bulk_delete_error_title' => 'Verwijderen mislukt',
    'bulk_delete_error_message' => 'Een of meerdere items konden niet worden verwijderd',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'Weet je zeker dat je deze :number items wilt klonen?',
    'bulk_clone_sucess_title' => 'Items gekloond',
    'bulk_clone_sucess_message' => ' items zijn gekloond.',
    'bulk_clone_error_title' => 'Klonen mislukt',
    'bulk_clone_error_message' => 'Een of meerdere items konden niet worden gekloond. Probeer het opnieuw.',

    // Ajax errors
    'ajax_error_title' => 'Fout',
    'ajax_error_text' => 'Fout bij het laden. Vernieuw de pagina.',

    // DataTables translation
    'emptyTable' => 'Geen data beschikbaar in de tabel',
    'info' => 'Toon _START_ tot _END_ van _TOTAL_ items',
    'infoEmpty' => '',
    'infoFiltered' => '(gefilterd van _MAX_ totale items)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ items per pagina',
    'loadingRecords' => 'Laden...',
    'processing' => 'Verwerken...',
    'search' => 'Zoeken',
    'zeroRecords' => 'Geen overeenkomend item gevonden',
    'paginate' => [
        'first' => 'Eerste',
        'last' => 'Laatste',
        'next' => 'Volgende',
        'previous' => 'Vorige',
    ],
    'aria' => [
        'sortAscending' => ': activeer om kolom oplopend te sorteren',
        'sortDescending' => ': activeer om kolom aflopend te sorteren',
    ],
    'export' => [
        'export' => 'Exporteer',
        'copy' => 'Kopieer',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Print',
        'column_visibility' => 'Kolom zichtbaarheid',
    ],

    // global crud - errors
    'unauthorized_access' => 'Geen toegang - je hebt niet de benodigde rechten om deze pagina te bekijken.',
    'please_fix' => 'Los de volgende fouten op:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Het item is succesvol toegevoegd.',
    'update_success' => 'Het item is succesvol bewerkt.',

    // CRUD reorder view
    'reorder' => 'Rangschik',
    'reorder_text' => 'Gebruik drag&drop om te rangschikken.',
    'reorder_success_title' => 'Klaar',
    'reorder_success_message' => 'De rangschikking is opgeslagen.',
    'reorder_error_title' => 'Fout',
    'reorder_error_message' => 'De rangschikking is niet opgeslagen.',

    // CRUD yes/no
    'yes' => 'Ja',
    'no' => 'Nee',

    // CRUD filters navbar view
    'filters' => 'Filters',
    'toggle_filters' => 'Schakel filters',
    'remove_filters' => 'Verwijder filters',
    'apply' => 'Toepassen',

    //filters language strings
    'today' => 'Vandaag',
    'yesterday' => 'Gisteren',
    'last_7_days' => 'Afgelopen 7 dagen',
    'last_30_days' => 'Afgelopen 30 dagen',
    'this_month' => 'Deze maand',
    'last_month' => 'Afgelopen maand',
    'custom_range' => 'Aangepast bereik',
    'weekLabel' => 'W',

    // Fields
    'browse_uploads' => 'Blader uploads',
    'select_all' => 'Selecteer alles',
    'select_files' => 'Selecteer bestanden',
    'select_file' => 'Selecteer bestand',
    'clear' => 'Wissen',
    'page_link' => 'Pagina link',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Interne link',
    'internal_link_placeholder' => 'Interne slug. B.v.: \'admin/page\' (geen quotes) voor \':url\'',
    'external_link' => 'Externe link',
    'choose_file' => 'Kies bestand',
    'new_item' => 'Nieuw item',
    'select_entry' => 'Selecteer een item',
    'select_entries' => 'Selecteer items',

    //Table field
    'table_cant_add' => 'Kan nieuwe :entity niet toevoegen',
    'table_max_reached' => 'Maximale grootte van :max bereikt',

    // File manager
    'file_manager' => 'Bestandsbeheer',

    // InlineCreateOperation
    'related_entry_created_success' => 'Gerelateerd item is aangemaakt en geselecteerd.',
    'related_entry_created_error' => 'Gerelateerd item kon niet worden aangemaakt.',

    // returned when no translations found in select inputs
    'empty_translations' => '(leeg)',
];
