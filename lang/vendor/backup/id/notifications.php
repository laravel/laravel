<?php

return [
    'exception_message' => 'Pesan pengecualian: :message',
    'exception_trace' => 'Jejak pengecualian: :trace',
    'exception_message_title' => 'Pesan pengecualian',
    'exception_trace_title' => 'Jejak pengecualian',

    'backup_failed_subject' => 'Gagal backup :application_name',
    'backup_failed_body' => 'Penting: Sebuah error terjadi ketika membackup :application_name',

    'backup_successful_subject' => 'Backup baru sukses dari :application_name',
    'backup_successful_subject_title' => 'Backup baru sukses!',
    'backup_successful_body' => 'Kabar baik, sebuah backup baru dari :application_name sukses dibuat pada disk bernama :disk_name.',

    'cleanup_failed_subject' => 'Membersihkan backup dari :application_name yang gagal.',
    'cleanup_failed_body' => 'Sebuah error teradi ketika membersihkan backup dari :application_name',

    'cleanup_successful_subject' => 'Sukses membersihkan backup :application_name',
    'cleanup_successful_subject_title' => 'Sukses membersihkan backup!',
    'cleanup_successful_body' => 'Pembersihan backup :application_name pada disk bernama :disk_name telah sukses.',

    'healthy_backup_found_subject' => 'Backup untuk :application_name pada disk :disk_name sehat',
    'healthy_backup_found_subject_title' => 'Backup untuk :application_name sehat',
    'healthy_backup_found_body' => 'Backup untuk :application_name dipertimbangkan sehat. Kerja bagus!',

    'unhealthy_backup_found_subject' => 'Penting: Backup untuk :application_name tidak sehat',
    'unhealthy_backup_found_subject_title' => 'Penting: Backup untuk :application_name tidak sehat. :problem',
    'unhealthy_backup_found_body' => 'Backup untuk :application_name pada disk :disk_name tidak sehat.',
    'unhealthy_backup_found_not_reachable' => 'Tujuan backup tidak dapat terjangkau. :error',
    'unhealthy_backup_found_empty' => 'Tidak ada backup pada aplikasi ini sama sekali.',
    'unhealthy_backup_found_old' => 'Backup terakhir dibuat pada :date dimana dipertimbahkan sudah sangat lama.',
    'unhealthy_backup_found_unknown' => 'Maaf, sebuah alasan persisnya tidak dapat ditentukan.',
    'unhealthy_backup_found_full' => 'Backup menggunakan terlalu banyak kapasitas penyimpanan. Penggunaan terkini adalah :disk_usage dimana lebih besar dari batas yang diperbolehkan yaitu :disk_limit.',

    'no_backups_info' => 'Belum ada backup yang dibuat',
    'application_name' => 'Nama aplikasi',
    'backup_name' => 'Nama cadangan',
    'disk' => 'Disk',
    'newest_backup_size' => 'Ukuran cadangan terbaru',
    'number_of_backups' => 'Jumlah cadangan',
    'total_storage_used' => 'Total penyimpanan yang digunakan',
    'newest_backup_date' => 'Ukuran cadangan terbaru',
    'oldest_backup_date' => 'Ukuran cadangan tertua',
];
