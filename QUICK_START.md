# LMS Laravel - Quick Reference Card

## 🚀 Getting Started (After Git Clone)

```bash
cd lms-laravel

# 1. Install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Setup database
php artisan migrate

# 4. Create storage symlink (CRITICAL!)
php artisan storage:link

# 5. Start dev server
php artisan serve
```

**Access:** `http://127.0.0.1:8000`

---

## 📋 User Roles & Access

| Role | Routes | Can Do |
|------|--------|--------|
| **Siswa** (Student) | `/siswa/dashboard` | View & download materials |
| **Guru** (Teacher) | `/guru/dashboard` + `/guru/materials/*` | Upload, edit, delete materials |
| **Admin** | `/admin/dashboard` | View system stats & reports (future) |

### Registration
- Siswa & Guru can self-register (role dropdown)
- Admin must be created manually via `php artisan tinker`

### Login
- Any role can login
- **Auto-redirect** to role-specific dashboard

---

## 🧪 Quick Test Script

```bash
# 1. Register as Siswa
# Go to http://127.0.0.1:8000/register
# Name: Andi, Email: andi@test.com, Password: pass123, Role: Siswa
# ✅ Should redirect to /siswa/dashboard

# 2. Logout

# 3. Register as Guru
# Name: Budi, Email: budi@test.com, Password: pass123, Role: Guru
# ✅ Should redirect to /guru/dashboard

# 4. Upload Material (as Guru)
# Click "Materi Saya" → "+ Tambah"
# Fill form & upload PDF/DOC
# ✅ Should save & show in list

# 5. Download Material (as Siswa)
# Logout, login as Siswa
# Should see material card
# Click "📥 Download"
# ✅ File should download
```

---

## 🔑 Common Commands

```bash
# Check routes
php artisan route:list

# Tinker shell (test code)
php artisan tinker

# Clear cache (if something seems broken)
php artisan cache:clear

# Check database
php artisan db:seed

# View setup diagnostics
php artisan lms:check
```

---

## 🛠️ Directory Structure

```
app/
├── Http/Controllers/
│   ├── Auth/ (Login/Register)
│   ├── Admin/DashboardController.php
│   ├── Guru/
│   │   ├── DashboardController.php
│   │   └── MaterialController.php (CRUD)
│   ├── Siswa/DashboardController.php (Download)
│   └── Middleware/
│       └── RoleMiddleware.php
├── Models/
│   ├── User.php (with isAdmin, isTeacher, isStudent helpers)
│   ├── Material.php
│   └── Report.php
└── Policies/
    └── MaterialPolicy.php (ownership check)

routes/
├── web.php (All routes)
└── auth.php (Auth scaffolding)

resources/views/
├── landing.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php (with role dropdown)
├── admin/dashboard.blade.php
├── guru/
│   ├── dashboard.blade.php
│   └── materials/
│       ├── index.blade.php (list)
│       ├── create.blade.php (upload form)
│       └── edit.blade.php (edit form)
└── siswa/dashboard.blade.php (materials grid + download)

storage/app/public/materials/ (uploaded files go here)
public/storage/ (symlink to above)
```

---

## ❌ Troubleshooting

### "403 Forbidden"
**This is expected!** It means role-based access control is working.
- Siswa can only access `/siswa/*`
- Guru can only access `/guru/*` and `/guru/materials/*`
- Admin can only access `/admin/*`

### "407 Proxy Authentication"
Clear cache & cookies:
```bash
php artisan cache:clear
# Then F12 → Application → Cookies → Delete all
# Then refresh page
```

### "Download doesn't work"
```bash
# Ensure symlink exists
php artisan storage:link

# Check if materials folder exists
ls storage/app/public/materials

# Verify public/storage points to it
ls -la public/storage
```

### "Material list is empty"
Guru must upload first! Login as guru and use "Materi Saya" → "+ Tambah"

---

## 📝 Database Schema

### users
```
id, name, email, password, role (admin|guru|siswa), created_at, updated_at
```

### materials
```
id, user_id (→ User), title, description, file_path, file_name, created_at, updated_at
```

### reports
```
id, user_id (→ User), title, description, status (open|process|solved), solution, created_at, updated_at
```

---

## 🎨 UI Features

All dashboards use **Tailwind CSS minimalist design**:
- Clean borders (not heavy shadows)
- Simple grid layouts
- Minimal color usage
- Hover effects for interactivity

**Components:**
- Material cards (siswa view)
- Stats boxes (admin/guru view)
- Forms with dashed-border file inputs (guru upload)
- Clean tables for lists

---

## 📚 Files for Customization

**To change UI colors:** `resources/css/app.css` (edit Tailwind config)

**To add new roles:** 
1. Add to enum validation in `RegisteredUserController`
2. Add case in `/dashboard` route redirect
3. Create new middleware group in `routes/web.php`
4. Create new controller & views

**To add new features:**
1. Create migration: `php artisan make:migration create_table_name`
2. Create model: `php artisan make:model TableName`
3. Create controller: `php artisan make:controller FeatureController`
4. Create routes in `routes/web.php`
5. Create views in `resources/views/`

---

## 🔐 Security Notes

- **Auth:** Session-based (Laravel Breeze default)
- **Roles:** Checked via middleware + policies
- **CSRF:** All forms use `@csrf` token
- **Ownership:** Material edit/delete checked via MaterialPolicy
- **File upload:** Validated mimes (pdf, doc, docx, xls, xlsx) + max 10MB

---

## 📞 Need Help?

See [SETUP_GUIDE.md](./SETUP_GUIDE.md) for detailed setup.
See [TESTING_GUIDE.md](./TESTING_GUIDE.md) for testing procedures.

