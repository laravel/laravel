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
    'save_action_save_and_new' => 'সংরক্ষণ করুন এবং নতুন আইটেম',
    'save_action_save_and_edit' => 'এই আইটেমটি সংরক্ষণ এবং সম্পাদনা করুন',
    'save_action_save_and_back' => 'সংরক্ষণ করুন এবং ফিরে যান',
    'save_action_save_and_preview' => 'সংরক্ষণ এবং প্রদর্শন',
    'save_action_changed_notification' => 'সংরক্ষণের পরে ডিফল্ট আচরণ পরিবর্তন করা হয়েছে।',

    // Create form
    'add' => 'যোগ করুন',
    'back_to_all' => 'সবগুলিতে ফিরে যান ',
    'cancel' => 'বাতিল',
    'add_a_new' => 'নতুন যোগ করুন ',

    // Edit form
    'edit' => 'সম্পাদনা',
    'save' => 'সংরক্ষণ',

    // Translatable models
    'edit_translations' => 'অনুবাদ',
    'language' => 'ভাষা',

    // CRUD table view
    'all' => 'সবগুলি ',
    'in_the_database' => 'ডাটাবেসে',
    'list' => 'তালিকা',
    'reset' => 'রিসেট',
    'actions' => 'অ্যাকশনগুলি',
    'preview' => 'প্রদর্শন',
    'delete' => 'মুছে ফেলুন',
    'admin' => 'অ্যাডমিন',
    'details_row' => 'এটি বিশদ সারি। আপনি দয়া করে পরিবর্তন করুন।',
    'details_row_loading_error' => 'বিস্তারিত লোড করার সময় একটি ত্রুটি হয়েছিল। পুনরায় চেষ্টা করুন',
    'clone' => 'ক্লোন',
    'clone_success' => '<strong>এন্ট্রি ক্লোন করা হয়েছে।</strong><br>একই তথ্য সহ একটি নতুন এন্ট্রি যুক্ত করা হয়েছে।',
    'clone_failure' => '<strong>ক্লোনিং ব্যর্থ হয়েছে</strong><br>নতুন এন্ট্রি তৈরি করা যায়নি। আবার চেষ্টা করুন।',

    // Confirmation messages and bubbles
    'delete_confirm' => 'আপনি কি নিশ্চিতভাবে এই আইটেমটি মুছে ফেলতে চান?',
    'delete_confirmation_title' => 'আইটেম মুছে ফেলা হয়েছে।',
    'delete_confirmation_message' => 'আইটেমটি সফলভাবে মুছে ফেলা হয়েছে।',
    'delete_confirmation_not_title' => 'মুছে ফেলা হয়নি।',
    'delete_confirmation_not_message' => 'একটি ত্রুটি পাওয়া গিয়েছে আপনার আইটেম মুছে ফেলা নাও হতে পারে।',
    'delete_confirmation_not_deleted_title' => 'মুছে ফেলা হয়নি।',
    'delete_confirmation_not_deleted_message' => 'কিছুই ঘটেনি। আপনার আইটেমটি নিরাপদে আছে।',

    // Bulk actions
    'bulk_no_entries_selected_title' => 'কোনও এন্ট্রি নির্বাচিত হয়নি।',
    'bulk_no_entries_selected_message' => 'বাল্ক অ্যাকশনের জন্য এক বা একাধিক আইটেম নির্বাচন করুন।',

    // Bulk delete
    'bulk_delete_are_you_sure' => 'আপনি কি :number এন্ট্রিগুলি মুছে ফেলার বিষয়ে নিশ্চিত?',
    'bulk_delete_sucess_title' => 'এন্ট্রিগুলি মুছে ফেলা হয়েছে।',
    'bulk_delete_sucess_message' => ' আইটেমগুলি মুছে ফেলা হয়েছে।',
    'bulk_delete_error_title' => 'মুছে ফেলা ব্যর্থ হয়েছে',
    'bulk_delete_error_message' => 'এক বা একাধিক আইটেম মুছে ফেলা যায়নি',

    // Bulk clone
    'bulk_clone_are_you_sure' => 'আপনি কি :number এন্ট্রিগুলি ক্লোন করার বিষয়ে নিশ্চিত?',
    'bulk_clone_sucess_title' => 'এন্ট্রিগুলি ক্লোন করা হয়েছে',
    'bulk_clone_sucess_message' => ' আইটেমগুলি ক্লোন করা হয়েছে',
    'bulk_clone_error_title' => 'ক্লোনিং ব্যর্থ হয়েছে',
    'bulk_clone_error_message' => 'এক বা একাধিক এন্ট্রি তৈরি করা যায়নি। আবার চেষ্টা করুন।',

    // Ajax errors
    'ajax_error_title' => 'ত্রুটি',
    'ajax_error_text' => 'পৃষ্ঠাটি লোড করার সময় ত্রুটি পাওয়া গেছে। রিফ্রেশ করুন।',

    // DataTables translation
    'emptyTable' => 'সারণীতে কোনও ডেটা নেই',
    'info' => 'সর্বমোট _TOTAL_ এন্ট্রির মধ্যে _START_ থেকে _END_ টি দেখানো হচ্ছে।',
    'infoEmpty' => 'কোনও এন্ট্রি নেই',
    'infoFiltered' => '( _MAX_ টি এন্ট্রি থেকে ফিল্টার করা হয়েছে)',
    'infoPostFix' => '.',
    'thousands' => ',',
    'lengthMenu' => 'প্রতি পৃষ্ঠায় এন্ট্রি _MENU_ টি।',
    'loadingRecords' => 'লোড হচ্ছে...',
    'processing' => 'প্রসেসিং হচ্ছে...',
    'search' => 'খুঁজুন',
    'zeroRecords' => 'কোনও মিলে যাওয়া এন্ট্রি পাওয়া যায় নি।',
    'paginate' => [
        'first' => 'প্রথম',
        'last' => 'শেষ',
        'next' => 'পরবর্তী',
        'previous' => 'পূর্ববর্তী',
    ],
    'aria' => [
        'sortAscending' => ': কলাম উর্ধ্বক্রম অনুসারে সাজানোর জন্য অ্যাক্টিভ করুন',
        'sortDescending' => ': কলাম অধঃক্রম অনুসারে সাজানোর জন্য অ্যাক্টিভ করুন',
    ],
    'export' => [
        'export' => 'Export',
        'copy' => 'Copy',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Print',
        'column_visibility' => 'Column visibility',
    ],

    // global crud - errors
    'unauthorized_access' => 'অননুমোদিত। এই পৃষ্ঠায় আপনার কাছে প্রয়োজনীয় অনুমতি নেই।',
    'please_fix' => 'নিম্নলিখিত ত্রুটি ঠিক করুন:',

    // global crud - success / error notification bubbles
    'insert_success' => 'আইটেমটি সফলভাবে যুক্ত করা হয়েছে।',
    'update_success' => 'আইটেমটি সফলভাবে পরিবর্তিত হয়েছে।',

    // CRUD reorder view
    'reorder' => 'পুনঃক্রম',
    'reorder_text' => 'পুনরায় অর্ডার করতে ড্রাগ এবং ড্রপ ব্যবহার করুন।',
    'reorder_success_title' => 'সম্পন্ন',
    'reorder_success_message' => 'আপনার অর্ডার সংরক্ষণ করা হয়েছে।',
    'reorder_error_title' => 'ত্রুটি',
    'reorder_error_message' => 'আপনার অর্ডার সংরক্ষণ করা হয়নি।',

    // CRUD yes/no
    'yes' => 'হ্যাঁ',
    'no' => 'না',

    // CRUD filters navbar view
    'filters' => 'ফিল্টার',
    'toggle_filters' => 'ফিল্টারগুলি টগল করুন',
    'remove_filters' => 'ফিল্টারগুলি সরান',
    'apply' => 'প্রয়োগ করুন',

    //filters language strings
    'today' => 'আজ',
    'yesterday' => 'গতকাল',
    'last_7_days' => 'শেষ ৭ দিন',
    'last_30_days' => 'শেষ ৩০ দিন',
    'this_month' => 'এই মাস',
    'last_month' => 'গত মাস',
    'custom_range' => 'কাস্টম রেঞ্জ',
    'weekLabel' => 'W',

    // Fields
    'browse_uploads' => 'আপলোডগুলি ব্রাউজ করুন',
    'select_all' => 'সবগুলি নির্বাচন করুন',
    'select_files' => 'ফাইলগুলি নির্বাচন করুন',
    'select_file' => 'ফাইল নির্বাচন করুন',
    'clear' => 'ক্লিয়ার',
    'page_link' => 'পৃষ্ঠা লিংক',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'অভ্যন্তরীণ লিংক',
    'internal_link_placeholder' => 'Internal slug. Ex: \'admin/page\' (no quotes) for \':url\'',
    'external_link' => 'বহি:স্থ লিংক',
    'choose_file' => 'ফাইল বাছাই করুন',
    'new_item' => 'নতুন আইটেম',
    'select_entry' => 'একটি এন্ট্রি নির্বাচন করুন',
    'select_entries' => 'এন্ট্রি নির্বাচন করুন',

    //Table field
    'table_cant_add' => 'নতুন :entity যোগ করা যাচ্ছে না',
    'table_max_reached' => 'সর্বাধিক সংখ্যক :max পৌঁছেছে',

    // File manager
    'file_manager' => 'ফাইল ম্যানেজার',

    // InlineCreateOperation
    'related_entry_created_success' => 'সম্পর্কিত এন্ট্রি তৈরি এবং নির্বাচন করা হয়েছে।',
    'related_entry_created_error' => 'সম্পর্কিত এন্ট্রি তৈরি করতে পারেনি।',

    // returned when no translations found in select inputs
    'empty_translations' => '(empty)',
];
