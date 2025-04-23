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
    'save_action_save_and_new' => '保存并新增',
    'save_action_save_and_edit' => '保存并继续编辑',
    'save_action_save_and_back' => '保存并返回',
    'save_action_save_and_preview' => '保存并预览',
    'save_action_changed_notification' => '保存后的默认行为已更改.',

    // Create form
    'add' => '新增',
    'back_to_all' => '回到所有 ',
    'cancel' => '取消',
    'add_a_new' => '新增一个 ',

    // Edit form
    'edit' => '编辑',
    'save' => '存储',

    // Translatable models
    'edit_translations' => '翻译',
    'language' => '语言',

    // CRUD table view
    'all' => '全部 ',
    'in_the_database' => '在数据库中',
    'list' => '列表',
    'reset' => '重置',
    'actions' => '操作',
    'preview' => '预览',
    'delete' => '删除',
    'admin' => '管理員',
    'details_row' => '这是详情列。你可以在这里做出编辑。',
    'details_row_loading_error' => '加载详情时出错。请重试。',
    'clone' => '复制',
    'clone_success' => '<strong>记录复制成功</strong><br>与此记录内容一致的新记录已被添加。',
    'clone_failure' => '<strong>复制失败</strong><br>新的记录没有被创建，请重试。',

    // Confirmation messages and bubbles
    'delete_confirm' => '你确定要删除这条记录吗？',
    'delete_confirmation_title' => '记录已被删除',
    'delete_confirmation_message' => '这条记录已被成功删除。',
    'delete_confirmation_not_title' => '记录未被删除',
    'delete_confirmation_not_message' => '尝试删除时发生错误。这项记录或未被成功删除。',
    'delete_confirmation_not_deleted_title' => '记录未被删除',
    'delete_confirmation_not_deleted_message' => '沒有任何事情发生过。你的这条记录纹丝未动。',

    // Bulk actions
    'bulk_no_entries_selected_title' => '没有选中任何记录',
    'bulk_no_entries_selected_message' => '执行批量操作前，请选择一条或多条记录.',

    // Bulk delete
    'bulk_delete_are_you_sure' => '确定要删除这 :number 条记录吗?',
    'bulk_delete_sucess_title' => '删除记录',
    'bulk_delete_sucess_message' => ' 记录已经被删除',
    'bulk_delete_error_title' => '删除失败',
    'bulk_delete_error_message' => '无法删除一条或多条记录',

    // Bulk clone
    'bulk_clone_are_you_sure' => '您确定要复制 :number 项纪录吗？',
    'bulk_clone_sucess_title' => '纪录已复制',
    'bulk_clone_sucess_message' => ' 项纪录已被复制。',
    'bulk_clone_error_title' => '复制失败',
    'bulk_clone_error_message' => '无法复制一项或多项纪录，请稍后再试。',

    // Ajax errors
    'ajax_error_title' => '错误',
    'ajax_error_text' => '加载页面时出错，请刷新页面。',

    // DataTables translation
    'emptyTable' => '数据库中没有相关记录',
    'info' => '正在显示 _TOTAL_ 个记录中的 _START_ 至 _END_ 项',
    'infoEmpty' => '',
    'infoFiltered' => '(自 _TOTAL_ 个记录中筛选出来的记录)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '每页 _MENU_ 条记录',
    'loadingRecords' => '加载中...',
    'processing' => '处理中...',
    'search' => '搜索',
    'zeroRecords' => '找不到相关记录',
    'paginate' => [
        'first' => '首页',
        'last' => '尾页',
        'next' => '下一页',
        'previous' => '上一页',
    ],
    'aria' => [
        'sortAscending' => ': 增序排列',
        'sortDescending' => ': 降序排列',
    ],
    'export' => [
        'export' => '导出',
        'copy' => '复制',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => '打印',
        'column_visibility' => '列可见性',
    ],

    // global crud - errors
    'unauthorized_access' => '您沒有权限浏览此页面。',
    'please_fix' => '请修正以下错误：',

    // global crud - success / error notification bubbles
    'insert_success' => '插入成功。',
    'update_success' => '更新成功。',

    // CRUD reorder view
    'reorder' => '重新排序',
    'reorder_text' => '请以拖放 (drag and drop) 的放式重新排序。',
    'reorder_success_title' => '完成',
    'reorder_success_message' => '你的排序已被儲存。',
    'reorder_error_title' => '错误',
    'reorder_error_message' => '你的排序尚未被儲存。',

    // CRUD yes/no
    'yes' => 'Yes',
    'no' => 'No',

    // CRUD filters navbar view
    'filters' => '筛选条件',
    'toggle_filters' => '切换筛选条件',
    'remove_filters' => '移除筛选条件',
    'apply' => '套用',

    //filters language strings
    'today' => '今天',
    'yesterday' => '昨天',
    'last_7_days' => '最近7天',
    'last_30_days' => '最近30天',
    'this_month' => '这个月',
    'last_month' => '上个月',
    'custom_range' => '自订范围',
    'weekLabel' => '周',

    // Fields
    'browse_uploads' => '查看已上传的文档',
    'select_all' => '选择全部',
    'select_files' => '选择文件（多个）',
    'select_file' => '选择文件（单个）',
    'clear' => '清除',
    'page_link' => '页面链接',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => '内部链接',
    'internal_link_placeholder' => '内部链接，例如: \'admin/page\' (no quotes) for \':url\'',
    'external_link' => '外部链接',
    'choose_file' => '选择文件',
    'new_item' => '新项目',
    'select_entry' => '选择一个项目',
    'select_entries' => '选择多个项目',

    //Table field
    'table_cant_add' => '不能再增加 :entity',
    'table_max_reached' => '已达到 :max 条记录的上限',

    // File manager
    'file_manager' => '文件管理器',

    // InlineCreateOperation
    'related_entry_created_success' => '相关的纪录已被新增并选择。',
    'related_entry_created_error' => '无法新增相关的纪录。',
];
