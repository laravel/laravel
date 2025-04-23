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
    'save_action_save_and_new' => 'บันทึกและสร้างใหม่',
    'save_action_save_and_edit' => 'บันทึกแและแก้ไขรายการนี้',
    'save_action_save_and_back' => 'บันทึกและออก',
    'save_action_save_and_preview' => 'บันทึกและเปิดดู',
    'save_action_changed_notification' => 'การกระทำหลังจากบันทึกมีการเปลี่ยนแปลง',

    // Create form
    'add' => 'เพิ่ม',
    'back_to_all' => 'กลับไปยังรายการทั้งหมด ',
    'cancel' => 'ยกเลิก',
    'add_a_new' => 'เพิ่ม  ',

    // Edit form
    'edit' => 'แก้ไข',
    'save' => 'บันทึก',

    // Translatable models
    'edit_translations' => 'การแปล',
    'language' => 'ภาษา',

    // CRUD table view
    'all' => 'รายการทั้งหมด ',
    'in_the_database' => 'ในฐานข้อมูล',
    'list' => 'รายการ',
    'reset' => 'รีเซ็ต',
    'actions' => 'การกระทำ',
    'preview' => 'เปิดดู',
    'delete' => 'ลบ',
    'admin' => 'ผู้ดูแลระบบ',
    'details_row' => 'นี่คือรายละเอียดของรายการนี้ คุณสามารถแก้ไขข้อมูลได้ที่นี่',
    'details_row_loading_error' => 'เกิดข้อผิดพลาดระหว่างโหลดรายละเอียด โปรดลองใหม่ ',
    'clone' => 'คัดลอก',
    'clone_success' => '<strong>คัดลอกรายการแล้ว</strong><br>รายการใหม่ถูกสร้างขึ้นด้วยข้อมูลจากรายการนี้',
    'clone_failure' => '<strong>คัดลอกไม่สำเร็จ</strong><br>ไม่สามารถสร้างรายการใหม่ได้ โปรดลองใหม่อีกครั้ง',

    // Confirmation messages and bubbles
    'delete_confirm' => 'คุณแน่ใจที่จะลบรายการนี้หรือไม่?',
    'delete_confirmation_title' => 'รายการถูกลบแล้ว',
    'delete_confirmation_message' => 'การลบรายการสำเร็จ',
    'delete_confirmation_not_title' => 'การลบไม่สำเร็จ',
    'delete_confirmation_not_message' => 'เกิดข้อผิดพลาดระหว่างการลบรายการ ข้อมูลบางส่วนอาจคงอยู่',
    'delete_confirmation_not_deleted_title' => 'รายการไม่ถูกลบ',
    'delete_confirmation_not_deleted_message' => 'ยกเลิกการลบแล้ว รายการนี้ไม่มีการเปลี่ยนแปลง',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'ยังไม่ได้เลือกรายการใดๆ',
    'bulk_no_entries_selected_message' => 'โปรดเลือกข้อมูลอย่างน้อยหนึ่งรายการเพื่อทำการกระทำจำนวนมาก',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'คุณแน่ใจที่จะลบข้อมูลจำนวน :number รายการหรือไม่?',
    'bulk_delete_sucess_title' => 'รายการถูกลบแล้ว',
    'bulk_delete_sucess_message' => ' รายการถูกลบแล้ว',
    'bulk_delete_error_title' => 'การลบไม่สำเร็จ',
    'bulk_delete_error_message' => 'มีข้อมูลบางรายการไม่สามารถลบได้',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'คุณแน่ใจที่จะคัดลอกข้อมูลจำนวน :number รายการหรือไม่?',
    'bulk_clone_sucess_title' => 'รายการถูกคัดลอกแล้ว',
    'bulk_clone_sucess_message' => ' รายการได้ถูกคัดลอก',
    'bulk_clone_error_title' => 'การคัดลอกไม่สำเร็จ',
    'bulk_clone_error_message' => 'มีข้อมูลบางรายการไม่สามารถคัดลอกได่ โปรดลองใหม่อีกครั้ง',

    // Ajax errors
    'ajax_error_title' => 'ข้อผิดพลาด',
    'ajax_error_text' => 'เกิดข้อผิดพลาดในการโหลดหน้านี้ โปรดรีเฟรชหน้าอีกครั้ง',

    // DataTables translation
    'emptyTable' => 'ไม่มีรายการในตาราง',
    'info' => 'กำลังแสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ',
    'infoEmpty' => 'ไม่มีข้อมูล',
    'infoFiltered' => '(กรองจากข้อมูล _MAX_ รายการ)',
    'infoPostFix' => '',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ รายการต่อหน้า',
    'loadingRecords' => 'กำลังโหลด...',
    'processing' => 'กำลังประมวลผล...',
    'search' => 'ค้นหา',
    'zeroRecords' => 'ไม่มีรายการที่ตรงกับเงื่อนไข',
    'paginate' => [
        'first' => 'หน้าแรก',
        'last' => 'หน้าสุดท้าย',
        'next' => 'หน้าถัดไป',
        'previous' => 'หน้าก่อนหน้า',
    ],
    'aria' => [
        'sortAscending' => ': เลือกเพื่อกรองจากน้อย-มาก',
        'sortDescending' => ': เลือกเพื่อกรองจากมาก-น้อย',
    ],
    'export' => [
        'export' => 'ส่งออก',
        'copy' => 'คัดลอก',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'พิมพ์',
        'column_visibility' => 'การแสดงคอลัมน์',
    ],

    // global crud - errors
    'unauthorized_access' => 'ไม่ได้รับอนุญาตให้เข้าถึง - คุณไม่ได้รับอนุญาตให้เข้าถึงหน้านี้',
    'please_fix' => 'โปรดแก้ไขข้อผิดพลาดเหล่านี้:',

    // global crud - success / error notification bubbles
    'insert_success' => 'การเพิ่มข้อมูลสำเร็จ',
    'update_success' => 'การแก้ไขข้อมูลสำเร็จ',

    // CRUD reorder view
    'reorder' => 'จัดเรียง',
    'reorder_text' => 'ลากและวางเพื่อจัดเรียงใหม่',
    'reorder_success_title' => 'สำเร็จ',
    'reorder_success_message' => 'การเรียงลำดับถูกบันทึกแล้ว',
    'reorder_error_title' => 'ข้อผิดพลาด',
    'reorder_error_message' => 'ไม่สามารถบันทึกการเรียงลำดับได้',

    // CRUD yes/no
    'yes' => 'ใช่',
    'no' => 'ไม่ใช่',

    // CRUD filters navbar view
    'filters' => 'ตัวกรอง',
    'toggle_filters' => 'เปิดปิดตัวกรอง',
    'remove_filters' => 'ล้างตัวกรอง',
    'apply' => 'นำไปใช้',

    //filters language strings
    'today' => 'วันนี้',
    'yesterday' => 'เมื่อวานนี้',
    'last_7_days' => '7 วันที่ผ่านมา',
    'last_30_days' => '30 วันที่ผ่านมา',
    'this_month' => 'เดือนนี้',
    'last_month' => 'เดือนก่อน',
    'custom_range' => 'กำหนดเอง',
    'weekLabel' => 'W',

    // Fields
    'browse_uploads' => 'เลือกไฟล์',
    'select_all' => 'เลือกทั้งหมด',
    'select_files' => 'เลือกไฟล์',
    'select_file' => 'เลือกไฟล์',
    'clear' => 'ล้าง',
    'page_link' => 'ลิงก์ไปหน้าอื่น',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'ลิงก์ภายใน',
    'internal_link_placeholder' => 'ลิงก์ภายใน เช่น \'admin/page\' (ไม่มีเครื่องหมาย ") สำหรับ \':url\'',
    'external_link' => 'ลิงก์ภายนอก',
    'choose_file' => 'เลือกไฟล์',
    'new_item' => 'สร้างใหม่',
    'select_entry' => 'เลือกรายการ',
    'select_entries' => 'เลือกหลายรายการ',
    'upload_multiple_files_selected' => 'ไฟล์ถูกเลือก หลังจากบันทึกไฟล์เหล่านี้จะแสดงด้านบน',

    //Table field
    'table_cant_add' => 'ไม่สามารถเพิ่ม:entityใหม่',
    'table_max_reached' => 'เกินจำนวนที่กำหนด (:max รายการ)',

    // File manager
    'file_manager' => 'ตัวจัดการไฟล์',

    // InlineCreateOperation
    'related_entry_created_success' => 'รายการที่เกี่ยวข้องถูกสร้างและเลือกแล้ว',
    'related_entry_created_error' => 'ไม่สามารถสร้างรายการที่เกี่ยวข้องได้',
    'inline_saving' => 'กำลังบันทึก...',

    // returned when no translations found in select inputs
    'empty_translations' => '(ว่าง)',

    // The pivot selector required validation message
    'pivot_selector_required_validation_message' => 'ต้องเลือก pivot field',
];
