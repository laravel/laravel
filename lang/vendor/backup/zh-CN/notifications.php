<?php

return [
    'exception_message' => '异常信息: :message',
    'exception_trace' => '异常跟踪: :trace',
    'exception_message_title' => '异常信息',
    'exception_trace_title' => '异常跟踪',

    'backup_failed_subject' => ':application_name 备份失败',
    'backup_failed_body' => '重要说明：备份 :application_name 时发生错误',

    'backup_successful_subject' => ':application_name 备份成功',
    'backup_successful_subject_title' => '备份成功！',
    'backup_successful_body' => '好消息, :application_name 备份成功，位于磁盘 :disk_name 中。',

    'cleanup_failed_subject' => '清除 :application_name 的备份失败。',
    'cleanup_failed_body' => '清除备份 :application_name 时发生错误',

    'cleanup_successful_subject' => '成功清除 :application_name 的备份',
    'cleanup_successful_subject_title' => '成功清除备份！',
    'cleanup_successful_body' => '成功清除 :disk_name 磁盘上 :application_name 的备份。',

    'healthy_backup_found_subject' => ':disk_name 磁盘上 :application_name 的备份是健康的',
    'healthy_backup_found_subject_title' => ':application_name 的备份是健康的',
    'healthy_backup_found_body' => ':application_name 的备份是健康的。干的好！',

    'unhealthy_backup_found_subject' => '重要说明：:application_name 的备份不健康',
    'unhealthy_backup_found_subject_title' => '重要说明：:application_name 备份不健康。 :problem',
    'unhealthy_backup_found_body' => ':disk_name 磁盘上 :application_name 的备份不健康。',
    'unhealthy_backup_found_not_reachable' => '无法访问备份目标。 :error',
    'unhealthy_backup_found_empty' => '根本没有此应用程序的备份。',
    'unhealthy_backup_found_old' => '最近的备份创建于 :date ，太旧了。',
    'unhealthy_backup_found_unknown' => '对不起，确切原因无法确定。',
    'unhealthy_backup_found_full' => '备份占用了太多存储空间。当前占用了 :disk_usage ，高于允许的限制 :disk_limit。',

    'no_backups_info' => '尚未进行任何备份',
    'application_name' => '应用名称',
    'backup_name' => '备份名称',
    'disk' => '磁盘',
    'newest_backup_size' => '最新备份大小',
    'number_of_backups' => '备份数量',
    'total_storage_used' => '使用的总存储量',
    'newest_backup_date' => '最新备份大小',
    'oldest_backup_date' => '最旧的备份大小',
];
