<?php

return [
    'exception_message' => '異常訊息: :message',
    'exception_trace' => '異常追蹤: :trace',
    'exception_message_title' => '異常訊息',
    'exception_trace_title' => '異常追蹤',

    'backup_failed_subject' => ':application_name 備份失敗',
    'backup_failed_body' => '重要說明：備份 :application_name 時發生錯誤',

    'backup_successful_subject' => ':application_name 備份成功',
    'backup_successful_subject_title' => '備份成功！',
    'backup_successful_body' => '好消息, :application_name 備份成功，位於磁盤 :disk_name 中。',

    'cleanup_failed_subject' => '清除 :application_name 的備份失敗。',
    'cleanup_failed_body' => '清除備份 :application_name 時發生錯誤',

    'cleanup_successful_subject' => '成功清除 :application_name 的備份',
    'cleanup_successful_subject_title' => '成功清除備份！',
    'cleanup_successful_body' => '成功清除 :disk_name 磁盤上 :application_name 的備份。',

    'healthy_backup_found_subject' => ':disk_name 磁盤上 :application_name 的備份是健康的',
    'healthy_backup_found_subject_title' => ':application_name 的備份是健康的',
    'healthy_backup_found_body' => ':application_name 的備份是健康的。幹的好！',

    'unhealthy_backup_found_subject' => '重要說明：:application_name 的備份不健康',
    'unhealthy_backup_found_subject_title' => '重要說明：:application_name 備份不健康。 :problem',
    'unhealthy_backup_found_body' => ':disk_name 磁盤上 :application_name 的備份不健康。',
    'unhealthy_backup_found_not_reachable' => '無法訪問備份目標。 :error',
    'unhealthy_backup_found_empty' => '根本沒有此應用程序的備份。',
    'unhealthy_backup_found_old' => '最近的備份創建於 :date ，太舊了。',
    'unhealthy_backup_found_unknown' => '對不起，確切原因無法確定。',
    'unhealthy_backup_found_full' => '備份佔用了太多存儲空間。當前佔用了 :disk_usage ，高於允許的限制 :disk_limit。',

    'no_backups_info' => '尚未進行任何備份',
    'application_name' => '應用名稱',
    'backup_name' => '備份名稱',
    'disk' => '磁碟',
    'newest_backup_size' => '最新備份大小',
    'number_of_backups' => '備份數量',
    'total_storage_used' => '使用的總存儲量',
    'newest_backup_date' => '最新備份大小',
    'oldest_backup_date' => '最早的備份大小',
];
