<?php

return [
    'exception_message' => 'پیغام خطا: :message',
    'exception_trace' => 'جزییات خطا: :trace',
    'exception_message_title' => 'پیغام خطا',
    'exception_trace_title' => 'جزییات خطا',

    'backup_failed_subject' => 'پشتیبان‌گیری :application_name با خطا مواجه شد.',
    'backup_failed_body' => 'پیغام مهم: هنگام پشتیبان‌گیری از :application_name خطایی رخ داده است. ',

    'backup_successful_subject' => 'نسخه پشتیبان جدید :application_name با موفقیت ساخته شد.',
    'backup_successful_subject_title' => 'پشتیبان‌گیری موفق!',
    'backup_successful_body' => 'خبر خوب, به تازگی نسخه پشتیبان :application_name بر روی دیسک :disk_name با موفقیت ساخته شد. ',

    'cleanup_failed_subject' => 'پاک‌‌سازی نسخه پشتیبان :application_name انجام نشد.',
    'cleanup_failed_body' => 'هنگام پاک‌سازی نسخه پشتیبان :application_name خطایی رخ داده است.',

    'cleanup_successful_subject' => 'پاک‌سازی نسخه پشتیبان :application_name با موفقیت انجام شد.',
    'cleanup_successful_subject_title' => 'پاک‌سازی نسخه پشتیبان!',
    'cleanup_successful_body' => 'پاک‌سازی نسخه پشتیبان :application_name بر روی دیسک :disk_name با موفقیت انجام شد.',

    'healthy_backup_found_subject' => 'نسخه پشتیبان :application_name بر روی دیسک :disk_name سالم بود.',
    'healthy_backup_found_subject_title' => 'نسخه پشتیبان :application_name سالم بود.',
    'healthy_backup_found_body' => 'نسخه پشتیبان :application_name به نظر سالم میاد. دمت گرم!',

    'unhealthy_backup_found_subject' => 'خبر مهم: نسخه پشتیبان :application_name سالم نبود.',
    'unhealthy_backup_found_subject_title' => 'خبر مهم: نسخه پشتیبان :application_name سالم نبود. :problem',
    'unhealthy_backup_found_body' => 'نسخه پشتیبان :application_name بر روی دیسک :disk_name سالم نبود.',
    'unhealthy_backup_found_not_reachable' => 'مقصد پشتیبان‌گیری در دسترس نبود. :error',
    'unhealthy_backup_found_empty' => 'برای این برنامه هیچ نسخه پشتیبانی وجود ندارد.',
    'unhealthy_backup_found_old' => 'آخرین نسخه پشتیبان برای تاریخ :date است. که به نظر خیلی قدیمی میاد. ',
    'unhealthy_backup_found_unknown' => 'متاسفانه دلیل دقیق مشخص نشده است.',
    'unhealthy_backup_found_full' => 'نسخه‌های پشتیبانی که تهیه کرده اید حجم زیادی اشغال کرده اند. میزان دیسک استفاده شده :disk_usage است که از میزان مجاز :disk_limit فراتر رفته است. ',

    'no_backups_info' => 'هنوز نسخه پشتیبان تهیه نشده است',
    'application_name' => 'نام نرم افزار',
    'backup_name' => 'نام پشتیبان',
    'disk' => 'دیسک',
    'newest_backup_size' => 'جدیدترین اندازه پشتیبان',
    'number_of_backups' => 'تعداد پشتیبان گیری',
    'total_storage_used' => 'کل فضای ذخیره سازی استفاده شده',
    'newest_backup_date' => 'جدیدترین اندازه پشتیبان',
    'oldest_backup_date' => 'قدیمی ترین اندازه پشتیبان',
];
