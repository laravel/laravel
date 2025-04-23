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
    'save_action_save_and_new' => '儲存並新增',
    'save_action_save_and_edit' => '儲存並繼續編輯',
    'save_action_save_and_back' => '儲存並返回',
    'save_action_save_and_preview' => '儲存並預覽',
    'save_action_changed_notification' => '儲存後的預設行為已更改。',

    // Create form
    'add' => '新增',
    'back_to_all' => '回到所有的 ',
    'cancel' => '取消',
    'add_a_new' => '新增一個 ',

    // Edit form
    'edit' => '編輯',
    'save' => '儲存',

    // Translatable models
    'edit_translations' => '翻譯',
    'language' => '語言',

    // CRUD table view
    'all' => '全部 ',
    'in_the_database' => '在資料庫中',
    'list' => '清單',
    'reset' => '重置',
    'actions' => '動作',
    'preview' => '預覽',
    'delete' => '刪除',
    'admin' => '管理員',
    'details_row' => '這是詳細內容列。你可以在這裡作出編輯。',
    'details_row_loading_error' => '當載入詳細內容時遇到錯誤。請重試。',
    'clone' => '複製',
    'clone_success' => '<strong>紀錄已複製</strong><br>與此紀錄內容一致的新紀錄已被新增。',
    'clone_failure' => '<strong>紀錄複製失敗</strong><br>無法新增複製的紀錄，請稍後再試。',

    // Confirmation messages and bubbles
    'delete_confirm' => '您確定要刪除此紀錄嗎？',
    'delete_confirmation_title' => '紀錄已刪除',
    'delete_confirmation_message' => '此紀錄已成功地刪除。',
    'delete_confirmation_not_title' => '紀錄未刪除',
    'delete_confirmation_not_message' => '發生錯誤，您的紀錄有可能並未成功刪除。',
    'delete_confirmation_not_deleted_title' => '紀錄未刪除',
    'delete_confirmation_not_deleted_message' => '沒有任何事情發生過，您的紀錄依然存在。',

    // Bulk actions
    'bulk_no_entries_selected_title' => '沒有選擇任何紀錄',
    'bulk_no_entries_selected_message' => '進行大量操作前，請選擇至少一項紀錄。',

    // Bulk delete
    'bulk_delete_are_you_sure' => '您確定要刪除 :number 項紀錄嗎？',
    'bulk_delete_sucess_title' => '紀錄已刪除',
    'bulk_delete_sucess_message' => ' 項紀錄已被刪除',
    'bulk_delete_error_title' => '刪除失敗',
    'bulk_delete_error_message' => '無法刪除一項或多項紀錄',

    // Bulk clone
    'bulk_clone_are_you_sure' => '您確定要複製 :number 項紀錄嗎？',
    'bulk_clone_sucess_title' => '紀錄已複製',
    'bulk_clone_sucess_message' => ' 項紀錄已被複製。',
    'bulk_clone_error_title' => '複製失敗',
    'bulk_clone_error_message' => '無法複製一項或多項紀錄，請稍後再試。',

    // Ajax errors
    'ajax_error_title' => '錯誤',
    'ajax_error_text' => '載入頁面時發生錯誤，請重新整理頁面。',

    // DataTables translation
    'emptyTable' => '資料表中無任何紀錄',
    'info' => '正在顯示 _TOTAL_ 項紀錄當中的第 _START_ 到 _END_ 項',
    'infoEmpty' => '沒有紀錄',
    'infoFiltered' => '（自 _TOTAL_ 項紀錄中篩選出來的紀錄）',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '每頁 _MENU_ 項紀錄',
    'loadingRecords' => '正在載入...',
    'processing' => '正在處理...',
    'search' => '搜尋',
    'zeroRecords' => '找不到符合的紀錄',
    'paginate' => [
        'first' => '第一頁',
        'last' => '最後一頁',
        'next' => '下一頁',
        'previous' => '上一頁',
    ],
    'aria' => [
        'sortAscending' => ': 以由小到大的方式排列',
        'sortDescending' => ': 以由大到小的方式排列',
    ],
    'export' => [
        'export' => '匯出',
        'copy' => '複製',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => '列印',
        'column_visibility' => '顯示欄位',
    ],

    // global crud - errors
    'unauthorized_access' => '未經授權的存取 - 您沒有權限瀏覽此頁面。',
    'please_fix' => '請修正以下的錯誤：',

    // global crud - success / error notification bubbles
    'insert_success' => '已成功新增此紀錄。',
    'update_success' => '已成功更新此紀錄。',

    // CRUD reorder view
    'reorder' => '重新排序',
    'reorder_text' => '請以拖曳的方式重新排序。',
    'reorder_success_title' => '完成',
    'reorder_success_message' => '您的排序已被儲存。',
    'reorder_error_title' => '錯誤',
    'reorder_error_message' => '您的排序並未被儲存。',

    // CRUD yes/no
    'yes' => '確定',
    'no' => '取消',

    // CRUD filters navbar view
    'filters' => '篩選',
    'toggle_filters' => '切換篩選',
    'remove_filters' => '移除篩選',
    'apply' => '套用',

    //filters language strings
    'today' => '今天',
    'yesterday' => '昨天',
    'last_7_days' => '最近7天',
    'last_30_days' => '最近30天',
    'this_month' => '這個月',
    'last_month' => '上個月',
    'custom_range' => '自訂範圍',
    'weekLabel' => '週',

    // Fields
    'browse_uploads' => '瀏覽已上傳的檔案',
    'select_all' => '選擇全部',
    'select_files' => '選擇檔案',
    'select_file' => '選擇檔案',
    'clear' => '清除',
    'page_link' => '頁面連結',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => '內部連結',
    'internal_link_placeholder' => '內部連結短碼，例如: \':url\' 後面加上 \'admin/page\' （去掉引號）',
    'external_link' => '外部連結',
    'choose_file' => '選擇檔案',
    'new_item' => '新項目',
    'select_entry' => '選擇一個項目',
    'select_entries' => '選擇多個項目',

    //Table field
    'table_cant_add' => '不能再增加 :entity',
    'table_max_reached' => '已達到 :max 項紀錄的上限',

    // File manager
    'file_manager' => '檔案管理',

    // InlineCreateOperation
    'related_entry_created_success' => '相關的紀錄已被新增並選擇。',
    'related_entry_created_error' => '無法新增相關的紀錄。',
];
