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
    'save_action_save_and_new' => 'Lưu và Thêm mới',
    'save_action_save_and_edit' => 'Lưu và Tiếp tục sửa',
    'save_action_save_and_back' => 'Lưu và Quay lại',
    'save_action_save_and_preview' => 'Lưu và Xem lại',
    'save_action_changed_notification' => 'Hành động sau khi lưu dữ liệu đã thay đổi.',

    // Create form
    'add' => 'Thêm',
    'back_to_all' => 'Quay lại danh sách ',
    'cancel' => 'Huỷ bỏ',
    'add_a_new' => 'Thêm mới ',

    // Edit form
    'edit' => 'Sửa',
    'save' => 'Lưu',

    // Translatable models
    'edit_translations' => 'Bản dịch',
    'language' => 'Ngôn ngữ',

    // CRUD table view
    'all' => 'Tất cả ',
    'in_the_database' => 'trong cơ sở dữ liệu',
    'list' => 'Danh sách',
    'reset' => 'Thiết lập lại',
    'actions' => 'Hành động',
    'preview' => 'Xem lại',
    'delete' => 'Xoá',
    'admin' => 'Quản trị',
    'details_row' => 'Đây là các chi tiết của bản ghi. Vui lòng chỉnh sửa lại theo nhu cầu của bạn.',
    'details_row_loading_error' => 'Đã xảy ra lỗi trong quá trình tải chi tiết bản ghi. Vui lòng thử lại.',
    'clone' => 'Nhân bản',
    'clone_success' => '<strong>Đã nhân bản</strong><br>Bản ghi mới với cùng nội dung như bản ghi này đã được thêm.',
    'clone_failure' => '<strong>Nhân bản thất bại</strong><br>Không thể nhân bản. Vui lòng thử lại.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Bạn chắc chắn muốn xoá bản ghi này chứ?',
    'delete_confirmation_title' => 'Đã Xoá',
    'delete_confirmation_message' => 'Bản ghi đã được xoá thành công.',
    'delete_confirmation_not_title' => 'KHÔNG xoá',
    'delete_confirmation_not_message' => 'Đã xảy ra lỗi. Có thể bản ghi của bạn vẫn chưa được xoá.',
    'delete_confirmation_not_deleted_title' => 'Không Xoá',
    'delete_confirmation_not_deleted_message' => 'Bản ghi của bạn sẽ không bị xoá.',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'Chưa chọn bản ghi',
    'bulk_no_entries_selected_message' => 'Vui lòng chọn một hoặc nhiều bản ghi để thực hiện thao tác trên tập hợp.',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'Bạn có chắc chắn muốn xoá :number bản ghi này?',
    'bulk_delete_sucess_title' => 'Đã Xoá',
    'bulk_delete_sucess_message' => ' bản ghi đã được xoá',
    'bulk_delete_error_title' => 'Xoá ất Bại',
    'bulk_delete_error_message' => 'Một hoặc nhiều bản ghi có thể vẫn chưa được xoá',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'Bạn có chắc chắn muốn nhân bản :number bản ghi này không?',
    'bulk_clone_sucess_title' => 'Nhân Bản Thành Công',
    'bulk_clone_sucess_message' => ' bạn ghi đã được nhân bản.',
    'bulk_clone_error_title' => 'Nhân Bản Thất Bại',
    'bulk_clone_error_message' => 'Một hoặc nhiều bản ghi đã không thể nhân bản. Vui lòng thử lại.',

    // Ajax errors
    'ajax_error_title' => 'Lỗi',
    'ajax_error_text' => 'Xảy ra lỗi trong khi tải trang. Vui lòng refresh lại trang.',

    // DataTables translation
    'emptyTable' => 'Bảng chưa có dữ liệu',
    'info' => 'Hiển thị từ _START_ đến _END_ trong tổng số _TOTAL_ bản ghi',
    'infoEmpty' => 'Không có bản ghi nào',
    'infoFiltered' => '(được lọc từ _MAX_ bản ghi)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ bản ghi trên một trang',
    'loadingRecords' => 'Đang tải...',
    'processing' => 'Đang xử lý...',
    'search' => 'Tìm kiếm',
    'zeroRecords' => 'Không tìm thấy bản ghi phù hợp',
    'paginate' => [
        'first' => 'Đầu tiên',
        'last' => 'Cuối cùng',
        'next' => 'Tiếp',
        'previous' => 'Trước',
    ],
    'aria' => [
        'sortAscending' => ': kích hoạt để sắp xếp cột theo thứ tự tăng dần',
        'sortDescending' => ': kích hoạt để sắp xếp cột theo thứ tự giảm dần',
    ],
    'export' => [
        'export' => 'Xuất tập tin',
        'copy' => 'Sao chép',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'In',
        'column_visibility' => 'Ẩn/hiện cột',
    ],
    'custom_views' => [
        'title' => 'Chế độ xem tuỳ chỉnh',
        'title_short' => 'lượt xem',
        'default' => 'mặc định',
    ],

    // global crud - errors
    'unauthorized_access' => 'Truy cập chưa được cấp phép - bạn cần được cấp quyền để xem trang này.',
    'please_fix' => 'Vui lòng sửa các lỗi sau:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Bản ghi đã được thêm mới thành công.',
    'update_success' => 'Bản ghi đã được cập nhật thành công.',

    // CRUD reorder view
    'reorder' => 'Sắp xếp',
    'reorder_text' => 'Kéo & Thả để sắp xếp.',
    'reorder_success_title' => 'Hoàn tất',
    'reorder_success_message' => 'Thứ tự đã được lưu lại.',
    'reorder_error_title' => 'Lỗi',
    'reorder_error_message' => 'Không thể lưu lại thứ tự.',

    // CRUD yes/no
    'yes' => 'Có',
    'no' => 'Không',

    // CRUD filters navbar view
    'filters' => 'Bộ lọc',
    'toggle_filters' => 'Bật tắt bộ lọc',
    'remove_filters' => 'Gỡ bỏ bộ lọc',
    'apply' => 'Áp dụng',

    //filters language strings
    'today' => 'Hôm nay',
    'yesterday' => 'Hôm qua',
    'last_7_days' => '7 ngày qua',
    'last_30_days' => '30 ngày qua',
    'this_month' => 'Tháng này',
    'last_month' => 'Tháng trước',
    'custom_range' => 'Phạm vi tuỳ chỉnh',
    'weekLabel' => 'W',

    // Fields
    'browse_uploads' => 'Chọn tập tin',
    'select_all' => 'Chọn tất cả',
    'unselect_all' => 'Bỏ chọn tất cả',
    'select_files' => 'Chọn các tập tin',
    'select_file' => 'Chọn tập tin',
    'clear' => 'Xoá',
    'page_link' => 'Liên kết',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Liên kết nội trang',
    'internal_link_placeholder' => 'Liên kết nội trang. Ví dụ: \'admin/page\' (không có dấu nháy) cho \':url\'',
    'external_link' => 'Liên kết bên ngoài',
    'choose_file' => 'Chọn tập tin',
    'new_item' => 'Bản ghi mới',
    'select_entry' => 'Chọn một bản ghi',
    'select_entries' => 'Chọn các bản ghi',
    'upload_multiple_files_selected' => 'Đã chọn tệp. Sau khi lưu, chúng sẽ hiển thị ở phía trên.',

    //Table field
    'table_cant_add' => 'Không thể thêm mới :entity',
    'table_max_reached' => 'Đã đạt đến số lượng tối đa :max bản ghi',

    // google_map
    'google_map_locate' => 'Lấy vị trí của tôi',

    // File manager
    'file_manager' => 'Quản lý Tập tin',

    // InlineCreateOperation
    'related_entry_created_success' => 'Bản ghi liên quan đã được tạo và được chọn.',
    'related_entry_created_error' => 'Không thể tạo bản ghi liên qua.',
    'inline_saving' => 'Đang lưu...',

    // returned when no translations found in select inputs
    'empty_translations' => '(chưa có)',

    // The pivot selector required validation message
    'pivot_selector_required_validation_message' => 'Trường pivot là bắt buộc.',
];
