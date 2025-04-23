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
    'save_action_save_and_new' => 'Sačuvaj i kreiraj novo',
    'save_action_save_and_edit' => 'Sačuvaj i izmeni',
    'save_action_save_and_back' => 'Sačuvaj i idi nazad',
    'save_action_save_and_preview' => 'Sačuvaj i pregledaj',
    'save_action_changed_notification' => 'Podrazumevano ponašanje nakon čuvanja je promenjeno.',

    // Create form
    'add' => 'Dodaj',
    'back_to_all' => 'Nazad na listu ',
    'cancel' => 'Otkaži',
    'add_a_new' => 'Dodaj novi ',

    // Edit form
    'edit' => 'Izmeni',
    'save' => 'Sačuvaj',

    // Translatable models
    'edit_translations' => 'Prevod',
    'language' => 'Jezik',

    // CRUD table view
    'all' => 'Sve ',
    'in_the_database' => 'u bazi',
    'list' => 'Lista',
    'actions' => 'Akcije',
    'preview' => 'Pregled',
    'delete' => 'Obriši',
    'admin' => 'Admin',
    'details_row' => 'Ovo je red sa detaljima. Promeni ga po svojoj želji.',
    'details_row_loading_error' => 'Došlo je do greške prilikom učitavanja detalja. Pokušaj ponovo.',
    'clone' => 'Dupliraj',
    'clone_success' => '<strong>Duplirano</strong><br>Dodat je novi unos sa istim podacima.',
    'clone_failure' => '<strong>Dupliranje nije uspelo</strong><br>Novi unos nije mogao da se kreira. Pokušajte ponovo.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Da li ste sigurni da želite da obrišete ovu stavku?',
    'delete_confirmation_title' => 'Obrisano!',
    'delete_confirmation_message' => 'Stavka je uspešno obrisana.',
    'delete_confirmation_not_title' => 'NIJE obrisano',
    'delete_confirmation_not_message' => 'Došlo je do greške. Vaša stavka možda nije izbrisana.',
    'delete_confirmation_not_deleted_title' => 'Nije obrisano',
    'delete_confirmation_not_deleted_message' => 'Ništa se nije dogodilo. Stavka je sigurna.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Nijedna stavka nije odabrana',
    'bulk_no_entries_selected_message' => 'Morate odabrati jednu ili više stavki da bi ste izvršili akciju nad njima.',

    // Bulk confirmation
    'bulk_delete_are_you_sure' => 'Da li ste sigurni da želite da obrišete :number stavki?',
    'bulk_delete_sucess_title' => 'Obrisano',
    'bulk_delete_sucess_message' => ' stavke su obrisane',
    'bulk_delete_error_title' => 'Brisanje nije uspelo',
    'bulk_delete_error_message' => 'Jednu ili više stavki nije bilo moguće obrisati',

    // Ajax errors
    'ajax_error_title' => 'Greška',
    'ajax_error_text' => 'Greška pri učitavanju stranice. Osvežite stranicu.',

    // DataTables translation
    'emptyTable' => 'Nema podataka u tabeli',
    'info' => 'Prikaz stavki od _START_ do _END_. Ukupno _TOTAL_ stavki',
    'infoEmpty' => 'Nema unosa',
    'infoFiltered' => '(filtrirano od _MAX_ ukupno stavki)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ stavki po strani',
    'loadingRecords' => 'Učitavanje...',
    'processing' => 'Obrada...',
    'search' => 'Pretraga',
    'zeroRecords' => 'Ne postoje stavke sa trenutno odabranim filterima. Uklonite neki od filtera.',
    'paginate' => [
        'first' => 'Prvi',
        'last' => 'Poslednji',
        'next' => 'Sledeći',
        'previous' => 'Prethodni',
    ],
    'aria' => [
        'sortAscending' => ': aktiviraj da bi sortirao kolonu rastuće',
        'sortDescending' => ': aktiviraj da bi sortirao kolonu opadajuće',
    ],
    'export' => [
        'export' => 'Izvezi',
        'copy' => 'Kopiraj',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Odštampaj',
        'column_visibility' => 'Vidljivost kolona',
    ],

    // global crud - errors
    'unauthorized_access' => 'Neovlašćeni pristup - nemate potrebna prava da pristupite ovoj stranici.',
    'please_fix' => 'Molim Vas ispravite sledeće greške:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Stavka je uspešno dodata.',
    'update_success' => 'Stavka je uspešno izmenjena.',

    // CRUD reorder view
    'reorder' => 'Promeni redosled',
    'reorder_text' => 'Koristite drag&drop za promenu redosleda.',
    'reorder_success_title' => 'Gotovo',
    'reorder_success_message' => 'Redosled je sačuvan.',
    'reorder_error_title' => 'Greška',
    'reorder_error_message' => 'Redosled nije sačuvan.',

    // CRUD yes/no
    'yes' => 'Da',
    'no' => 'Ne',

    // CRUD filters navbar view
    'filters' => 'Filteri',
    'toggle_filters' => 'Prikaz filtera',
    'remove_filters' => 'Ukloni filtere',

    // Fields
    'browse_uploads' => 'Pregled fajlova',
    'select_all' => 'Odaberi Sve',
    'select_files' => 'Odaberi fajlove',
    'select_file' => 'Odaberi fajl',
    'clear' => 'Ukloni',
    'page_link' => 'Link ka stranici',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Interni link',
    'internal_link_placeholder' => 'Interni nastavak linka (slug). Primer: \'admin/page\' (bez navodnika) za \':url\'',
    'external_link' => 'Eksterni link',
    'choose_file' => 'Izaberi fajl',

    //Table field
    'table_cant_add' => 'Nije moguće dodati nov :entity',
    'table_max_reached' => 'Maksimalni broj od :max je dostignut',

    // File manager
    'file_manager' => 'Menadžer Fajlova',

    // InlineCreateOperation
    'related_entry_created_success' => 'Srodni unos je kreiran i izabran.',
    'related_entry_created_error' => 'Srodni unos nije kreiran.',
];
