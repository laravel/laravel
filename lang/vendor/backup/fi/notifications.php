<?php

return [
    'exception_message' => 'Virheilmoitus: :message',
    'exception_trace' => 'Virhe, jäljitys: :trace',
    'exception_message_title' => 'Virheilmoitus',
    'exception_trace_title' => 'Virheen jäljitys',

    'backup_failed_subject' => ':application_name varmuuskopiointi epäonnistui',
    'backup_failed_body' => 'HUOM!: :application_name varmuuskoipionnissa tapahtui virhe',

    'backup_successful_subject' => ':application_name varmuuskopioitu onnistuneesti',
    'backup_successful_subject_title' => 'Uusi varmuuskopio!',
    'backup_successful_body' => 'Hyviä uutisia! :application_name on varmuuskopioitu levylle :disk_name.',

    'cleanup_failed_subject' => ':application_name varmuuskopioiden poistaminen epäonnistui.',
    'cleanup_failed_body' => ':application_name varmuuskopioiden poistamisessa tapahtui virhe.',

    'cleanup_successful_subject' => ':application_name varmuuskopiot poistettu onnistuneesti',
    'cleanup_successful_subject_title' => 'Varmuuskopiot poistettu onnistuneesti!',
    'cleanup_successful_body' => ':application_name varmuuskopiot poistettu onnistuneesti levyltä :disk_name.',

    'healthy_backup_found_subject' => ':application_name varmuuskopiot levyllä :disk_name ovat kunnossa',
    'healthy_backup_found_subject_title' => ':application_name varmuuskopiot ovat kunnossa',
    'healthy_backup_found_body' => ':application_name varmuuskopiot ovat kunnossa. Hieno homma!',

    'unhealthy_backup_found_subject' => 'HUOM!: :application_name varmuuskopiot ovat vialliset',
    'unhealthy_backup_found_subject_title' => 'HUOM!: :application_name varmuuskopiot ovat vialliset. :problem',
    'unhealthy_backup_found_body' => ':application_name varmuuskopiot levyllä :disk_name ovat vialliset.',
    'unhealthy_backup_found_not_reachable' => 'Varmuuskopioiden kohdekansio ei ole saatavilla. :error',
    'unhealthy_backup_found_empty' => 'Tästä sovelluksesta ei ole varmuuskopioita.',
    'unhealthy_backup_found_old' => 'Viimeisin varmuuskopio, luotu :date, on liian vanha.',
    'unhealthy_backup_found_unknown' => 'Virhe, tarkempaa tietoa syystä ei valitettavasti ole saatavilla.',
    'unhealthy_backup_found_full' => 'Varmuuskopiot vievät liikaa levytilaa. Tällä hetkellä käytössä :disk_usage, mikä on suurempi kuin sallittu tilavuus (:disk_limit).',

    'no_backups_info' => 'Varmuuskopioita ei vielä tehty',
    'application_name' => 'Sovelluksen nimi',
    'backup_name' => 'Varmuuskopion nimi',
    'disk' => 'Levy',
    'newest_backup_size' => 'Uusin varmuuskopion koko',
    'number_of_backups' => 'Varmuuskopioiden määrä',
    'total_storage_used' => 'Käytetty tallennustila yhteensä',
    'newest_backup_date' => 'Uusin varmuuskopion koko',
    'oldest_backup_date' => 'Vanhin varmuuskopion koko',
];
