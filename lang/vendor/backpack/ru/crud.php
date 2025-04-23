<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backpack Crud Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    // Forms
    'save_action_save_and_new' => 'Сохранить и создать',
    'save_action_save_and_edit' => 'Сохранить и продолжить редактирование',
    'save_action_save_and_back' => 'Сохранить и выйти',
    'save_action_save_and_preview' => 'Сохранить и предпросмотр',
    'save_action_changed_notification' => 'Действие после сохранения было изменено',

    // Create form
    'add' => 'Добавить',
    'back_to_all' => 'Вернуться к списку',
    'cancel' => 'Отменить',
    'add_a_new' => 'Добавить новый(ую)',

    // Edit form
    'edit' => 'Редактировать',
    'save' => 'Сохранить',

    // Translatable models
    'edit_translations' => 'Перевод',
    'language' => 'Язык',

    // CRUD table view
    'all' => 'Все ',
    'in_the_database' => 'в базе данных',
    'list' => 'Список',
    'reset' => 'Сбросить',
    'actions' => 'Действия',
    'preview' => 'Предпросмотр',
    'delete' => 'Удалить',
    'admin' => 'Главная',
    'details_row' => 'Это строка сведений. Измените, пожалуйста',
    'details_row_loading_error' => 'Произошла ошибка при загрузке сведений. Повторите операцию.',
    'clone' => 'Создать копию',
    'clone_success' => '<strong>Успешно!</strong><br>Была добавлена новая запись с той же информацией',
    'clone_failure' => '<strong>Ошибка!</strong><br>Не получилось создать новую запись. Перезагрузите страницу и попробуйте еще раз',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Вы уверены, что хотите удалить эту запись?',
    'delete_confirmation_title' => 'Успешно!',
    'delete_confirmation_message' => 'Запись была удалена',
    'delete_confirmation_not_title' => 'Ошибка!',
    'delete_confirmation_not_message' => 'Запись не была удалена. Обновите страницу и повторите попытку',
    'delete_confirmation_not_deleted_title' => 'Не удалено',
    'delete_confirmation_not_deleted_message' => 'Запись осталась без изменений',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Записи не выбраны',
    'bulk_no_entries_selected_message' => 'Пожалуйста, выберите один или несколько элементов, чтобы выполнить массовое действие с ними',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'Вы уверены, что хотите удалить :number записей?',
    'bulk_delete_sucess_title' => 'Записи удалены',
    'bulk_delete_sucess_message' => ' элементов было удалено',
    'bulk_delete_error_title' => 'Ошибка!',
    'bulk_delete_error_message' => 'Некоторые из выбранных элементов не могут быть удалены',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'Подтвердите копирование записей(:number)',
    'bulk_clone_sucess_title' => 'Записи скопированы успешно!',
    'bulk_clone_sucess_message' => ' элементов было скопировано.',
    'bulk_clone_error_title' => 'Ошибка!',
    'bulk_clone_error_message' => 'Одна или более записей не может быть скопирована. Пожалуйста, попробуйте повторить операцию.',

    // Ajax errors
    'ajax_error_title' => 'Ошибка!',
    'ajax_error_text' => 'Пожалуйста, перезагрузите страницу',

    // DataTables translation
    'emptyTable' => 'В таблице нет доступных данных',
    'info' => 'Показано _START_ до _END_ из _TOTAL_ совпадений',
    'infoEmpty' => '',
    'infoFiltered' => '(отфильтровано из _MAX_ совпадений)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ записей на странице',
    'loadingRecords' => 'Загрузка...',
    'processing' => 'Обработка...',
    'search' => 'Поиск',
    'zeroRecords' => 'Совпадений не найдено',
    'paginate' => [
        'first' => 'Первая',
        'last' => 'Последняя',
        'next' => 'Следующая',
        'previous' => 'Предыдущая',
    ],
    'aria' => [
        'sortAscending' => ': нажмите для сортировки по возрастанию',
        'sortDescending' => ': нажмите для сортировки по убыванию',
    ],
    'export' => [
        'export' => 'Экспорт',
        'copy' => 'Копировать в буфер',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'На печать',
        'column_visibility' => 'Видимость колонок',
    ],

    // global crud - errors
    'unauthorized_access' => 'У Вас нет необходимых прав для просмотра этой страницы.',
    'please_fix' => 'Пожалуйста, исправьте следующие ошибки:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Запись была успешно добавлена.',
    'update_success' => 'Запись была успешно изменена.',

    // CRUD reorder view
    'reorder' => 'Изменить порядок',
    'reorder_text' => 'Используйте drag&drop для изменения порядка.',
    'reorder_success_title' => 'Готово',
    'reorder_success_message' => 'Порядок был сохранен.',
    'reorder_error_title' => 'Ошибка',
    'reorder_error_message' => 'Порядок не был сохранен.',

    // CRUD yes/no
    'yes' => 'Да',
    'no' => 'Нет',

    // CRUD filters navbar view
    'filters' => 'Фильтры',
    'toggle_filters' => 'Переключить фильтры',
    'remove_filters' => 'Очистить фильтры',
    'apply' => 'Принять',

    //filters language strings
    'today' => 'Сегодня',
    'yesterday' => 'Вчера',
    'last_7_days' => 'Последние 7 дней',
    'last_30_days' => 'Последние 30 дней',
    'this_month' => 'Текущий месяц',
    'last_month' => 'Последний месяц',
    'custom_range' => 'Выбрать даты',
    'weekLabel' => 'W',

    // Fields
    'browse_uploads' => 'Загрузить файлы',
    'select_all' => 'Выбрать все',
    'select_files' => 'Выбрать файлы',
    'select_file' => 'Выбрать файл',
    'clear' => 'Очистить',
    'page_link' => 'Ссылка на страницу',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Внутренняя ссылка',
    'internal_link_placeholder' => 'Внутренний путь. Например: \'admin/page\' (без кавычек) для \':url\'',
    'external_link' => 'Внешняя ссылка',
    'choose_file' => 'Выбрать файл',
    'new_item' => 'Новый элемент',
    'select_entry' => 'Выбрать запись',
    'select_entries' => 'Выбрать записи',
    'upload_multiple_files_selected' => 'Файлы выбраны. После сохранения они появятся выше.',

    //Table field
    'table_cant_add' => 'Не удалось добавить новую :entity',
    'table_max_reached' => 'Максимальное количество из :max достигнуто',

    // google_map
    'google_map_locate' => 'Получить мое местоположение',

    // File manager
    'file_manager' => 'Файловый менеджер',

    // InlineCreateOperation
    'related_entry_created_success' => 'Связанная запись создана и выбрана.',
    'related_entry_created_error' => 'Не удалось создать связанную запись.',
    'inline_saving' => 'Сохранение...',

    // returned when no translations found in select inputs
    'empty_translations' => '(пусто)',

    // The pivot selector required validation message
    'pivot_selector_required_validation_message' => 'Поле сводной таблицы является обязательным.',
];
