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
    'save_action_save_and_new' => 'Uložit a přidat nový',
    'save_action_save_and_edit' => 'Uložit a upravovat',
    'save_action_save_and_back' => 'Uložit a zpět',
    'save_action_changed_notification' => 'Výchozí chování po uložení bylo změněno.',

    // Create form
    'add' => 'Přidat',
    'back_to_all' => 'Zpět na všechny ',
    'cancel' => 'Zrušit',
    'add_a_new' => 'Přidat ',

    // Edit form
    'edit' => 'Upravit',
    'save' => 'Uložit',

    // Translatable models
    'edit_translations' => 'UPRAVIT PŘEKLADY',
    'language' => 'Jazyk',

    // CRUD table view
    'all' => 'Veškeré ',
    'in_the_database' => 'v databázi',
    'list' => 'Seznam',
    'actions' => 'Akce',
    'preview' => 'Zobrazit',
    'delete' => 'Smazat',
    'admin' => 'Admin',
    'details_row' => 'Toto je řádek podrobností. Změňte jej prosím.',
    'details_row_loading_error' => 'Při načítání podrobností došlo k chybě. Zkuste to prosím znovu.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Opravdu chcete smazat tuto položku?',
    'delete_confirmation_title' => 'Položka smazána',
    'delete_confirmation_message' => 'Položka byla úspěšně smazána.',
    'delete_confirmation_not_title' => 'Položka nebyla smazána',
    'delete_confirmation_not_message' => 'Došlo k chybě. Vaše položka pravděpodobně nebyla smazána.',
    'delete_confirmation_not_deleted_title' => 'Položka nebyla smazána',
    'delete_confirmation_not_deleted_message' => 'Nic se nestalo. Váše položka je v bezpečí.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Nebyly vybrány žádné položky',
    'bulk_no_entries_selected_message' => 'Vyberte jednu nebo více položek, abyste na nich provedli hromadnou akci.',

    // Bulk confirmation
    'bulk_delete_are_you_sure' => 'Opravdu chcete smazat těchto :number položek?',
    'bulk_delete_sucess_title' => 'Položky smazány',
    'bulk_delete_sucess_message' => ' položek bylo smazáno',
    'bulk_delete_error_title' => 'Smazání se nezdařilo',
    'bulk_delete_error_message' => 'Nelze smazat jednu nebo více položek.',

    // Ajax errors
    'ajax_error_title' => 'Chyba',
    'ajax_error_text' => 'Chyba při načítání stránky. Obnovte stránku.',

    // DataTables translation
    'emptyTable' => 'V tabulce nejsou k dispozici žádná data',
    'info' => 'Zobrazeno _START_ až _END_ z celkových _TOTAL_ záznamů',
    'infoEmpty' => '',
    'infoFiltered' => '(filtrováno z _MAX_ celkových záznamů)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ záznamů na stránku',
    'loadingRecords' => 'Načítání...',
    'processing' => 'Zpracování...',
    'search' => 'Hledat',
    'zeroRecords' => 'Nebyly nalezeny žádné odpovídající záznamy',
    'paginate' => [
        'first' => 'První',
        'last' => 'Poslední',
        'next' => 'Další',
        'previous' => 'Předchozí',
    ],
    'aria' => [
        'sortAscending' => ': aktivovat třídění sloupců vzestupně',
        'sortDescending' => ': aktivovat třídění sloupců sestupně',
    ],
    'export' => [
        'export' => 'Exportovat',
        'copy' => 'Kopírovat',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Tisknout',
        'column_visibility' => 'Zobrazené sloupce',
    ],

    // global crud - errors
    'unauthorized_access' => 'Neautorizovaný pristup - pro zobrazení této stránky nemáte potrebna opravneni.',
    'please_fix' => 'Prosím opravte následující chyby:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Položka byla úspěšně přidána.',
    'update_success' => 'Položka byla úspěšně upravena.',

    // CRUD reorder view
    'reorder' => 'Změna pořadí pro',
    'reorder_text' => 'Použijte drag & drop pro změnu pořadí',
    'reorder_success_title' => 'Uloženo',
    'reorder_success_message' => 'Vaše úpravy byly uloženy.',
    'reorder_error_title' => 'Chyba',
    'reorder_error_message' => 'Vaše úpravy nebyly uloženy.',

    // CRUD yes/no
    'yes' => 'Ano',
    'no' => 'Ne',

    // CRUD filters navbar view
    'filters' => 'Filtry',
    'toggle_filters' => 'Přepnout filtry',
    'remove_filters' => 'Odstranit filtry',

    // Fields
    'browse_uploads' => 'Procházet soubory',
    'select_all' => 'Vybrat vše',
    'select_files' => 'Vybrat soubory',
    'select_file' => 'Vybrat soubor',
    'clear' => 'Vyčistit',
    'page_link' => 'Odkaz na stránku',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Interní link',
    'internal_link_placeholder' => 'Interní slug. např.: \'admin/page\' (bez uvozovek) pro \':url\'',
    'external_link' => 'Externí odkaz',
    'choose_file' => 'Vybrat soubor',

    //Table field
    'table_cant_add' => 'Nelze přidat nové :entity',
    'table_max_reached' => 'Maximální počet :max byl dosáhnut',

    // File manager
    'file_manager' => 'Správce souborů',
];
