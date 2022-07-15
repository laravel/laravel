<?php

return [
    'exception_message' => 'Zpráva výjimky: :message',
    'exception_trace' => 'Stopa výjimky: :trace',
    'exception_message_title' => 'Zpráva výjimky',
    'exception_trace_title' => 'Stopa výjimky',

    'backup_failed_subject' => 'Záloha :application_name neuspěla',
    'backup_failed_body' => 'Důležité: Při záloze :application_name se vyskytla chyba',

    'backup_successful_subject' => 'Úspěšná nová záloha :application_name',
    'backup_successful_subject_title' => 'Úspěšná nová záloha!',
    'backup_successful_body' => 'Dobrá zpráva, na disku jménem :disk_name byla úspěšně vytvořena nová záloha :application_name.',

    'cleanup_failed_subject' => 'Vyčištění záloh :application_name neuspělo.',
    'cleanup_failed_body' => 'Při čištění záloh :application_name se vyskytla chyba',

    'cleanup_successful_subject' => 'Vyčištění záloh :application_name úspěšné',
    'cleanup_successful_subject_title' => 'Vyčištění záloh bylo úspěšné!',
    'cleanup_successful_body' => 'Vyčištění záloh :application_name na disku jménem :disk_name bylo úspěšné.',

    'healthy_backup_found_subject' => 'Zálohy pro :application_name na disku :disk_name jsou zdravé',
    'healthy_backup_found_subject_title' => 'Zálohy pro :application_name jsou zdravé',
    'healthy_backup_found_body' => 'Zálohy pro :application_name jsou považovány za zdravé. Dobrá práce!',

    'unhealthy_backup_found_subject' => 'Důležité: Zálohy pro :application_name jsou nezdravé',
    'unhealthy_backup_found_subject_title' => 'Důležité: Zálohy pro :application_name jsou nezdravé. :problem',
    'unhealthy_backup_found_body' => 'Zálohy pro :application_name na disku :disk_name jsou nezdravé.',
    'unhealthy_backup_found_not_reachable' => 'Nelze se dostat k cíli zálohy. :error',
    'unhealthy_backup_found_empty' => 'Tato aplikace nemá vůbec žádné zálohy.',
    'unhealthy_backup_found_old' => 'Poslední záloha vytvořená dne :date je považována za příliš starou.',
    'unhealthy_backup_found_unknown' => 'Omlouváme se, nemůžeme určit přesný důvod.',
    'unhealthy_backup_found_full' => 'Zálohy zabírají příliš mnoho místa na disku. Aktuální využití disku je :disk_usage, což je vyšší než povolený limit :disk_limit.',

    'no_backups_info' => 'Zatím nebyly vytvořeny žádné zálohy',
    'application_name' => 'Název aplikace',
    'backup_name' => 'Název zálohy',
    'disk' => 'Disk',
    'newest_backup_size' => 'Velikost nejnovější zálohy',
    'number_of_backups' => 'Počet záloh',
    'total_storage_used' => 'Celková využitá kapacita úložiště',
    'newest_backup_date' => 'Datum nejnovější zálohy',
    'oldest_backup_date' => 'Datum nejstarší zálohy',
];
