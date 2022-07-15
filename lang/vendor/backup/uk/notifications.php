<?php

return [
    'exception_message' => 'Повідомлення про помилку: :message',
    'exception_trace' => 'Деталі помилки: :trace',
    'exception_message_title' => 'Повідомлення помилки',
    'exception_trace_title' => 'Деталі помилки',

    'backup_failed_subject' => 'Не вдалось зробити резервну копію :application_name',
    'backup_failed_body' => 'Увага: Трапилась помилка під час резервного копіювання :application_name',

    'backup_successful_subject' => 'Успішне резервне копіювання :application_name',
    'backup_successful_subject_title' => 'Успішно створена резервна копія!',
    'backup_successful_body' => 'Чудова новина, нова резервна копія :application_name успішно створена і збережена на диск :disk_name.',

    'cleanup_failed_subject' => 'Не вдалось очистити резервні копії :application_name',
    'cleanup_failed_body' => 'Сталася помилка під час очищення резервних копій :application_name',

    'cleanup_successful_subject' => 'Успішне очищення від резервних копій :application_name',
    'cleanup_successful_subject_title' => 'Очищення резервних копій пройшло вдало!',
    'cleanup_successful_body' => 'Очищенно від старих резервних копій :application_name на диску :disk_name пойшло успішно.',

    'healthy_backup_found_subject' => 'Резервна копія :application_name з диску :disk_name установлена',
    'healthy_backup_found_subject_title' => 'Резервна копія :application_name установлена',
    'healthy_backup_found_body' => 'Резервна копія :application_name успішно установлена. Хороша робота!',

    'unhealthy_backup_found_subject' => 'Увага: резервна копія :application_name не установилась',
    'unhealthy_backup_found_subject_title' => 'Увага: резервна копія для :application_name не установилась. :problem',
    'unhealthy_backup_found_body' => 'Резервна копія для :application_name на диску :disk_name не установилась.',
    'unhealthy_backup_found_not_reachable' => 'Резервна копія не змогла установитись. :error',
    'unhealthy_backup_found_empty' => 'Резервні копії для цього додатку відсутні.',
    'unhealthy_backup_found_old' => 'Останнє резервне копіювання створено :date є застарілим.',
    'unhealthy_backup_found_unknown' => 'Вибачте, але ми не змогли визначити точну причину.',
    'unhealthy_backup_found_full' => 'Резервні копії використовують занадто багато пам`яті. Використовується :disk_usage що вище за допустиму межу :disk_limit.',

    'no_backups_info' => 'Резервних копій ще не було зроблено',
    'application_name' => 'Назва програми',
    'backup_name' => 'Резервне ім’я',
    'disk' => 'Диск',
    'newest_backup_size' => 'Найновіший розмір резервної копії',
    'number_of_backups' => 'Кількість резервних копій',
    'total_storage_used' => 'Загальний обсяг використаного сховища',
    'newest_backup_date' => 'Найновіший розмір резервної копії',
    'oldest_backup_date' => 'Найстаріший розмір резервної копії',
];
