<?php

return [
    'exception_message' => '例外のメッセージ: :message',
    'exception_trace' => '例外の追跡: :trace',
    'exception_message_title' => '例外のメッセージ',
    'exception_trace_title' => '例外の追跡',

    'backup_failed_subject' => ':application_name のバックアップに失敗しました。',
    'backup_failed_body' => '重要: :application_name のバックアップ中にエラーが発生しました。',

    'backup_successful_subject' => ':application_name のバックアップに成功しました。',
    'backup_successful_subject_title' => 'バックアップに成功しました！',
    'backup_successful_body' => '朗報です。ディスク :disk_name へ :application_name のバックアップが成功しました。',

    'cleanup_failed_subject' => ':application_name のバックアップ削除に失敗しました。',
    'cleanup_failed_body' => ':application_name のバックアップ削除中にエラーが発生しました。',

    'cleanup_successful_subject' => ':application_name のバックアップ削除に成功しました。',
    'cleanup_successful_subject_title' => 'バックアップ削除に成功しました！',
    'cleanup_successful_body' => 'ディスク :disk_name に保存された :application_name のバックアップ削除に成功しました。',

    'healthy_backup_found_subject' => 'ディスク :disk_name への :application_name のバックアップは正常です。',
    'healthy_backup_found_subject_title' => ':application_name のバックアップは正常です。',
    'healthy_backup_found_body' => ':application_name へのバックアップは正常です。いい仕事してますね！',

    'unhealthy_backup_found_subject' => '重要: :application_name のバックアップに異常があります。',
    'unhealthy_backup_found_subject_title' => '重要: :application_name のバックアップに異常があります。 :problem',
    'unhealthy_backup_found_body' => ':disk_name への :application_name のバックアップに異常があります。',
    'unhealthy_backup_found_not_reachable' => 'バックアップ先にアクセスできませんでした。 :error',
    'unhealthy_backup_found_empty' => 'このアプリケーションのバックアップは見つかりませんでした。',
    'unhealthy_backup_found_old' => ':date に保存された直近のバックアップが古すぎます。',
    'unhealthy_backup_found_unknown' => '申し訳ございません。予期せぬエラーです。',
    'unhealthy_backup_found_full' => 'バックアップがディスク容量を圧迫しています。現在の使用量 :disk_usage　は、許可された限界値 :disk_limit を超えています。',

    'no_backups_info' => 'バックアップはまだ作成されていません',
    'application_name' => 'アプリケーション名',
    'backup_name' => 'バックアップ名',
    'disk' => 'ディスク',
    'newest_backup_size' => '最新のバックアップサイズ',
    'number_of_backups' => 'バックアップ数',
    'total_storage_used' => '使用された合計ストレージ',
    'newest_backup_date' => '最新のバックアップ日時',
    'oldest_backup_date' => '最も古いバックアップ日時',
];
