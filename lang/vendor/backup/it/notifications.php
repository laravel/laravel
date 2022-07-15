<?php

return [
    'exception_message' => 'Messaggio dell\'eccezione: :message',
    'exception_trace' => 'Traccia dell\'eccezione: :trace',
    'exception_message_title' => 'Messaggio dell\'eccezione',
    'exception_trace_title' => 'Traccia dell\'eccezione',

    'backup_failed_subject' => 'Fallito il backup di :application_name',
    'backup_failed_body' => 'Importante: Si è verificato un errore durante il backup di :application_name',

    'backup_successful_subject' => 'Creato nuovo backup di :application_name',
    'backup_successful_subject_title' => 'Nuovo backup creato!',
    'backup_successful_body' => 'Grande notizia, un nuovo backup di :application_name è stato creato con successo sul disco :disk_name.',

    'cleanup_failed_subject' => 'Pulizia dei backup di :application_name fallita.',
    'cleanup_failed_body' => 'Si è verificato un errore durante la pulizia dei backup di :application_name',

    'cleanup_successful_subject' => 'Pulizia dei backup di :application_name avvenuta con successo',
    'cleanup_successful_subject_title' => 'Pulizia dei backup avvenuta con successo!',
    'cleanup_successful_body' => 'La pulizia dei backup di :application_name sul disco :disk_name è avvenuta con successo.',

    'healthy_backup_found_subject' => 'I backup per :application_name sul disco :disk_name sono sani',
    'healthy_backup_found_subject_title' => 'I backup per :application_name sono sani',
    'healthy_backup_found_body' => 'I backup per :application_name sono considerati sani. Bel Lavoro!',

    'unhealthy_backup_found_subject' => 'Importante: i backup per :application_name sono corrotti',
    'unhealthy_backup_found_subject_title' => 'Importante: i backup per :application_name sono corrotti. :problem',
    'unhealthy_backup_found_body' => 'I backup per :application_name sul disco :disk_name sono corrotti.',
    'unhealthy_backup_found_not_reachable' => 'Impossibile raggiungere la destinazione di backup. :error',
    'unhealthy_backup_found_empty' => 'Non esiste alcun backup di questa applicazione.',
    'unhealthy_backup_found_old' => 'L\'ultimo backup fatto il :date è considerato troppo vecchio.',
    'unhealthy_backup_found_unknown' => 'Spiacenti, non è possibile determinare una ragione esatta.',
    'unhealthy_backup_found_full' => 'I backup utilizzano troppa memoria. L\'utilizzo corrente è :disk_usage che è superiore al limite consentito di :disk_limit.',

    'no_backups_info' => 'Non sono stati ancora effettuati backup',
    'application_name' => 'Nome dell\'applicazione',
    'backup_name' => 'Nome di backup',
    'disk' => 'Disco',
    'newest_backup_size' => 'Dimensione backup più recente',
    'number_of_backups' => 'Numero di backup',
    'total_storage_used' => 'Spazio di archiviazione totale utilizzato',
    'newest_backup_date' => 'Dimensione backup più recente',
    'oldest_backup_date' => 'Dimensione backup più vecchia',
];
