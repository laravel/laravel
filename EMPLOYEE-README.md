# Employee Management System - Documentation

## Overview

Sistem Employee Management adalah bagian dari aplikasi Slip Gaji yang memungkinkan pengelolaan data karyawan secara lengkap. Sistem ini dibangun menggunakan Laravel dan menyediakan fitur CRUD (Create, Read, Update, Delete) untuk manajemen karyawan.

## Fitur-Fitur

### 1. **Manajemen Karyawan**
- ✅ Tambah karyawan baru
- ✅ Lihat daftar karyawan
- ✅ Edit data karyawan
- ✅ Hapus karyawan
- ✅ Pencarian dan filter karyawan
- ✅ Export data ke CSV

### 2. **Form Input Karyawan**
- **Name**: Nama lengkap karyawan (hanya huruf dan spasi)
- **Email**: Alamat email unik
- **Position**: Jabatan/posisi
- **Department**: Departemen (IT, HR, Finance, Marketing, Operations)
- **Salary**: Gaji bulanan (dalam Rupiah)
- **Hire Date**: Tanggal mulai kerja

### 3. **Validasi Form**
- ✅ Validasi email format dan uniqueness
- ✅ Validasi nama (hanya huruf dan spasi)
- ✅ Validasi gaji (harus positif)
- ✅ Validasi tanggal masuk (tidak boleh masa depan)
- ✅ Auto-format nama (capitalize)
- ✅ Auto-format email (lowercase)

### 4. **Pencarian & Filter**
- 🔍 Pencarian berdasarkan nama
- 🏢 Filter berdasarkan departemen
- 💰 Filter berdasarkan range gaji
- 📅 Filter berdasarkan tanggal masuk
- 💼 Pencarian berdasarkan posisi

### 5. **Export Data**
- 📊 Export ke format CSV
- 📈 Statistik karyawan
- 📋 Data lengkap dengan tahun masa kerja

## Struktur File

### Controllers
```
app/Http/Controllers/EmployeeController.php
```
**Methods:**
- `index()` - Menampilkan daftar karyawan
- `store()` - Menyimpan karyawan baru
- `edit($id)` - Menampilkan form edit
- `update($id)` - Update data karyawan
- `destroy($id)` - Hapus karyawan
- `search()` - Pencarian karyawan
- `export()` - Export data CSV
- `getStatistics()` - Statistik karyawan

### Models
```
app/Employee.php
```
**Attributes:**
- `name`, `email`, `position`, `department`, `salary`, `hire_date`
- `phone`, `address`, `status` (optional)

**Methods:**
- `getFormattedSalaryAttribute()` - Format gaji
- `getYearsOfServiceAttribute()` - Hitung masa kerja
- `scopeByDepartment()` - Filter departemen
- `getDepartments()` - Daftar departemen
- `getStatistics()` - Statistik data

### Views
```
resources/views/employe.blade.php           # Halaman utama
resources/views/employee/edit.blade.php     # Form edit
resources/views/layouts/app.blade.php       # Layout utama
```

### Migrations
```
database/migrations/2024_01_01_000000_create_employees_table.php
```

### Seeders
```
database/seeds/EmployeeSeeder.php
database/seeds/DatabaseSeeder.php
```

## Setup & Installation

### 1. **Database Migration**
```bash
php artisan migrate
```

### 2. **Seeding Data**
```bash
php artisan db:seed --class=EmployeeSeeder
```

### 3. **Routes**
Tambahkan di `routes/web.php`:
```php
// Employee Management Routes
Route::get('/employe', 'EmployeeController@index')->name('employee.index');
Route::post('/employee', 'EmployeeController@store')->name('employee.store');
Route::get('/employee/{id}/edit', 'EmployeeController@edit')->name('employee.edit');
Route::put('/employee/{id}', 'EmployeeController@update')->name('employee.update');
Route::delete('/employee/{id}', 'EmployeeController@destroy')->name('employee.destroy');
Route::get('/employee/search', 'EmployeeController@search')->name('employee.search');
Route::get('/employee/export', 'EmployeeController@export')->name('employee.export');
Route::get('/employee/statistics', 'EmployeeController@getStatistics')->name('employee.statistics');
```

## Penggunaan

### 1. **Akses Halaman Employee**
Buka: `http://your-domain/employe`

### 2. **Menambah Karyawan Baru**
1. Isi form di bagian atas halaman
2. Klik tombol "Add Employee"
3. Data akan tersimpan dan muncul di daftar

### 3. **Edit Karyawan**
1. Klik tombol "Edit" pada karyawan yang ingin diubah
2. Ubah data yang diperlukan
3. Klik "Update Employee"

### 4. **Hapus Karyawan**
1. Klik tombol "Delete" pada karyawan
2. Konfirmasi penghapusan
3. Data akan terhapus dari sistem

### 5. **Pencarian & Filter**
1. Gunakan form pencarian di atas tabel
2. Masukkan kriteria pencarian
3. Klik "Search" atau "Advanced" untuk filter lanjutan

### 6. **Export Data**
1. Klik tombol "Export CSV" di header tabel
2. File CSV akan terdownload otomatis

## Keamanan

### 1. **Validasi Input**
- CSRF Protection dengan `@csrf`
- Server-side validation untuk semua input
- Client-side validation untuk UX yang lebih baik

### 2. **Authentication**
- Menggunakan middleware `auth.custom`
- Akses hanya untuk user yang login

### 3. **Database Security**
- Mass assignment protection dengan `$fillable`
- SQL injection protection dengan Eloquent ORM

## Teknologi yang Digunakan

### Backend
- **Laravel 5.x** - PHP Framework
- **MySQL** - Database
- **Eloquent ORM** - Database abstraction

### Frontend
- **Bootstrap 3.4.1** - CSS Framework
- **jQuery 3.5.1** - JavaScript Library
- **Font Awesome 4.7.0** - Icons
- **Blade Templates** - Template Engine

### Features
- **Responsive Design** - Mobile-friendly
- **AJAX Support** - Dynamic interactions
- **CSV Export** - Data portability
- **Real-time Validation** - Immediate feedback

## Sample Data

Sistem dilengkapi dengan 10 sample data karyawan:
- John Doe (IT - Software Developer)
- Jane Smith (HR - HR Manager)
- Mike Johnson (Finance - Financial Analyst)
- Sarah Wilson (Marketing - Marketing Specialist)
- David Brown (Operations - Operations Manager)
- Emily Davis (IT - System Administrator)
- Robert Taylor (Finance - Accountant)
- Lisa Anderson (Marketing - Digital Marketing Specialist)
- Kevin Martinez (Operations - Quality Control)
- Amanda Garcia (HR - HR Specialist)

## Troubleshooting

### 1. **Error: Route not found**
- Pastikan routes sudah ditambahkan di `web.php`
- Clear route cache: `php artisan route:clear`

### 2. **Error: Class not found**
- Pastikan autoload: `composer dump-autoload`
- Clear config cache: `php artisan config:clear`

### 3. **Database Error**
- Pastikan migration sudah dijalankan
- Check database connection di `.env`

### 4. **Permission Error**
- Set permission folder storage: `chmod -R 755 storage/`
- Set permission folder bootstrap/cache: `chmod -R 755 bootstrap/cache/`

## Future Enhancements

### Planned Features
- [ ] Photo upload untuk karyawan
- [ ] Bulk import dari Excel/CSV
- [ ] Advanced reporting & analytics
- [ ] Employee hierarchy management
- [ ] Performance evaluation system
- [ ] Attendance tracking integration
- [ ] Payroll calculation integration
- [ ] Email notifications
- [ ] API endpoints untuk mobile app
- [ ] Advanced search dengan multiple criteria

### Database Enhancements
- [ ] Employee categories/grades
- [ ] Skills & certifications tracking
- [ ] Emergency contact information
- [ ] Document attachments
- [ ] Audit trail untuk changes

---

**Developed by:** Slipgaji Development Team  
**Version:** 1.0.0  
**Last Updated:** September 2025
