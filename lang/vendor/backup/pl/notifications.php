<?php

return [
    'exception_message' => 'Błąd: :message',
    'exception_trace' => 'Zrzut błędu: :trace',
    'exception_message_title' => 'Błąd',
    'exception_trace_title' => 'Zrzut błędu',

    'backup_failed_subject' => 'Tworzenie kopii zapasowej aplikacji :application_name nie powiodło się',
    'backup_failed_body' => 'Ważne: Wystąpił błąd podczas tworzenia kopii zapasowej aplikacji :application_name',

    'backup_successful_subject' => 'Pomyślnie utworzono kopię zapasową aplikacji :application_name',
    'backup_successful_subject_title' => 'Nowa kopia zapasowa!',
    'backup_successful_body' => 'Wspaniała wiadomość, nowa kopia zapasowa aplikacji :application_name została pomyślnie utworzona na dysku o nazwie :disk_name.',

    'cleanup_failed_subject' => 'Czyszczenie kopii zapasowych aplikacji :application_name nie powiodło się.',
    'cleanup_failed_body' => 'Wystąpił błąd podczas czyszczenia kopii zapasowej aplikacji :application_name',

    'cleanup_successful_subject' => 'Kopie zapasowe aplikacji :application_name zostały pomyślnie wyczyszczone',
    'cleanup_successful_subject_title' => 'Kopie zapasowe zostały pomyślnie wyczyszczone!',
    'cleanup_successful_body' => 'Czyszczenie kopii zapasowych aplikacji :application_name na dysku :disk_name zakończone sukcesem.',

    'healthy_backup_found_subject' => 'Kopie zapasowe aplikacji :application_name na dysku :disk_name są poprawne',
    'healthy_backup_found_subject_title' => 'Kopie zapasowe aplikacji :application_name są poprawne',
    'healthy_backup_found_body' => 'Kopie zapasowe aplikacji :application_name są poprawne. Dobra robota!',

    'unhealthy_backup_found_subject' => 'Ważne: Kopie zapasowe aplikacji :application_name są niepoprawne',
    'unhealthy_backup_found_subject_title' => 'Ważne: Kopie zapasowe aplikacji :application_name są niepoprawne. :problem',
    'unhealthy_backup_found_body' => 'Kopie zapasowe aplikacji :application_name na dysku :disk_name są niepoprawne.',
    'unhealthy_backup_found_not_reachable' => 'Miejsce docelowe kopii zapasowej nie jest osiągalne. :error',
    'unhealthy_backup_found_empty' => 'W aplikacji nie ma żadnej kopii zapasowych tej aplikacji.',
    'unhealthy_backup_found_old' => 'Ostatnia kopia zapasowa wykonania dnia :date jest zbyt stara.',
    'unhealthy_backup_found_unknown' => 'Niestety, nie można ustalić dokładnego błędu.',
    'unhealthy_backup_found_full' => 'Kopie zapasowe zajmują zbyt dużo miejsca. Obecne użycie dysku :disk_usage jest większe od ustalonego limitu :disk_limit.',

    'no_backups_info' => 'Nie utworzono jeszcze kopii zapasowych',
    'application_name' => 'Nazwa aplikacji',
    'backup_name' => 'Nazwa kopii zapasowej',
    'disk' => 'Dysk',
    'newest_backup_size' => 'Najnowszy rozmiar kopii zapasowej',
    'number_of_backups' => 'Liczba kopii zapasowych',
    'total_storage_used' => 'Całkowite wykorzystane miejsce',
    'newest_backup_date' => 'Najnowszy rozmiar kopii zapasowej',
    'oldest_backup_date' => 'Najstarszy rozmiar kopii zapasowej',
];
