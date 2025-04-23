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
    'save_action_save_and_new' => 'Зберегти та створити',
    'save_action_save_and_edit' => 'Зберегти та продовжити редагування',
    'save_action_save_and_back' => 'Зберегти і вийти',
    'save_action_save_and_preview' => 'Зберегти та переглянути',
    'save_action_changed_notification' => 'Поведінка за замовчуванням після збереження була змінена.',

    // Create form
    'add' => 'Додати',
    'back_to_all' => 'Назад до всіх ',
    'cancel' => 'Скасувати',
    'add_a_new' => 'Додати новий ',

    // Edit form
    'edit' => 'Редагувати',
    'save' => 'Зберегти',

    // Translatable models
    'edit_translations' => 'Переклад',
    'language' => 'Мова',

    // CRUD table view
    'all' => 'Всі ',
    'in_the_database' => 'в базі даних',
    'list' => 'Список',
    'reset' => 'Скинути',
    'actions' => 'Дії',
    'preview' => 'Попередній перегляд',
    'delete' => 'Видалити',
    'admin' => 'Admin',
    'details_row' => 'Це рядок з деталями. Змінюйте як завгодно.',
    'details_row_loading_error' => 'Під час завантаження деталей сталася помилка. Повторіть спробу.',
    'clone' => 'Створити копію',
    'clone_success' => '<strong>Успіх!</strong><br>Додано новий запис із тією ж інформацією, що і цей.',
    'clone_failure' => '<strong>Помилка!</strong><br>Не вдалося створити новий запис. Будь ласка спробуйте ще раз.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Ви впевнені, що хочете видалити цей елемент?',
    'delete_confirmation_title' => 'Запис видалено',
    'delete_confirmation_message' => 'Запис успішно видалено.',
    'delete_confirmation_not_title' => 'НЕ видалено',
    'delete_confirmation_not_message' => 'Сталася помилка. Будь ласка спробуйте ще раз.',
    'delete_confirmation_not_deleted_title' => 'Не видалено',
    'delete_confirmation_not_deleted_message' => 'Нічого не трапилося. Ваш запис у безпеці.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Записів не вибрано',
    'bulk_no_entries_selected_message' => 'Виберіть один або кілька записів, щоб виконати групову дію над ними.',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'Ви впевнені, що хочете видалити ці :number записів?',
    'bulk_delete_sucess_title' => 'Записи видалено',
    'bulk_delete_sucess_message' => ' записів було видалено',
    'bulk_delete_error_title' => 'Помилка',
    'bulk_delete_error_message' => 'Не вдалося видалити один або кілька записів',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'Ви впевнені, що хочете скопіювати ці :number записів?',
    'bulk_clone_sucess_title' => 'Записи скопійовано',
    'bulk_clone_sucess_message' => ' записів було скопійовано.',
    'bulk_clone_error_title' => 'Помилка копіювання',
    'bulk_clone_error_message' => 'Не вдалося створити один або кілька записів. Будь ласка спробуйте ще раз.',

    // Ajax errors
    'ajax_error_title' => 'Помилка',
    'ajax_error_text' => 'Помилка завантаження сторінки. Оновіть сторінку.',

    // DataTables translation
    'emptyTable' => 'У таблиці відсутні дані',
    'info' => 'Відображено з _START_ по _END_ з _TOTAL_ записів',
    'infoEmpty' => 'Немає записів',
    'infoFiltered' => '(відфільтровано з _MAX_ записів)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ записів на сторінці',
    'loadingRecords' => 'Завантаження...',
    'processing' => 'Обробка...',
    'search' => 'Пошук',
    'zeroRecords' => 'Відповідних записів не знайдено',
    'paginate' => [
        'first' => 'Перший',
        'last' => 'Останній',
        'next' => 'Наступний',
        'previous' => 'Попередній',
    ],
    'aria' => [
        'sortAscending' => ': сортувати стовпець за зростанням',
        'sortDescending' => ': сортувати стовпець за спаданням',
    ],
    'export' => [
        'export' => 'Експорт',
        'copy' => 'Копіювати',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Друк',
        'column_visibility' => 'Видимість стовпців',
    ],

    // global crud - errors
    'unauthorized_access' => 'Несанкціонований доступ - у вас немає необхідних прав, щоб побачити цю сторінку.',
    'please_fix' => 'Виправте наступні помилки:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Запис успішно додано.',
    'update_success' => 'Запис успішно змінено.',

    // CRUD reorder view
    'reorder' => 'Змінити порядок',
    'reorder_text' => 'Використовуйте перетягування, щоб змінити порядок.',
    'reorder_success_title' => 'Готово',
    'reorder_success_message' => 'Порядок був збережений.',
    'reorder_error_title' => 'Помилка',
    'reorder_error_message' => 'Порядок не було збережено.',

    // CRUD yes/no
    'yes' => 'Так',
    'no' => 'Ні',

    // CRUD filters navbar view
    'filters' => 'Фільтри',
    'toggle_filters' => 'Переключити фільтри',
    'remove_filters' => 'Очистити фільтри',
    'apply' => 'Прийняти',

    //filters language strings
    'today' => 'Сьогодні',
    'yesterday' => 'Вчора',
    'last_7_days' => 'Останні 7 днів',
    'last_30_days' => 'Останні 30 днів',
    'this_month' => 'Цього місяця',
    'last_month' => 'Останній місяць',
    'custom_range' => 'Вибрати дати',
    'weekLabel' => 'W',

    // Fields
    'browse_uploads' => 'Завантажити файли',
    'select_all' => 'Вибрати все',
    'select_files' => 'Вибрати файли',
    'select_file' => 'Вибрати файл',
    'clear' => 'Очистити',
    'page_link' => 'Посилання на сторінку',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Внутрішнє посилання',
    'internal_link_placeholder' => 'Внутрішній шлях. Наприклад: \'admin/page\' (без лапок) для \':url\'',
    'external_link' => 'Зовнішнє посилання',
    'choose_file' => 'Вибрати файл',
    'new_item' => 'Новий елемент',
    'select_entry' => 'Вибрати запис',
    'select_entries' => 'Вибрати записи',
    'upload_multiple_files_selected' => 'Вибрані файли. Після збереження вони з\'являться вище.',

    //Table field
    'table_cant_add' => 'Не вдається додати нове :entity',
    'table_max_reached' => 'Максимальна кількість з :max досягнута',

    // google_map
    'google_map_locate' => 'Отримати моє місцезнаходження',

    // File manager
    'file_manager' => 'Файловий менеджер',

    // InlineCreateOperation
    'related_entry_created_success' => 'Пов\'язаний запис створено та вибрано.',
    'related_entry_created_error' => 'Не вдалося створити пов\'язаний запис.',
    'inline_saving' => 'Збереження...',

    // returned when no translations found in select inputs
    'empty_translations' => '(порожньо)',

    // The pivot selector required validation message
    'pivot_selector_required_validation_message' => 'Поле зведеної таблиці є обов\'язковим.',
];
