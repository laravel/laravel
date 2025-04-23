<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backpack Crud Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | Author: Frederik Rabøl Madsen <frederik-rm@hotmail.com>
    |
    */

    // Forms
    'save_action_save_and_new' => 'Gem og nyt emne',
    'save_action_save_and_edit' => 'Gem og rediger dette emne',
    'save_action_save_and_back' => 'Gem og tilbage til listevisning',
    'save_action_changed_notification' => 'standard handling efter gem er blevet ændret.',

    // Create form
    'add' => 'tilføj',
    'back_to_all' => 'tilbage til listevisning',
    'cancel' => 'annuller',
    'add_a_new' => 'tilføj ny ',

    // Edit form
    'edit' => 'Rediger',
    'save' => 'Gem',

    // Translatable models
    'edit_translations' => 'rediger oversættelser',
    'language' => 'Sprog',

    // CRUD table view
    'all' => 'Alle ',
    'in_the_database' => 'i databasen',
    'list' => 'Liste',
    'actions' => 'Handlinger',
    'preview' => 'Forhåndsvisning',
    'delete' => 'Slet',
    'admin' => 'Administrator',
    'details_row' => 'dette er detajle rækken. Ændrer som du har lyst.',
    'details_row_loading_error' => 'Der opstod en fejl under indlæsningen af detajlerne. Prøv igen.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Er du sikker på at du vil slette dette emne? ',
    'delete_confirmation_title' => 'Emne slettet',
    'delete_confirmation_message' => 'Emnet er blevet slettet successfuldt.',
    'delete_confirmation_not_title' => 'IKKE slettet',
    'delete_confirmation_not_message' => 'Der opstod en fejl. Dit emne er måske ikke fjernet.',
    'delete_confirmation_not_deleted_title' => 'ikke fjernet',
    'delete_confirmation_not_deleted_message' => 'Der skete intet. Dit emne er i god behold',

    // DataTables translation
    'emptyTable' => 'Ingen data tilgængelig',
    'info' => 'Viser fra _START_ til _END_ ud af _TOTAL_ emner',
    'infoEmpty' => '',
    'infoFiltered' => '(filtreret efter max _MAX_ emner)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ felter pr side',
    'loadingRecords' => 'Indlæser...',
    'processing' => 'Arbejder...',
    'search' => 'Søg',
    'zeroRecords' => 'Ingen emner blev fundet',
    'paginate' => [
        'first' => 'Første',
        'last' => 'Sidste',
        'next' => 'Næste',
        'previous' => 'Tidligere',
    ],
    'aria' => [
        'sortAscending' => ': aktiver for at sortere kolonen efter stigende rækkefølge',
        'sortDescending' => ': aktiver for at sortere kolonen efter faldende rækkefølge',
    ],

    // global crud - errors
    'unauthorized_access' => 'Ingen adgang - Du har ikke de nødvendige rettigheder for at se denne side.',
    'please_fix' => 'Ret venligst følgende fejl:',

    // global crud - success / error notification bubbles
    'insert_success' => 'emnet er tilføjet.',
    'update_success' => 'emner er ændret.',

    // CRUD reorder view
    'reorder' => 'skift rækkefølge',
    'reorder_text' => 'træk og slip for at skifte rækkefølge.',
    'reorder_success_title' => 'færdig',
    'reorder_success_message' => 'rækkefølgen er ændret.',
    'reorder_error_title' => 'fejl',
    'reorder_error_message' => 'rækkefølgen blev ikke gemt.',

    // CRUD yes/no
    'yes' => 'Ja',
    'no' => 'Nej',

    // CRUD filters navbar view
    'filters' => 'Filtre',
    'toggle_filters' => 'vis/skjul filtre',
    'remove_filters' => 'fjern filtre',

    // Fields
    'browse_uploads' => 'Se uploadede filer',
    'clear' => 'fjern',
    'page_link' => 'Side link',
    'page_link_placeholder' => 'http://eksempel.dk/din-oenskede-side',
    'internal_link' => 'Internt link',
    'internal_link_placeholder' => 'Interne slug. Ex: \'admin/side\' (no quotes) for \':url\'',
    'external_link' => 'Eksternt link',
    'choose_file' => 'Vælg fil',

    //Table field
    'table_cant_add' => 'Kan ikke tilføje ny :entity',
    'table_max_reached' => 'Maximum antal :max er nået',

    // File manager
    'file_manager' => 'Stifinder',
];
