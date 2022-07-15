<?php

return [
    'exception_message' => 'Съобщение за изключение: :message',
    'exception_trace' => 'Проследяване на изключение: :trace',
    'exception_message_title' => 'Съобщение за изключение',
    'exception_trace_title' => 'Проследяване на изключение',

    'backup_failed_subject' => 'Неуспешно резервно копие на :application_name',
    'backup_failed_body' => 'Важно: Възникна грешка при архивиране на :application_name',

    'backup_successful_subject' => 'Успешно ново резервно копие на :application_name',
    'backup_successful_subject_title' => 'Успешно ново резервно копие!',
    'backup_successful_body' => 'Чудесни новини, ново резервно копие на :application_name беше успешно създадено на диска с име :disk_name.',

    'cleanup_failed_subject' => 'Почистването на резервните копия на :application_name не бе успешно.',
    'cleanup_failed_body' => 'Възникна грешка при почистването на резервните копия на :application_name',

    'cleanup_successful_subject' => 'Почистването на архивите на :application_name е успешно',
    'cleanup_successful_subject_title' => 'Почистването на резервните копия е успешно!',
    'cleanup_successful_body' => 'Почистването на резервни копия на :application_name на диска с име :disk_name беше успешно.',

    'healthy_backup_found_subject' => 'Резервните копия за :application_name на диск :disk_name са здрави',
    'healthy_backup_found_subject_title' => 'Резервните копия за :application_name са здрави',
    'healthy_backup_found_body' => 'Резервните копия за :application_name се считат за здрави. Добра работа!',

    'unhealthy_backup_found_subject' => 'Важно: Резервните копия за :application_name не са здрави',
    'unhealthy_backup_found_subject_title' => 'Важно: Резервните копия за :application_name не са здрави. :проблем',
    'unhealthy_backup_found_body' => 'Резервните копия за :application_name на диск :disk_name не са здрави.',
    'unhealthy_backup_found_not_reachable' => 'Дестинацията за резервни копия не може да бъде достигната. :грешка',
    'unhealthy_backup_found_empty' => 'Изобщо няма резервни копия на това приложение.',
    'unhealthy_backup_found_old' => 'Последното резервно копие, направено на :date, се счита за твърде старо.',
    'unhealthy_backup_found_unknown' => 'За съжаление не може да се определи точна причина.',
    'unhealthy_backup_found_full' => 'Резервните копия използват твърде много място за съхранение. Текущото използване е :disk_usage, което е по-високо от разрешеното ограничение на :disk_limit.',

    'no_backups_info' => 'Все още не са правени резервни копия',
    'application_name' => 'Име на приложението',
    'backup_name' => 'Име на резервно копие',
    'disk' => 'Диск',
    'newest_backup_size' => 'Най-новият размер на резервно копие',
    'number_of_backups' => 'Брой резервни копия',
    'total_storage_used' => 'Общо използвано дисково пространство',
    'newest_backup_date' => 'Най-нова дата на резервно копие',
    'oldest_backup_date' => 'Най-старата дата на резервно копие',
];
