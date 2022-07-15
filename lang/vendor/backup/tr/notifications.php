<?php

return [
    'exception_message' => 'Hata mesajı: :message',
    'exception_trace' => 'Hata izleri: :trace',
    'exception_message_title' => 'Hata mesajı',
    'exception_trace_title' => 'Hata izleri',

    'backup_failed_subject' => 'Yedeklenemedi :application_name',
    'backup_failed_body' => 'Önemli: Yedeklenirken bir hata oluştu :application_name',

    'backup_successful_subject' => 'Başarılı :application_name yeni yedeklemesi',
    'backup_successful_subject_title' => 'Başarılı bir yeni yedekleme!',
    'backup_successful_body' => 'Harika bir haber, :application_name âit yeni bir yedekleme :disk_name adlı diskte başarıyla oluşturuldu.',

    'cleanup_failed_subject' => ':application_name yedeklemeleri temizlenmesi başarısız.',
    'cleanup_failed_body' => ':application_name yedeklerini temizlerken bir hata oluştu ',

    'cleanup_successful_subject' => ':application_name yedeklemeleri temizlenmesi başarılı.',
    'cleanup_successful_subject_title' => 'Yedeklerin temizlenmesi başarılı!',
    'cleanup_successful_body' => ':application_name yedeklemeleri temizlenmesi ,:disk_name diskinden silindi',

    'healthy_backup_found_subject' => ':application_name yedeklenmesi ,:disk_name adlı diskte sağlıklı',
    'healthy_backup_found_subject_title' => ':application_name yedeklenmesi sağlıklı',
    'healthy_backup_found_body' => ':application_name için yapılan yedeklemeler sağlıklı sayılır. Aferin!',

    'unhealthy_backup_found_subject' => 'Önemli: :application_name için yedeklemeler sağlıksız',
    'unhealthy_backup_found_subject_title' => 'Önemli: :application_name için yedeklemeler sağlıksız. :problem',
    'unhealthy_backup_found_body' => 'Yedeklemeler: :application_name disk: :disk_name sağlıksız.',
    'unhealthy_backup_found_not_reachable' => 'Yedekleme hedefine ulaşılamıyor. :error',
    'unhealthy_backup_found_empty' => 'Bu uygulamanın yedekleri yok.',
    'unhealthy_backup_found_old' => ':date tarihinde yapılan en son yedekleme çok eski kabul ediliyor.',
    'unhealthy_backup_found_unknown' => 'Üzgünüm, kesin bir sebep belirlenemiyor.',
    'unhealthy_backup_found_full' => 'Yedeklemeler çok fazla depolama alanı kullanıyor. Şu anki kullanım: :disk_usage, izin verilen sınırdan yüksek: :disk_limit.',

    'no_backups_info' => 'Henüz yedekleme yapılmadı',
    'application_name' => 'Uygulama Adı',
    'backup_name' => 'Yedek adı',
    'disk' => 'Disk',
    'newest_backup_size' => 'En yeni yedekleme boyutu',
    'number_of_backups' => 'Yedekleme sayısı',
    'total_storage_used' => 'Kullanılan toplam depolama alanı',
    'newest_backup_date' => 'En yeni yedekleme boyutu',
    'oldest_backup_date' => 'En eski yedekleme boyutu',
];
