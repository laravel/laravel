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
    'save_action_save_and_new' => 'Mentés és új létrehozása',
    'save_action_save_and_edit' => 'Mentés és szerkesztés',
    'save_action_save_and_back' => 'Mentés és visszalépés',
    'save_action_save_and_preview' => 'Mentés és előnézet',
    'save_action_changed_notification' => 'A mentés utáni alapértelmezett viselkedés megváltozott.',

    // Create form
    'add' => 'Hozzáadás',
    'back_to_all' => 'Vissza ',
    'cancel' => 'Mégsem',
    'add_a_new' => 'Új ',

    // Edit form
    'edit' => 'Szerkesztés',
    'save' => 'Mentés',

    // Translatable models
    'edit_translations' => 'Fordítások',
    'language' => 'Nyelv',

    // CRUD table view
    'all' => 'Összes ',
    'in_the_database' => 'az adatbázisban',
    'list' => 'Listázás',
    'reset' => 'Újratöltés',
    'actions' => 'Műveletek',
    'preview' => 'Előnézet',
    'delete' => 'Törlés',
    'admin' => 'Admin',
    'details_row' => 'Az a részlet sor. Módosítsd, ahogyan szeretnéd.',
    'details_row_loading_error' => 'Hiba történt a részletek betöltésekor. Kérlek próbáld újra!',
    'clone' => 'Klónozás',
    'clone_success' => '<strong>Sikeres klónozás</strong><br>Létrejött az új elem ugyanezekkel az információkkal.',
    'clone_failure' => '<strong>Sikertelen klónozás</strong><br>Nem sikerült létrehozni az új elemet. Kérlek próbáld újra!',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Biztos, hogy törölni szeretnéd ez az elemet?',
    'delete_confirmation_title' => 'Törölve',
    'delete_confirmation_message' => 'Az elemet sikeresen töröltük.',
    'delete_confirmation_not_title' => 'NEM sikerült a törlés',
    'delete_confirmation_not_message' => 'Valami hiba történt, az elemet nem sikerült törölni.',
    'delete_confirmation_not_deleted_title' => 'Nem törlődött',
    'delete_confirmation_not_deleted_message' => 'Nem történt semmi. Az elem biztonságban van.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Nincs kiválasztott elem.',
    'bulk_no_entries_selected_message' => 'Kérlek válassz ki egy vagy több elemet a csoportos művelethez!',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'Biztos, hogy törölni szeretnéd ezt a :number elemet?',
    'bulk_delete_sucess_title' => 'Elemek törölve',
    'bulk_delete_sucess_message' => ' elem törölve lett.',
    'bulk_delete_error_title' => 'Sikertelen törlés',
    'bulk_delete_error_message' => 'Egy vagy több elemet nem sikerült törölni.',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'Biztos, hogy klónozni szeretnéd ezt a :number elemet?',
    'bulk_clone_sucess_title' => 'Elemek klónozva',
    'bulk_clone_sucess_message' => ' elem klónozva lett.',
    'bulk_clone_error_title' => 'Sikertelen klónozás',
    'bulk_clone_error_message' => 'Egy vagy több elemet nem sikerült klónozni. Kérlek próbáld újra!',

    // Ajax errors
    'ajax_error_title' => 'Hiba',
    'ajax_error_text' => 'Nem sikerült betölteni az oldalt. Kérlek frissítsd!',

    // DataTables translation
    'emptyTable' => 'Nincs adat.',
    'info' => '_START_ - _END_ megjelenítése a(z) _TOTAL_ elemből.',
    'infoEmpty' => 'Nincs elem',
    'infoFiltered' => '(szűrve az összesen _MAX_ elemből)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ elem laponként',
    'loadingRecords' => 'Betöltés...',
    'processing' => 'Feldolgozás...',
    'search' => 'Keresés',
    'zeroRecords' => 'Nem található elem',
    'paginate' => [
        'first' => 'Első',
        'last' => 'Utolsó',
        'next' => 'Következő',
        'previous' => 'Előző',
    ],
    'aria' => [
        'sortAscending' => ': aktiváld a növekvő rendezéshez',
        'sortDescending' => ': aktiváld a csökkenő rendezéshez',
    ],
    'export' => [
        'export' => 'Exportálás',
        'copy' => 'Másolás',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Nyomtatás',
        'column_visibility' => 'Oszlop láthatósága',
    ],

    // global crud - errors
    'unauthorized_access' => 'Jogosulatlan hozzáférés. Nincsenek meg a megfelelő jogosultságaid az oldal megtekintéséhez.',
    'please_fix' => 'Kérlek javítsd a következő hibákat:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Sikeresen hozzáadva.',
    'update_success' => 'Sikeres szerkesztés.',

    // CRUD reorder view
    'reorder' => 'Átrendezés',
    'reorder_text' => 'Használd a fogd és vidd módszert az átrendezéshez.',
    'reorder_success_title' => 'Kész',
    'reorder_success_message' => 'A sorrend mentve.',
    'reorder_error_title' => 'Hiba',
    'reorder_error_message' => 'Nem sikerült menteni a sorrendet.',

    // CRUD yes/no
    'yes' => 'Igen',
    'no' => 'Nem',

    // CRUD filters navbar view
    'filters' => 'Szűrők',
    'toggle_filters' => 'Szűrők váltása',
    'remove_filters' => 'Szűrők eltávolítása',

    // Fields
    'browse_uploads' => 'Feltöltések böngészése',
    'select_all' => 'Összes kiválasztása',
    'select_files' => 'Fájlok kiválasztása',
    'select_file' => 'Fájl kiválasztása',
    'clear' => 'Törlés',
    'page_link' => 'Oldal link',
    'page_link_placeholder' => 'http://example.com/sajat-oldal',
    'internal_link' => 'Belső link',
    'internal_link_placeholder' => 'Belső link. Pl: \'admin/oldal\' (idézőjelek nélkül) itt \':url\'',
    'external_link' => 'Külső link',
    'choose_file' => 'Fájl kiválasztása',
    'new_item' => 'Új elem',
    'select_entry' => 'Válassz egy elemet',
    'select_entries' => 'Válassz elemeket',

    //Table field
    'table_cant_add' => 'Nem sikerült az új :entity létrehozása.',
    'table_max_reached' => 'Elérted a maximum :max elemet.',

    // File manager
    'file_manager' => 'Fájlkezelő',

    // InlineCreateOperation
    'related_entry_created_success' => 'A kapcsolódó elem létrejött és ki lett választva.',
    'related_entry_created_error' => 'Nem sikerült létrehozni a kapcsolódó elemet.',
];
