<?php

return [
    'exception_message' => 'Exception: :message',
    'exception_trace' => 'Exception trace: :trace',
    'exception_message_title' => 'Exception',
    'exception_trace_title' => 'Exception trace',

    'backup_failed_subject' => 'Backup feilet for :application_name',
    'backup_failed_body' => 'Viktg: En feil oppstod under backing av :application_name',

    'backup_successful_subject' => 'Gjennomført backup av :application_name',
    'backup_successful_subject_title' => 'Gjennomført backup!',
    'backup_successful_body' => 'Gode nyheter, en ny backup av :application_name ble opprettet på disken :disk_name.',

    'cleanup_failed_subject' => 'Opprydding av backup for :application_name feilet.',
    'cleanup_failed_body' => 'En feil oppstod under opprydding av backups for :application_name',

    'cleanup_successful_subject' => 'Opprydding av backup for :application_name gjennomført',
    'cleanup_successful_subject_title' => 'Opprydding av backup gjennomført!',
    'cleanup_successful_body' => 'Oppryddingen av backup for :application_name på disken :disk_name har blitt gjennomført.',

    'healthy_backup_found_subject' => 'Alle backups for :application_name på disken :disk_name er OK',
    'healthy_backup_found_subject_title' => 'Alle backups for :application_name er OK',
    'healthy_backup_found_body' => 'Alle backups for :application_name er ok. Godt jobba!',

    'unhealthy_backup_found_subject' => 'Viktig: Backups for :application_name ikke OK',
    'unhealthy_backup_found_subject_title' => 'Viktig: Backups for :application_name er ikke OK. :problem',
    'unhealthy_backup_found_body' => 'Backups for :application_name på disken :disk_name er ikke OK.',
    'unhealthy_backup_found_not_reachable' => 'Kunne ikke finne backup-destinasjonen. :error',
    'unhealthy_backup_found_empty' => 'Denne applikasjonen mangler backups.',
    'unhealthy_backup_found_old' => 'Den siste backupem fra :date er for gammel.',
    'unhealthy_backup_found_unknown' => 'Beklager, kunne ikke finne nøyaktig årsak.',
    'unhealthy_backup_found_full' => 'Backups bruker for mye lagringsplass. Nåværende diskbruk er :disk_usage, som er mer enn den tillatte grensen på :disk_limit.',

    'no_backups_info' => 'Ingen sikkerhetskopier ble gjort ennå',
    'application_name' => 'Programnavn',
    'backup_name' => 'Navn på sikkerhetskopi',
    'disk' => 'Disk',
    'newest_backup_size' => 'Nyeste backup-størrelse',
    'number_of_backups' => 'Antall sikkerhetskopier',
    'total_storage_used' => 'Total lagring brukt',
    'newest_backup_date' => 'Nyeste backup-størrelse',
    'oldest_backup_date' => 'Eldste sikkerhetskopistørrelse',
];
