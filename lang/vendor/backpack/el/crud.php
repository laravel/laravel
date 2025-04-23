<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dick Crud Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
     */

    // Forms
    'save_action_save_and_new' => 'Αποθήκευση και εισαγωγή νέου',
    'save_action_save_and_edit' => 'Αποθήκευση και επεξεργασία',
    'save_action_save_and_back' => 'Αποθήκευση και επιστροφή',
    'save_action_changed_notification' => 'Η προκαθορισμένη συμπεριφορά μετά την αποθήκευση έχει αλλάξει.',

    // Create form
    'add' => 'Προσθήκη',
    'back_to_all' => 'Επιστροφή στη λίστα ',
    'cancel' => 'Ακύρωση',
    'add_a_new' => 'Προσθήκη ',

    // Create form - advanced options
    'after_saving' => 'Μετά την αποθήκευση',
    'go_to_the_table_view' => 'επιστροφή στον πίνακα',
    'let_me_add_another_item' => 'προσθήκη νέου αντικειμένου',
    'edit_the_new_item' => 'τροποποίηση νέου αντικειμένου',

    // Edit form
    'edit' => 'Τροποποίηση',
    'save' => 'Αποθήκευση',

    // CRUD table view
    'all' => '',
    'in_the_database' => 'της βάσης δεδομένων',
    'list' => 'Λίστα',
    'actions' => 'Ενέργειες',
    'preview' => 'Προεπισκόπηση',
    'delete' => 'Διαγραφή',
    'admin' => 'Διαχειριστής',
    'details_row' => 'Αυτή είναι η γραμμή λεπτομερειών. Τροποποιήστε την όπως επιθυμείτε.',
    'details_row_loading_error' => 'Υπήρξε ένα σφάλμα κατά τη φόρτωση λεπτομερειών. Παρακαλώ δοκιμάστε ξανά.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Είστε σίγουρος/η πως θέλετε να διαγράψετε αυτό το αντικείμενο?',
    'delete_confirmation_title' => 'Το αντικείμενο διαγράφηκε',
    'delete_confirmation_message' => 'Το αντικείμενο διαγράφηκε επιτυχώς.',
    'delete_confirmation_not_title' => 'ΔΕΝ διαγράφηκε',
    'delete_confirmation_not_message' => 'Υπήρξε σφάλμα. Το αντικείμενο ενδέχεται να μην έχει διαγραφεί.',
    'delete_confirmation_not_deleted_title' => 'Δεν διαγράφηκε',
    'delete_confirmation_not_deleted_message' => 'Δεν έγινε τίποτα. Το αντικείμενό σας είναι ασφαλές.',

    // DataTables translation
    'emptyTable' => 'Δεν υπάρχουν διαθέσιμα δεδομένα στον πίνακα',
    'info' => 'Εμφάνιση _START_ έως _END_ από _TOTAL_ εγγραφές',
    'infoEmpty' => '',
    'infoFiltered' => '(Επιστράφηκαν από _MAX_ συνολικές εγγραφές)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ εγγραφές ανά σελίδα',
    'loadingRecords' => 'Loading...',
    'processing' => 'Processing...',
    'search' => 'Αναζήτηση',
    'zeroRecords' => 'Δεν βρέθηκε καμία σχετική εγγραφή',
    'paginate' => [
        'first' => 'Πρώτη',
        'last' => 'Τελευταία',
        'next' => 'Επόμενη',
        'previous' => 'Προηγούμενη',
    ],
    'aria' => [
        'sortAscending' => ': ενεργοποίηση για αύξουσα ταξινόμηση',
        'sortDescending' => ': ενεργοποίηση για φθίνουσα ταξινόμηση',
    ],

    // global crud - errors
    'unauthorized_access' => 'Μη εξουσιοδοτημένη πρόσβαση - δεν έχετε την απαιτούμενη εξουσιοδότηση για να δείτε τη σελίδα αυτή.',
    'please_fix' => 'Παρακαλώ διορθώστε τα παρακάτω σφάλματα:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Το αντικείμενο προστέθηκε με επιτυχία.',
    'update_success' => 'Το αντικείμενο τροποποιήθηκε με επιτυχία.',

    // CRUD reorder view
    'reorder' => 'Αλλαγή σειράς εμφάνισης',
    'reorder_text' => 'Χρησιμοποιήστε drag&drop για αλλαγή σειράς εμφάνισης.',
    'reorder_success_title' => 'Πραγματοποιήθηκε',
    'reorder_success_message' => 'Η σειρά εμφάνισης έχει αποθηκευτεί.',
    'reorder_error_title' => 'Σφάλμα',
    'reorder_error_message' => 'Η σειρά εμφάνισης δεν έχει αποθηκευτεί.',

    // CRUD yes/no
    'yes' => 'Ναι',
    'no' => 'Όχι',

    // Fields
    'browse_uploads' => 'Αναζήτηση μεταφορτωμένων αρχείων',
    'clear' => 'Εκκαθάριση',
    'page_link' => 'Σύνδεσμος Σελίδας',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Εσωτερικός σύνδεσμος',
    'internal_link_placeholder' => 'Εσωτερικό slug. πχ: \'admin/page\' (χωρίς εισαγωγικά) για \':url\'',
    'external_link' => 'Εξωτερικός σύνδεσμος',
    'choose_file' => 'Επιλογή αρχείου',

    //Table field
    'table_cant_add' => 'Δεν μπορεί να προστεθεί νέο/α :entity',
    'table_max_reached' => 'Μέγιστο πλήθος από :max reached',

];
