<?php

return [
    'exception_message' => 'رسالة استثناء: :message',
    'exception_trace' => 'تتبع الإستثناء: :trace',
    'exception_message_title' => 'رسالة استثناء',
    'exception_trace_title' => 'تتبع الإستثناء',

    'backup_failed_subject' => 'أخفق النسخ الاحتياطي لل :application_name',
    'backup_failed_body' => 'مهم: حدث خطأ أثناء النسخ الاحتياطي :application_name',

    'backup_successful_subject' => 'نسخ احتياطي جديد ناجح ل :application_name',
    'backup_successful_subject_title' => 'نجاح النسخ الاحتياطي الجديد!',
    'backup_successful_body' => 'أخبار عظيمة، نسخة احتياطية جديدة ل :application_name تم إنشاؤها بنجاح على القرص المسمى :disk_name.',

    'cleanup_failed_subject' => 'فشل تنظيف النسخ الاحتياطي للتطبيق :application_name .',
    'cleanup_failed_body' => 'حدث خطأ أثناء تنظيف النسخ الاحتياطية ل :application_name',

    'cleanup_successful_subject' => 'تنظيف النسخ الاحتياطية ل :application_name تمت بنجاح',
    'cleanup_successful_subject_title' => 'تنظيف النسخ الاحتياطية تم بنجاح!',
    'cleanup_successful_body' => 'تنظيف النسخ الاحتياطية ل :application_name على القرص المسمى :disk_name تم بنجاح.',

    'healthy_backup_found_subject' => 'النسخ الاحتياطية ل :application_name على القرص :disk_name صحية',
    'healthy_backup_found_subject_title' => 'النسخ الاحتياطية ل :application_name صحية',
    'healthy_backup_found_body' => 'تعتبر النسخ الاحتياطية ل :application_name صحية. عمل جيد!',

    'unhealthy_backup_found_subject' => 'مهم: النسخ الاحتياطية ل :application_name غير صحية',
    'unhealthy_backup_found_subject_title' => 'مهم: النسخ الاحتياطية ل :application_name غير صحية. :problem',
    'unhealthy_backup_found_body' => 'النسخ الاحتياطية ل :application_name على القرص :disk_name غير صحية.',
    'unhealthy_backup_found_not_reachable' => 'لا يمكن الوصول إلى وجهة النسخ الاحتياطي. :error',
    'unhealthy_backup_found_empty' => 'لا توجد نسخ احتياطية لهذا التطبيق على الإطلاق.',
    'unhealthy_backup_found_old' => 'تم إنشاء أحدث النسخ الاحتياطية في :date وتعتبر قديمة جدا.',
    'unhealthy_backup_found_unknown' => 'عذرا، لا يمكن تحديد سبب دقيق.',
    'unhealthy_backup_found_full' => 'النسخ الاحتياطية تستخدم الكثير من التخزين. الاستخدام الحالي هو :disk_usage وهو أعلى من الحد المسموح به من :disk_limit.',

    'no_backups_info' => 'لم يتم عمل نسخ احتياطية حتى الآن',
    'application_name' => 'اسم التطبيق',
    'backup_name' => 'اسم النسخ الاحتياطي',
    'disk' => 'القرص',
    'newest_backup_size' => 'أحدث حجم للنسخ الاحتياطي',
    'number_of_backups' => 'عدد النسخ الاحتياطية',
    'total_storage_used' => 'إجمالي مساحة التخزين المستخدمة',
    'newest_backup_date' => 'أحدث تاريخ النسخ الاحتياطي',
    'oldest_backup_date' => 'أقدم تاريخ نسخ احتياطي',
];
