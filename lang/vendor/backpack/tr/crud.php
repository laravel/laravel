<?php

return [

    // Forms
    'save_action_save_and_new' => 'Kaydet ve yeni öğe',
    'save_action_save_and_edit' => 'Bu öğeyi kaydet ve düzenle',
    'save_action_save_and_back' => 'Kaydet ve geri dön.',
    'save_action_changed_notification' => 'Kaydedildikten sonra varsayılan davranış değiştirildi.',

    // Create form
    'add' => 'Ekle',
    'back_to_all' => 'Geri Dön ',
    'cancel' => 'İptal',
    'add_a_new' => 'Yeni Ekle ',

    // Edit form
    'edit' => 'Düzenle',
    'save' => 'Kaydet',

    // Translatable models
    'edit_translations' => 'EDIT TRANSLATIONS',
    'language' => 'Language',

    // CRUD table view
    'all' => 'Tümü ',
    'in_the_database' => 'Veritabanında',
    'list' => 'Liste',
    'actions' => 'Aksiyonlar',
    'preview' => 'Önizleme',
    'delete' => 'Sil',
    'admin' => 'Admin',
    'details_row' => 'Detaylar satırı. İstediğiniz gibi değiştirin.',
    'details_row_loading_error' => 'Ayrıntılar yüklenirken bir hata oluştu. Lütfen tekrar deneyiniz.',
    'confirm_delete' => 'Evet',
    'are_you_sure' => 'Emin misiniz?',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Bu öğeyi silmek istediğinizden emin misiniz?',
    'delete_confirmation_title' => 'Öğe Silindi.',
    'delete_confirmation_message' => 'Öğe başarıyla silindi.',
    'delete_confirmation_not_title' => 'Silinemedi',
    'delete_confirmation_not_message' => 'Bir hata oluştu. Öğeniz silinmemiş olabilir.',
    'delete_confirmation_not_deleted_title' => 'Silinmedi',
    'delete_confirmation_not_deleted_message' => 'Hiçbir şey olmadı. Öğeniz güvende.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Kayıt seçilmedi',
    'bulk_no_entries_selected_message' => 'Lütfen toplu işlem gerçekleştirmek için bir veya daha fazla öğe seçin.',

    // Bulk confirmation
    'bulk_delete_are_you_sure' => 'Bunları silmek istediğinizden emin misiniz? :number öğe',
    'bulk_delete_sucess_title' => 'Girdiler silindi.',
    'bulk_delete_sucess_message' => 'Öğeler silindi',
    'bulk_delete_error_title' => 'Silme başarısız',
    'bulk_delete_error_message' => 'Bir veya daha fazla öğe silinemedi',

    // Ajax errors
    'ajax_error_title' => 'Hata',
    'ajax_error_text' => 'Sayfa yüklenirken hata oluştu. Lütfen sayfayı yenileyin.',

    // DataTables translation
    'emptyTable' => 'Tabloda veri yok',
    'info' => '_START_ ile _END_ arası Toplam _TOTAL_ kayıt',
    'infoEmpty' => '',
    'infoFiltered' => '(Toplam _MAX_ kayıt filtrelendi)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ kayıt sayfa başına',
    'loadingRecords' => 'Yükleniyor...',
    'processing' => 'İşleniyor...',
    'search' => 'Arama',
    'zeroRecords' => 'Hiçbir eşleşen kayıt bulunamadı',
    'paginate' => [
        'first' => 'İlk',
        'last' => 'Son',
        'next' => 'Sonraki',
        'previous' => 'Önceki',
    ],
    'aria' => [
        'sortAscending' => ': artan sütun sıralamak için etkinleştir',
        'sortDescending' => ': azalan sütun sıralamak için etkinleştir',
    ],
    'export' => [
        'copy' => 'Kopyala',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Yazdır',
        'column_visibility' => 'Sütün görünürlüğü',
    ],

    // global crud - errors
    'unauthorized_access' => 'Yetkisiz erişim - bu sayfayı görmek için gerekli izinlere sahip değilsiniz.',
    'please_fix' => 'Lütfen aşağıdaki hataları düzeltin:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Öğe başarıyla eklendi.',
    'update_success' => 'Öğe başarıyla değiştirildi.',

    // CRUD reorder view
    'reorder' => 'Tekrar Sırala',
    'reorder_text' => 'Sürükle&bırak olarak sırala.',
    'reorder_success_title' => 'Tamam',
    'reorder_success_message' => 'Sıralama kayıt edildi.',
    'reorder_error_title' => 'Hata',
    'reorder_error_message' => 'Sıralama kayıt edilemedi.',

    // CRUD yes/no
    'yes' => 'Evet',
    'no' => 'Hayır',

    // CRUD filters navbar view
    'filters' => 'Filtreler',
    'toggle_filters' => 'Geçiş filtreleri',
    'remove_filters' => 'Silme filtreleri',

    // Fields
    'browse_uploads' => 'Yüklemelere göz atın',
    'select_all' => 'Tümünü seç',
    'select_files' => 'Dosyaları seç',
    'select_file' => 'Dosya seç',
    'clear' => 'Temizle',
    'page_link' => 'Sayfa linki',
    'page_link_placeholder' => 'https://ornek.com/sayfa',
    'internal_link' => 'İç link',
    'internal_link_placeholder' => 'İç sayfa slug. Örn: \'admin/sayfa\' (tırnak isareti yok) => \':url\'',
    'external_link' => 'Dış link',
    'choose_file' => 'Dosya seç',

    //Table field
    'table_cant_add' => 'Yeni :entity eklenemez',
    'table_max_reached' => 'Maksimum :max kadar',

    // File manager
    'file_manager' => 'Dosya yöneticisi',

];
