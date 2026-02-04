# Testing Guide - LMS Laravel

## Prerequisites
```bash
php artisan migrate
php artisan serve
```
Server runs at: `http://127.0.0.1:8000`

---

## Test 1: Registration & Authentication

### Step 1.1 - Register as Siswa (Student)
1. Go to `http://127.0.0.1:8000/register`
2. Fill form:
   - Name: `Andi Siswa`
   - Email: `siswa@test.com`
   - Password: `password123`
   - Role: **Siswa** ← important!
3. Click "Register"
4. **Expected:** Auto-login → Redirect to `/siswa/dashboard`
5. **Check:** URL should be `/siswa/dashboard`, not 403/407 error

### Step 1.2 - Register as Guru (Teacher)
1. Logout (click profile → Logout)
2. Go to `http://127.0.0.1:8000/register`
3. Fill form:
   - Name: `Budi Guru`
   - Email: `guru@test.com`
   - Password: `password123`
   - Role: **Guru** ← important!
4. Click "Register"
5. **Expected:** Auto-login → Redirect to `/guru/dashboard`
6. **Check:** URL should be `/guru/dashboard`, not 403/407 error

### Step 1.3 - Login as Siswa
1. Logout
2. Go to `http://127.0.0.1:8000/login`
3. Fill:
   - Email: `siswa@test.com`
   - Password: `password123`
4. Click "Login"
5. **Expected:** Redirect to `/siswa/dashboard`
6. **Check:** If you see 403/407, check browser console (F12) for errors

---

## Test 2: Material Upload (Guru Only)

### Step 2.1 - Guru Upload Material
1. Login as Guru (email: `guru@test.com`)
2. You should be on `/guru/dashboard`
3. Click "Materi Saya" in navbar or click action button
4. Should go to `/guru/materials`
5. Click **+ Tambah** button
6. Fill form:
   - Judul: `Materi Kalkulus Dasar`
   - Deskripsi: `Pengenalan materi kalkulus untuk siswa kelas X`
   - File: Upload any PDF/DOC file
7. Click "Simpan"
8. **Expected:** Redirected to materials list, new material appears

---

## Test 3: Material Download (Siswa Only)

### Step 3.1 - Siswa Download Material
1. Login as Siswa (email: `siswa@test.com`)
2. You should be on `/siswa/dashboard`
3. You should see material cards from guru uploads
4. Click **📥 Download** button
5. **Expected:** File downloads to your computer
6. **If broken:** Check if Storage symlink exists: `php artisan storage:link`

---

## Test 4: Role-Based Access Control

### Step 4.1 - Siswa Access Guru Routes (Should Fail 403)
1. Login as Siswa
2. Try to access `http://127.0.0.1:8000/guru/dashboard`
3. **Expected:** 403 Forbidden error (not 407!)

### Step 4.2 - Guru Access Siswa Routes (Should Fail 403)
1. Login as Guru
2. Try to access `http://127.0.0.1:8000/siswa/dashboard`
3. **Expected:** 403 Forbidden error (not 407!)

---

## Troubleshooting 403/407 Errors

### Error 403 = Correct! Role-based access denied (expected for unauthorized roles)
### Error 407 = Wrong! This is authentication proxy error (unusual in Laravel)

**If you see 407:**
1. Clear browser cookies: F12 → Application → Cookies → Delete all
2. Clear Laravel session cache: `php artisan cache:clear`
3. Restart PHP server: `php artisan serve`
4. Try login again

**If still broken:**
1. Check database users table: `php artisan tinker`
   ```php
   DB::table('users')->get();
   // Verify user role is correctly saved (admin/guru/siswa)
   ```
2. Check middleware: `cat app/Http/Middleware/RoleMiddleware.php`
3. Check routes: `php artisan route:list | grep -i dashboard`

---

## API Testing (Optional)

Use curl or Postman to test:

```bash
# Test register
curl -X POST http://127.0.0.1:8000/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@test.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "siswa"
  }'

# Test login
curl -X POST http://127.0.0.1:8000/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "siswa@test.com",
    "password": "password123"
  }'
```

---

## Summary of Expected Behavior

| Action | Role | Expected Result | URL |
|--------|------|-----------------|-----|
| Register | Siswa | Auto-login, redirect | `/siswa/dashboard` |
| Register | Guru | Auto-login, redirect | `/guru/dashboard` |
| Login | Siswa | Redirect | `/siswa/dashboard` |
| Login | Guru | Redirect | `/guru/dashboard` |
| Access `/guru/materials` as Siswa | Siswa | 403 Forbidden | 403 |
| Access `/siswa/dashboard` as Guru | Guru | 403 Forbidden | 403 |
| Download material | Siswa | File downloads | ✅ |
| View materials | Siswa | See all materials grid | ✅ |
| Upload material | Guru | Material saved | ✅ |

