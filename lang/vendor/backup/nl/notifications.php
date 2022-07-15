<?php

return [
    'exception_message' => 'Fout bericht: :message',
    'exception_trace' => 'Fout trace: :trace',
    'exception_message_title' => 'Fout bericht',
    'exception_trace_title' => 'Fout trace',

    'backup_failed_subject' => 'Back-up van :application_name mislukt',
    'backup_failed_body' => 'Belangrijk: Er ging iets fout tijdens het maken van een back-up van :application_name',

    'backup_successful_subject' => 'Succesvolle nieuwe back-up van :application_name',
    'backup_successful_subject_title' => 'Succesvolle nieuwe back-up!',
    'backup_successful_body' => 'Goed nieuws, een nieuwe back-up van :application_name was succesvol aangemaakt op de schijf genaamd :disk_name.',

    'cleanup_failed_subject' => 'Het opschonen van de back-ups van :application_name is mislukt.',
    'cleanup_failed_body' => 'Er ging iets fout tijdens het opschonen van de back-ups van :application_name',

    'cleanup_successful_subject' => 'Opschonen van :application_name back-ups was succesvol.',
    'cleanup_successful_subject_title' => 'Opschonen van back-ups was succesvol!',
    'cleanup_successful_body' => 'Het opschonen van de :application_name back-ups op de schijf genaamd :disk_name was succesvol.',

    'healthy_backup_found_subject' => 'De back-ups voor :application_name op schijf :disk_name zijn gezond',
    'healthy_backup_found_subject_title' => 'De back-ups voor :application_name zijn gezond',
    'healthy_backup_found_body' => 'De back-ups voor :application_name worden als gezond beschouwd. Goed gedaan!',

    'unhealthy_backup_found_subject' => 'Belangrijk: De back-ups voor :application_name zijn niet meer gezond',
    'unhealthy_backup_found_subject_title' => 'Belangrijk: De back-ups voor :application_name zijn niet gezond. :problem',
    'unhealthy_backup_found_body' => 'De back-ups voor :application_name op schijf :disk_name zijn niet gezond.',
    'unhealthy_backup_found_not_reachable' => 'De back-upbestemming kon niet worden bereikt. :error',
    'unhealthy_backup_found_empty' => 'Er zijn geen back-ups van deze applicatie beschikbaar.',
    'unhealthy_backup_found_old' => 'De laatste back-up gemaakt op :date is te oud.',
    'unhealthy_backup_found_unknown' => 'Sorry, een exacte reden kon niet worden bepaald.',
    'unhealthy_backup_found_full' => 'De back-ups gebruiken te veel opslagruimte. Momenteel wordt er :disk_usage gebruikt wat hoger is dan de toegestane limiet van :disk_limit.',

    'no_backups_info' => 'Er zijn nog geen back-ups gemaakt',
    'application_name' => 'Naam van de toepassing',
    'backup_name' => 'Back-upnaam',
    'disk' => 'Schijf',
    'newest_backup_size' => 'Nieuwste back-upgrootte',
    'number_of_backups' => 'Aantal back-ups',
    'total_storage_used' => 'Totale gebruikte opslagruimte',
    'newest_backup_date' => 'Datum nieuwste back-up',
    'oldest_backup_date' => 'Datum oudste back-up',
];
