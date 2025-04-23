<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backpack Crud Language Lines - Arabic
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    | Author: https://github.com/allam2002
    | Updating: https://github.com/EGYWEB-Mohamed
    |
    */

    // Forms
    'save_action_save_and_new' => 'حفظ وأضف عنصر الجديد',
    'save_action_save_and_edit' => 'حفظ وتحرير هذا العنصر',
    'save_action_save_and_back' => 'حفظ و العودة',
    'save_action_save_and_preview' => 'حفظ و معاينة',
    'save_action_changed_notification' => 'تم تغيير السلوك الافتراضي بعد الحفظ.',

    // Create form
    'add' => 'أضافة',
    'back_to_all' => 'العودة إلى الكل ',
    'cancel' => 'الغاء',
    'add_a_new' => 'أضف جديد ',

    // Edit form
    'edit' => 'تعديل',
    'save' => 'حفظ',

    // Translatable models
    'edit_translations' => 'ترجمة',
    'language' => 'لغة',

    // CRUD table view
    'all' => 'الكل ',
    'in_the_database' => 'في قاعدة البيانات',
    'list' => 'قائمة',
    'reset' => 'إعادة ضبط',
    'actions' => 'أجراءات',
    'preview' => 'معاينة',
    'delete' => 'حذف',
    'admin' => 'مسؤل',
    'details_row' => 'هذا هو صف التفاصيل, قم بتعديلة من فضلك. ',
    'details_row_loading_error' => 'حدث خطأ أثناء تحميل التفاصيل, حاول مرة اخري. ',
    'clone' => 'استنساخ',
    'clone_success' => '<strong>تم الاستنساخ</strong><br>تمت إضافة نسخة جديد ، بنفس المعلومات مثل هذا الصف.',
    'clone_failure' => '<strong>فشل الاستنساخ</strong><br>لا يمكن إنشاء نسخة جديد. ',

    // Confirmation messages and bubbles
    'delete_confirm' => 'هل أنت متأكد أنك تريد حذف هذا العنصر؟',
    'delete_confirmation_title' => 'تم حذف العنصر',
    'delete_confirmation_message' => 'تم حذف العنصر بنجاح.',
    'delete_confirmation_not_title' => 'لم يتم حذفه',
    'delete_confirmation_not_message' => 'هناك خطأ, العنصر ربما لم يتم حذفة. ',
    'delete_confirmation_not_deleted_title' => 'غير محذوف',
    'delete_confirmation_not_deleted_message' => 'لم يحدث شيء, العنصر بأمان. ',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'لم يتم تحديد العناصر',
    'bulk_no_entries_selected_message' => 'الرجاء تحديد عنصر واحد أو أكثر لتنفيذ إجراء جماعي عليهم.',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'هل أنت متأكد أنك تريد حذف عدد :number عناصر؟',
    'bulk_delete_sucess_title' => 'تم حذف المدخل',
    'bulk_delete_sucess_message' => ' تم حذف من العناصر',
    'bulk_delete_error_title' => 'فشل الحذف',
    'bulk_delete_error_message' => 'تعذر حذف عنصر أو أكثر',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'هل أنت متأكد من أنك تريد استنساخ هذه:number إدخالات؟',
    'bulk_clone_sucess_title' => 'إدخالات مستنسخة',
    'bulk_clone_sucess_message' => ' تم استنساخ العناصر.',
    'bulk_clone_error_title' => 'فشل الاستنساخ',
    'bulk_clone_error_message' => 'تعذر إنشاء إدخال أو أكثر. ',

    // Ajax errors
    'ajax_error_title' => 'خطأ',
    'ajax_error_text' => 'خطأ في تحميل الصفحة. ',

    // DataTables translation
    'emptyTable' => 'لا توجد بيانات متوفرة في الجدول',
    'info' => 'إظهار _START_ إلى _END_ من _TOTAL_ من الإدخالات',
    'infoEmpty' => 'لا توجد إدخالات',
    'infoFiltered' => '(تمت تصفيته من إجمالي إدخالات _MAX_)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ إدخالات لكل صفحة',
    'loadingRecords' => 'تحميل...',
    'processing' => 'معالجة...',
    'search' => 'بحث',
    'zeroRecords' => 'لم يتم العثور على إدخالات مطابقة',
    'paginate' => [
        'first' => 'أولاً',
        'last' => 'الاخير',
        'next' => 'التالي',
        'previous' => 'سابق',
    ],
    'aria' => [
        'sortAscending' => ': تفعيل لفرز العمود تصاعديا',
        'sortDescending' => ': تفعيل لفرز العمود تنازلياً',
    ],
    'export' => [
        'export' => 'تصدير',
        'copy' => 'نسخ',
        'excel' => 'اكسل',
        'csv' => 'CSV',
        'pdf' => 'بي دي إف',
        'print' => 'طباعة',
        'column_visibility' => 'الاعمد الظاهرة',
    ],

    // global crud - errors
    'unauthorized_access' => 'وصول غير مصرح به - ليس لديك الأذونات اللازمة لرؤية هذه الصفحة.',
    'please_fix' => 'يرجى تصحيح الأخطاء التالية:',

    // global crud - success / error notification bubbles
    'insert_success' => 'تمت إضافة العنصر بنجاح.',
    'update_success' => 'تم تعديل العنصر بنجاح.',

    // CRUD reorder view
    'reorder' => 'إعادة ترتيب',
    'reorder_text' => 'استخدم السحب',
    'reorder_success_title' => 'تم',
    'reorder_success_message' => 'تم حفظ طلبك.',
    'reorder_error_title' => 'خطأ',
    'reorder_error_message' => 'لم يتم حفظ طلبك.',

    // CRUD yes/no
    'yes' => 'نعم',
    'no' => 'لا',

    // CRUD filters navbar view
    'filters' => 'الفلاتر',
    'toggle_filters' => 'تبديل الفلاتر',
    'remove_filters' => 'إزالة الفلاتر',
    'apply' => 'يتقدم',

    //filters language strings
    'today' => 'اليوم',
    'yesterday' => 'أمس',
    'last_7_days' => 'اخر 7 ايام',
    'last_30_days' => 'آخر 30 يومًا',
    'this_month' => 'هذا الشهر',
    'last_month' => 'الشهر الماضي',
    'custom_range' => 'نطاق مخصص',
    'weekLabel' => 'أسب',

    // Fields
    'browse_uploads' => 'تصفح التحميلات',
    'select_all' => 'اختر الكل',
    'select_files' => 'اختر الملفات',
    'select_file' => 'حدد الملف',
    'clear' => 'تهيئة',
    'page_link' => 'رابط الصفحة',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'ارتباط داخلي',
    'internal_link_placeholder' => "أختصار داخلي. Ex: \'admin/page\' (بدون تنصيص) لاجل \':url\'",
    'external_link' => 'رابط خارجي',
    'choose_file' => 'اختر ملف',
    'new_item' => 'عنصر جديد',
    'select_entry' => 'حدد إدخالاً',
    'select_entries' => 'حدد الإدخالات',
    'upload_multiple_files_selected' => 'تم تحديد الملفات, بعد الحذف، ستظهر بالاعلي.',

    //Table field
    'table_cant_add' => 'لا يمكن إضافة :entity',
    'table_max_reached' => 'وصلت للحد الاقصي :max',

    // google_map
    'google_map_locate' => 'احصل على موقعي',

    // File manager
    'file_manager' => 'مدير الملفات',

    // InlineCreateOperation
    'related_entry_created_success' => 'تم إنشاء المدخل واختياره.',
    'related_entry_created_error' => 'تعذر إنشاء الإدخال.',
    'inline_saving' => 'جاري الحفظ...',

    // returned when no translations found in select inputs
    'empty_translations' => '(فارغ)',

    // The pivot selector required validation message
    'pivot_selector_required_validation_message' => 'الحقل المحوري مطلوب.',
];
