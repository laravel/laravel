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
    'save_action_save_and_new' => 'Запиши и добави нов',
    'save_action_save_and_edit' => 'Запиши и поправи текущия запис',
    'save_action_save_and_back' => 'Запиши и се върни назад',
    'save_action_changed_notification' => 'Действието след запис по подразбиране слез запис беше променено.',

    // Create form
    'add' => 'Добави',
    'back_to_all' => 'Назад към записите ',
    'cancel' => 'Отказ',
    'add_a_new' => 'Добави нов запис ',

    // Create form - advanced options
    'after_saving' => 'След запис',
    'go_to_the_table_view' => 'Върни се към записите',
    'let_me_add_another_item' => 'Нека добавя отново',
    'edit_the_new_item' => 'Поправи току що направеният запис',

    // Edit form
    'edit' => 'Поправи',
    'save' => 'Запиши',

    // CRUD table view
    'all' => 'Всички ',
    'in_the_database' => 'В базите данни',
    'list' => 'Лист',
    'actions' => 'Действия',
    'preview' => 'Преглед',
    'delete' => 'Изтрии',
    'admin' => 'Admin',
    'details_row' => 'Това е колонката с детайли. Променяи както желаеш.',
    'details_row_loading_error' => 'Възникна грешка при зареджането на детайлите. Моля опитайте отново.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Сигурни ли сте, че искате да изтриете този запис ?',
    'delete_confirmation_title' => 'Запис изтрит',
    'delete_confirmation_message' => 'Записът беше успешно изтрит.',
    'delete_confirmation_not_title' => 'НЕ Е ИЗТРИТ',
    'delete_confirmation_not_message' => 'Възникна грешка. Записът не бе изтрит.',
    'delete_confirmation_not_deleted_title' => 'Не е изтрит',
    'delete_confirmation_not_deleted_message' => 'Споко :). Записът е на сигурно място.',

    // DataTables translation
    'emptyTable' => 'Няма намерени резултати',
    'info' => 'Показване на резултати от _START_ до _END_ от общо _TOTAL_',
    'infoEmpty' => '',
    'infoFiltered' => '(филтрирани от общо _MAX_ резултата))',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ records per page',
    'loadingRecords' => 'Зареждам...',
    'processing' => 'Обработка на резултатите...',
    'search' => 'Търсене',
    'zeroRecords' => 'Няма намерени резултати',
    'paginate' => [
        'first' => 'Първа',
        'last' => 'Последна',
        'next' => 'Следваща',
        'previous' => 'Предишна',
    ],
    'aria' => [
        'sortAscending' => ': Възходящ ред',
        'sortDescending' => ': Низходящ ред',
    ],

    // global crud - errors
    'unauthorized_access' => 'Неразрешен достъп - нямате необходимите разрешения, за да видите тази страница.',
    'please_fix' => 'Моля, поправете следните грешки:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Елементът е добавен успешно.',
    'update_success' => 'Елементът е променен успешно.',

    // CRUD reorder view
    'reorder' => 'Пренареждане',
    'reorder_text' => 'Използвайте влачене и пускане, за да пренаредите.',
    'reorder_success_title' => 'Готово',
    'reorder_success_message' => 'Вашето нареждане е запазено.',
    'reorder_error_title' => 'Грешка',
    'reorder_error_message' => 'Вашето нареждане не е запазено..',

    // CRUD yes/no
    'yes' => 'Да',
    'no' => 'Не',

    // Fields
    'browse_uploads' => 'Browse uploads',
    'clear' => 'Изчисти',
    'page_link' => 'Линк на страницата',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Вътрешен линк',
    'internal_link_placeholder' => 'Вътрешен слъг. Пр: \'admin/page\' (без кавички) за \':url\'',
    'external_link' => 'Външен линк',
    'choose_file' => 'Избери файл',

    //Table field
    'table_cant_add' => 'Не може да се добави нов :entity',
    'table_max_reached' => 'Максимален брой :max достигнат',

];
