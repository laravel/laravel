<?php

return [
    'exception_message' => 'এক্সসেপশন বার্তা: :message',
    'exception_trace' => 'এক্সসেপশন ট্রেস: :trace',
    'exception_message_title' => 'এক্সসেপশন message',
    'exception_trace_title' => 'এক্সসেপশন ট্রেস',

    'backup_failed_subject' => ':application_name এর ব্যাকআপ ব্যর্থ হয়েছে।',
    'backup_failed_body' => 'গুরুত্বপূর্ণঃ :application_name ব্যাক আপ করার সময় একটি ত্রুটি ঘটেছে।',

    'backup_successful_subject' => ':application_name এর নতুন ব্যাকআপ সফল হয়েছে।',
    'backup_successful_subject_title' => 'নতুন ব্যাকআপ সফল হয়েছে!',
    'backup_successful_body' => 'খুশির খবর, :application_name এর নতুন ব্যাকআপ :disk_name ডিস্কে সফলভাবে তৈরি হয়েছে।',

    'cleanup_failed_subject' => ':application_name ব্যাকআপগুলি সাফ করতে ব্যর্থ হয়েছে।',
    'cleanup_failed_body' => ':application_name ব্যাকআপগুলি সাফ করার সময় একটি ত্রুটি ঘটেছে।',

    'cleanup_successful_subject' => ':application_name এর ব্যাকআপগুলি সফলভাবে সাফ করা হয়েছে।',
    'cleanup_successful_subject_title' => 'ব্যাকআপগুলি সফলভাবে সাফ করা হয়েছে!',
    'cleanup_successful_body' => ':application_name এর ব্যাকআপগুলি :disk_name ডিস্ক থেকে সফলভাবে সাফ করা হয়েছে।',

    'healthy_backup_found_subject' => ':application_name এর ব্যাকআপগুলি :disk_name ডিস্কে স্বাস্থ্যকর অবস্থায় আছে।',
    'healthy_backup_found_subject_title' => ':application_name এর ব্যাকআপগুলি স্বাস্থ্যকর অবস্থায় আছে।',
    'healthy_backup_found_body' => ':application_name এর ব্যাকআপগুলি  স্বাস্থ্যকর বিবেচনা করা হচ্ছে। Good job!',

    'unhealthy_backup_found_subject' => 'গুরুত্বপূর্ণঃ :application_name এর ব্যাকআপগুলি অস্বাস্থ্যকর অবস্থায় আছে।',
    'unhealthy_backup_found_subject_title' => 'গুরুত্বপূর্ণঃ :application_name এর ব্যাকআপগুলি অস্বাস্থ্যকর অবস্থায় আছে। :problem',
    'unhealthy_backup_found_body' => ':disk_name ডিস্কের :application_name এর ব্যাকআপগুলি অস্বাস্থ্যকর অবস্থায় আছে।',
    'unhealthy_backup_found_not_reachable' => 'ব্যাকআপ গন্তব্যে পৌঁছানো যায় নি। :error',
    'unhealthy_backup_found_empty' => 'এই অ্যাপ্লিকেশনটির কোনও ব্যাকআপ নেই।',
    'unhealthy_backup_found_old' => 'সর্বশেষ ব্যাকআপ যেটি :date এই তারিখে করা হয়েছে, সেটি খুব পুরানো।',
    'unhealthy_backup_found_unknown' => 'দুঃখিত, সঠিক কারণ নির্ধারণ করা সম্ভব হয়নি।',
    'unhealthy_backup_found_full' => 'ব্যাকআপগুলি অতিরিক্ত স্টোরেজ ব্যবহার করছে। বর্তমান ব্যবহারের পরিমান :disk_usage যা অনুমোদিত সীমা :disk_limit এর বেশি।',

    'no_backups_info' => 'কোনো ব্যাকআপ এখনও তৈরি হয়নি',
    'application_name' => 'আবেদনের নাম',
    'backup_name' => 'ব্যাকআপের নাম',
    'disk' => 'ডিস্ক',
    'newest_backup_size' => 'নতুন ব্যাকআপ আকার',
    'number_of_backups' => 'ব্যাকআপের সংখ্যা',
    'total_storage_used' => 'ব্যবহৃত মোট সঞ্চয়স্থান',
    'newest_backup_date' => 'নতুন ব্যাকআপের তারিখ',
    'oldest_backup_date' => 'পুরানো ব্যাকআপের তারিখ',
];
