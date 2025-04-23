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
    'save_action_save_and_new' => 'Salvează și adaugă o nouă intrare',
    'save_action_save_and_edit' => 'Salvează și editează intrarea',
    'save_action_save_and_back' => 'Salvează și mergi la listă',
    'save_action_save_and_preview' => 'Salvează și previzualizează',
    'save_action_changed_notification' => 'A fost salvată preferința redirecționării după salvare.',

    // Create form
    'add' => 'Adaugă',
    'back_to_all' => 'Înapoi la ',
    'cancel' => 'Anulează',
    'add_a_new' => 'Adaugă ',

    // Edit form
    'edit' => 'Editează',
    'save' => 'Salvează',

    // Translatable models
    'edit_translations' => 'EDITEAZĂ TRADUCERILE',
    'language' => 'Limbă',

    // CRUD table view
    'all' => 'Toate ',
    'reset' => 'Resetează ',
    'in_the_database' => 'din baza de date',
    'list' => 'Listă',
    'actions' => 'Operațiuni',
    'preview' => 'Previzualizare',
    'delete' => 'Șterge',
    'admin' => 'Administrator',
    'details_row' => 'Acesta este rândul detalii. Modifică cum dorești',
    'details_row_loading_error' => 'A apărut o eroare la încărcarea detaliilor. Te rog să reîncerci.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Ești sigur că vrei să ștergi această intrare?',
    'delete_confirmation_title' => 'Intrare ștearsă',
    'delete_confirmation_message' => 'Intrarea a fost ștearsă cu succes.',
    'delete_confirmation_not_title' => 'Eroare',
    'delete_confirmation_not_message' => 'A avut loc o eroare. E posibil ca intrarea să nu fi fost ștearsă.',
    'delete_confirmation_not_deleted_title' => 'Intrarea nu a fost ștearsă',
    'delete_confirmation_not_deleted_message' => 'Nu am șters intrarea din baza de date.',
    'ajax_error_title' => 'Eroare',
    'ajax_error_text' => 'Eroare la încărcarea paginii. Te rog să reîncarci pagina.',

    // DataTables translation
    'emptyTable' => 'Nu există intrări în baza de date',
    'info' => 'Sunt afișate intrările _START_-_END_ din _TOTAL_',
    'infoEmpty' => '',
    'infoFiltered' => '(filtrate din totalul de _MAX_ )',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ pe pagină',
    'loadingRecords' => 'Se încarcă...',
    'processing' => 'Se procesează...',
    'search' => 'Caută',
    'zeroRecords' => 'Nu au fost găsite intrări care să se potrivească',
    'paginate' => [
        'first' => 'Prima pagină',
        'last' => 'Ultima pagină',
        'next' => 'Pagina următoare',
        'previous' => 'Pagina anterioară',
    ],
    'aria' => [
        'sortAscending' => ': activează pentru a ordona ascendent coloana',
        'sortDescending' => ': activează pentru a ordona descendent coloana',
    ],
    'export' => [
        'pdf' => 'PDF',
        'print' => 'Imprimă',
        'column_visibility' => 'Vizibilitate coloane',
        'copy' => 'Copiere',
        'excel' => 'Fișier Excel',
        'csv' => 'Fișier CSV',
    ],

    // global crud - errors
    'unauthorized_access' => 'Acces neautorizat - Nu ai permisiunea necesară pentru a accesa pagina.',
    'please_fix' => 'Vă rugăm să reparați următoarele erori:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Intrarea a fost adăugată cu succes.',
    'update_success' => 'Intrarea a fost modificată cu succes.',

    // CRUD reorder view
    'reorder' => 'Reordonare',
    'reorder_text' => 'Folosește drag&drop pentru a reordona.',
    'reorder_success_title' => 'Terminat',
    'reorder_success_message' => 'Ordinea a fost salvată.',
    'reorder_error_title' => 'Eroare',
    'reorder_error_message' => 'Ordinea nu a fost salvată.',

    // CRUD yes/no
    'yes' => 'Da',
    'no' => 'Nu',

    // CRUD filters navbar view
    'filters' => 'Filtre',
    'toggle_filters' => 'Comutare filtre',
    'remove_filters' => 'Anulează filtre',

    // Fields
    'browse_uploads' => 'Alege din fișierele urcate',
    'select_all' => 'Selectează tot',
    'clear' => 'Curăță',
    'page_link' => 'Link către pagină',
    'page_link_placeholder' => 'http://example.com/pagina-dorita-de-tine',
    'internal_link' => 'Link intern',
    'internal_link_placeholder' => 'Rută internă. De ex: \'admin/page\' (fără ghilimele) pentru \':url\'',
    'external_link' => 'Link extern',
    'choose_file' => 'Alege fișier',

    //Table field
    'table_cant_add' => 'Nu pot adăuga o nouă :entity',
    'table_max_reached' => 'Numărul maxim :max a fost atins',

    // File manager
    'file_manager' => 'Manager fișiere',
];
