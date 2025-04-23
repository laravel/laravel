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
    'save_action_save_and_new' => 'Simpan dan buat baru',
    'save_action_save_and_edit' => 'Simpan dan lanjutkan perubahan',
    'save_action_save_and_back' => 'Simpan dan kembali',
    'save_action_save_and_preview' => 'Simpan dan lihat',
    'save_action_changed_notification' => 'Perilaku default setelah penyimpanan diubah.',

    // Create form
    'add' => 'Tambah',
    'back_to_all' => 'Kembali ke semua ',
    'cancel' => 'Batal',
    'add_a_new' => 'Tambahkan yang baru ',

    // Edit form
    'edit' => 'Ubah',
    'save' => 'Simpan',

    // Translatable models
    'edit_translations' => 'Terjemahan',
    'language' => 'Bahasa',

    // CRUD table view
    'all' => 'Semua ',
    'in_the_database' => 'di database',
    'list' => 'Daftar',
    'reset' => 'Set ulang',
    'actions' => 'Aksi',
    'preview' => 'Lihat',
    'delete' => 'Hapus',
    'admin' => 'Admin',
    'details_row' => 'Ini adalah baris rincian. Ubah sesuka Anda.',
    'details_row_loading_error' => 'Terjadi kesalahan saat memuat detail. Silakan coba lagi.',
    'clone' => 'Duplikat',
    'clone_success' => '<strong>Masukan telah diduplikat</strong><br>Masukan baru telah ditambahkan, dengan informasi yang sama.',
    'clone_failure' => '<strong>Proses duplikat gagal</strong><br>Masukan baru tidak dapat dibuat. Silakan coba lagi.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Anda yakin ingin menghapus item ini?',
    'delete_confirmation_title' => 'Item Dihapus',
    'delete_confirmation_message' => 'Item telah berhasil dihapus.',
    'delete_confirmation_not_title' => 'TIDAK dihapus',
    'delete_confirmation_not_message' => 'Terjadi kesalahan. Item Anda mungkin belum dihapus.',
    'delete_confirmation_not_deleted_title' => 'Tidak dihapus',
    'delete_confirmation_not_deleted_message' => 'Tidak ada yang terjadi. Item Anda aman.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Tidak ada masukan yang dipilih',
    'bulk_no_entries_selected_message' => 'Silakan pilih satu atau lebih untuk melakukan tindakan massal pada mereka.',

    // Bulk confirmation
    'bulk_delete_are_you_sure' => 'Anda yakin ingin menghapus :number item ini?',
    'bulk_delete_sucess_title' => 'Item dihapus',
    'bulk_delete_sucess_message' => ' item telah dihapus',
    'bulk_delete_error_title' => 'Penghapusan gagal',
    'bulk_delete_error_message' => 'Satu atau lebih item tidak dapat dihapus',

    // Ajax errors
    'ajax_error_title' => 'Terjadi kesalahan',
    'ajax_error_text' => 'Terjadi kesalahan saat memuat halaman. Harap segarkan halaman.',

    // DataTables translation
    'emptyTable' => 'Tak ada data yang tersedia pada tabel ini',
    'info' => 'Menampilkan _START_ hingga _END_ dari _TOTAL_ masukan',
    'infoEmpty' => 'Tidak ada masukan',
    'infoFiltered' => '(difilter dari _MAX_ jumlah masukan)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ masukan per halaman',
    'loadingRecords' => 'Memuat...',
    'processing' => 'Memproses...',
    'search' => 'Cari',
    'zeroRecords' => 'Tidak ada data yang cocok ditemukan',
    'paginate' => [
        'first' => 'Pertama',
        'last' => 'Terakhir',
        'next' => 'Selanjutnya',
        'previous' => 'Sebelumnya',
    ],
    'aria' => [
        'sortAscending' => ': aktifkan untuk mengurutkan kolom naik',
        'sortDescending' => ': aktifkan untuk mengurutkan kolom turun',
    ],
    'export' => [
        'export' => 'Ekspor',
        'copy' => 'Salin',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Cetak',
        'column_visibility' => 'Visibilitas kolom',
    ],

    // global crud - errors
    'unauthorized_access' => 'Akses tidak sah - Anda tidak memiliki izin yang diperlukan untuk melihat halaman ini.',
    'please_fix' => 'Harap perbaiki yang berikut ini:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Item berhasil ditambahkan.',
    'update_success' => 'Item berhasil diubah.',

    // CRUD reorder view
    'reorder' => 'Susun ulang',
    'reorder_text' => 'Gunakan seret & lepas untuk menyusun ulang.',
    'reorder_success_title' => 'Selesai',
    'reorder_success_message' => 'Susunan Anda telah disimpan.',
    'reorder_error_title' => 'Terjadi kesalahan',
    'reorder_error_message' => 'Susunan Anda belum tersimpan',

    // CRUD yes/no
    'yes' => 'Ya',
    'no' => 'Tidak',

    // CRUD filters navbar view
    'filters' => 'Filter',
    'toggle_filters' => 'Alihkan filter',
    'remove_filters' => 'Hapus filter',

    // Fields
    'browse_uploads' => 'Jelajahi unggahan',
    'select_all' => 'Pilih Semua',
    'select_files' => 'Pilih file',
    'select_file' => 'Pilih file',
    'clear' => 'Bersihkan',
    'page_link' => 'Tautan halaman',
    'page_link_placeholder' => 'http://contoh.com/halaman-yang-anda-inginkan',
    'internal_link' => 'Tautan internal',
    'internal_link_placeholder' => 'Slug internal. Cth: \'admin/page\' (tanpa tanda kutip) untuk \':url\'',
    'external_link' => 'Tautan eksternal',
    'choose_file' => 'Pilih File',
    'new_item' => 'Item baru',
    'select_entry' => 'Pilih masukan',
    'select_entries' => 'Pilih masukan',

    //Table field
    'table_cant_add' => 'Tidak dapat menambahkan :entity yang baru',
    'table_max_reached' => 'Jumlah maksimum :max telah tercapai',

    // File manager
    'file_manager' => 'Manajer File',

    // InlineCreateOperation
    'related_entry_created_success' => 'Masukan terkait telah dibuat dan dipilih.',
    'related_entry_created_error' => 'Tidak dapat membuat masukan terkait.',
];
