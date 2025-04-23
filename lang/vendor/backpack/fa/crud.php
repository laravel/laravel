<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backpack Crud Language Lines‌ - Persian
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    // Forms
    'save_action_save_and_new' => 'ذخیره و مورد جدید',
    'save_action_save_and_edit' => 'ذخیره و ویرایش این مورد',
    'save_action_save_and_back' => 'ذخیره و بازگشت',
    'save_action_changed_notification' => 'رفتار پیش فرض پس از ذخیره سازی تغییر کرده است.',

    // Create form
    'add' => 'افزودن',
    'back_to_all' => 'بازگشت به همه ',
    'cancel' => 'انصراف',
    'add_a_new' => 'افزودن یک چیز جدید ',

    // Edit form
    'edit' => 'ویرایش',
    'save' => 'ذخیره',

    // Translatable models
    'edit_translations' => 'ترجمه',
    'language' => 'زبان',

    // CRUD table view
    'all' => 'همه ',
    'in_the_database' => 'در پایگاه داده',
    'list' => 'لیست',
    'actions' => 'اقدامات',
    'preview' => 'پیش‌نمایش',
    'delete' => 'حذف',
    'admin' => 'مدیر',
    'details_row' => 'این ردیف جزئیات است. مطابق میل خود تغییر دهید.',
    'details_row_loading_error' => 'هنگام بارگذاری جزئیات خطایی روی داد. لطفا دوباره سعی کنید.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'آیا شما از حذف این مورد مطمين هستید؟',
    'delete_confirmation_title' => 'مورد حذف شد',
    'delete_confirmation_message' => 'مورد مورد نظر با موفقیت حذف شد.',
    'delete_confirmation_not_title' => 'حذف نشد',
    'delete_confirmation_not_message' => 'خطایی وجود داشت. مورد مورد نظر ممکن است حذف نشده باشد.',
    'delete_confirmation_not_deleted_title' => 'حذف نشد',
    'delete_confirmation_not_deleted_message' => 'هیچ اتفاقی نیفتاده مورد شما ایمن است.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'هیچ ورودی انتخاب نشده است',
    'bulk_no_entries_selected_message' => 'لطفاً یک یا چند مورد را انتخاب کنید تا یک عمل انبوه بر روی آنها انجام شود.',

    // Bulk confirmation
    'bulk_delete_are_you_sure' => 'آیا مطمئن هستید که می خواهید :number مورد را حذف کنید؟',
    'bulk_delete_sucess_title' => 'موارد حذف شد',
    'bulk_delete_sucess_message' => ' موارد حذف شدند',
    'bulk_delete_error_title' => 'حذف نشد',
    'bulk_delete_error_message' => 'یک یا چند مورد قابل حذف نیستند',

    // Ajax errors
    'ajax_error_title' => 'خطا',
    'ajax_error_text' => 'خطا در بارگذاری صفحه. صفحه را تازه کنید. ',

    // DataTables translation
    'emptyTable' => 'داده‌ای در جدول وجود ندارد',
    'info' => 'نمایش  _START_ تا _END_ از _TOTAL_ مورد',
    'infoEmpty' => 'نمایش ۰ تا ۰ از ۰ مورد',
    'infoFiltered' => '(فیلتر شده از _MAX_ مورد)',
    'infoPostFix' => '.',
    'thousands' => '،',
    'lengthMenu' => '_MENU_ رکورد در صفحه',
    'loadingRecords' => 'درحال بارگذاری...',
    'processing' => 'درحال پردازش...',
    'search' => 'جستجو',
    'zeroRecords' => 'مورد مطابقت داده شده یافت نشد',
    'paginate' => [
        'first' => 'اولین',
        'last' => 'آخرین',
        'next' => 'بعدی',
        'previous' => 'قبلی',
    ],
    'aria' => [
        'sortAscending' => ': برای مرتب سازی صعود ستون فعال کنید',
        'sortDescending' => ': برای مرتب سازی نزولی ستون فعال کنید',
    ],
    'export' => [
        'export' => 'خروجی',
        'copy' => 'کپی',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'چاپ',
        'column_visibility' => 'ستون‌های نمایان',
    ],

    // global crud - errors
    'unauthorized_access' => 'دسترسی غیرمجاز - شما مجوزهای لازم برای دیدن این صفحه را ندارید.',
    'please_fix' => 'لطفا این خطاها را برطرف کنید:',

    // global crud - success / error notification bubbles
    'insert_success' => 'مورد مورد نظر با موفقیت اضافه شد.',
    'update_success' => 'مورد با موفقیت تغییر یافت.',

    // CRUD reorder view
    'reorder' => 'ترتیب دوباره',
    'reorder_text' => 'Use drag&drop to reorder.',
    'reorder_success_title' => 'انجام شد',
    'reorder_success_message' => 'ترتیب شما ذخیره شد.',
    'reorder_error_title' => 'خطا',
    'reorder_error_message' => 'ترتیب شما ذخیره نشده است',

    // CRUD yes/no
    'yes' => 'Yes',
    'no' => 'No',

    // CRUD filters navbar view
    'filters' => 'فیلترها',
    'toggle_filters' => 'تغییر فیلترها',
    'remove_filters' => 'حذف فیلترها',

    // Fields
    'browse_uploads' => 'مرور بارگذاری‌ها',
    'select_all' => 'انتخاب همه',
    'select_files' => 'انتخاب پرونده‌ها',
    'select_file' => 'انتخاب پرونده',
    'clear' => 'پاک کردن',
    'page_link' => 'پیوند به صفحه',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'پیوند داخلی',
    'internal_link_placeholder' => 'Internal slug. Ex: \'admin/page\' (no quotes) for \':url\'',
    'external_link' => 'پیوند خارجی',
    'choose_file' => 'انتخاب پرونده',

    //Table field
    'table_cant_add' => 'نمی توان :entity جدید اضافه کرد',
    'table_max_reached' => 'به حداکثر مقدار :max رسیده است',

    // File manager
    'file_manager' => 'مدیریت پرونده',
];
