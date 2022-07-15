<?php

return [
    'exception_message' => 'Fehlermeldung: :message',
    'exception_trace' => 'Fehlerverfolgung: :trace',
    'exception_message_title' => 'Fehlermeldung',
    'exception_trace_title' => 'Fehlerverfolgung',

    'backup_failed_subject' => 'Backup von :application_name konnte nicht erstellt werden',
    'backup_failed_body' => 'Wichtig: Beim Backup von :application_name ist ein Fehler aufgetreten',

    'backup_successful_subject' => 'Erfolgreiches neues Backup von :application_name',
    'backup_successful_subject_title' => 'Erfolgreiches neues Backup!',
    'backup_successful_body' => 'Gute Nachrichten, ein neues Backup von :application_name wurde erfolgreich erstellt und in :disk_name gepeichert.',

    'cleanup_failed_subject' => 'Aufräumen der Backups von :application_name schlug fehl.',
    'cleanup_failed_body' => 'Beim aufräumen der Backups von :application_name ist ein Fehler aufgetreten',

    'cleanup_successful_subject' => 'Aufräumen der Backups von :application_name backups erfolgreich',
    'cleanup_successful_subject_title' => 'Aufräumen der Backups erfolgreich!',
    'cleanup_successful_body' => 'Aufräumen der Backups von :application_name in :disk_name war erfolgreich.',

    'healthy_backup_found_subject' => 'Die Backups von :application_name in :disk_name sind gesund',
    'healthy_backup_found_subject_title' => 'Die Backups von :application_name sind Gesund',
    'healthy_backup_found_body' => 'Die Backups von :application_name wurden als gesund eingestuft. Gute Arbeit!',

    'unhealthy_backup_found_subject' => 'Wichtig: Die Backups für :application_name sind nicht gesund',
    'unhealthy_backup_found_subject_title' => 'Wichtig: Die Backups für :application_name sind ungesund. :problem',
    'unhealthy_backup_found_body' => 'Die Backups für :application_name in :disk_name sind ungesund.',
    'unhealthy_backup_found_not_reachable' => 'Das Backup Ziel konnte nicht erreicht werden. :error',
    'unhealthy_backup_found_empty' => 'Es gibt für die Anwendung noch gar keine Backups.',
    'unhealthy_backup_found_old' => 'Das letzte Backup am :date ist zu lange her.',
    'unhealthy_backup_found_unknown' => 'Sorry, ein genauer Grund konnte nicht gefunden werden.',
    'unhealthy_backup_found_full' => 'Die Backups verbrauchen zu viel Platz. Aktuell wird :disk_usage belegt, dass ist höher als das erlaubte Limit von :disk_limit.',

    'no_backups_info' => 'Bisher keine Backups vorhanden',
    'application_name' => 'Applikationsname',
    'backup_name' => 'Backup Name',
    'disk' => 'Speicherort',
    'newest_backup_size' => 'Neuste Backup-Größe',
    'number_of_backups' => 'Anzahl Backups',
    'total_storage_used' => 'Gesamter genutzter Speicherplatz',
    'newest_backup_date' => 'Neustes Backup',
    'oldest_backup_date' => 'Ältestes Backup',
];
