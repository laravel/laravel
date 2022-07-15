<?php

return [
    'exception_message' => 'Exception message: :message',
    'exception_trace' => 'Exception trace: :trace',
    'exception_message_title' => 'Exception message',
    'exception_trace_title' => 'Exception trace',

    'backup_failed_subject' => 'Failed backup of :application_name',
    'backup_failed_body' => 'Important: An error occurred while backing up :application_name',

    'backup_successful_subject' => 'Successful new backup of :application_name',
    'backup_successful_subject_title' => 'Successful new backup!',
    'backup_successful_body' => 'Great news, a new backup of :application_name was successfully created on the disk named :disk_name.',

    'cleanup_failed_subject' => 'Cleaning up the backups of :application_name failed.',
    'cleanup_failed_body' => 'An error occurred while cleaning up the backups of :application_name',

    'cleanup_successful_subject' => 'Clean up of :application_name backups successful',
    'cleanup_successful_subject_title' => 'Clean up of backups successful!',
    'cleanup_successful_body' => 'The clean up of the :application_name backups on the disk named :disk_name was successful.',

    'healthy_backup_found_subject' => 'The backups for :application_name on disk :disk_name are healthy',
    'healthy_backup_found_subject_title' => 'The backups for :application_name are healthy',
    'healthy_backup_found_body' => 'The backups for :application_name are considered healthy. Good job!',

    'unhealthy_backup_found_subject' => 'Important: The backups for :application_name are unhealthy',
    'unhealthy_backup_found_subject_title' => 'Important: The backups for :application_name are unhealthy. :problem',
    'unhealthy_backup_found_body' => 'The backups for :application_name on disk :disk_name are unhealthy.',
    'unhealthy_backup_found_not_reachable' => 'The backup destination cannot be reached. :error',
    'unhealthy_backup_found_empty' => 'There are no backups of this application at all.',
    'unhealthy_backup_found_old' => 'The latest backup made on :date is considered too old.',
    'unhealthy_backup_found_unknown' => 'Sorry, an exact reason cannot be determined.',
    'unhealthy_backup_found_full' => 'The backups are using too much storage. Current usage is :disk_usage which is higher than the allowed limit of :disk_limit.',

    'no_backups_info' => 'No backups were made yet',
    'application_name' => 'Application name',
    'backup_name' => 'Backup name',
    'disk' => 'Disk',
    'newest_backup_size' => 'Newest backup size',
    'number_of_backups' => 'Number of backups',
    'total_storage_used' => 'Total storage used',
    'newest_backup_date' => 'Newest backup date',
    'oldest_backup_date' => 'Oldest backup date',
];
