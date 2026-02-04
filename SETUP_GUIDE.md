# LMS Laravel - Setup & Fix Guide

## Quick Start

### 1. Create Storage Symlink (CRITICAL for downloads)
```bash
php artisan storage:link
```
This creates a symlink so files in `storage/app/public` are accessible via `public/storage` URL.

**Why?** When siswa downloads materials, the server needs to serve files from `storage/app/public/materials`. Without the symlink, downloads will fail.

### 2. Verify Database & Migrations
```bash
php artisan migrate
```

Check if tables exist:
```bash
php artisan tinker
>>> DB::table('users')->get();
>>> DB::table('materials')->get();
```

### 3. Create Admin User (Optional)
```bash
php artisan tinker
>>> App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@test.com',
    'password' => bcrypt('password123'),
    'role' => 'admin'
]);
```

### 4. Run Development Server
```bash
php artisan serve
```

Visit: `http://127.0.0.1:8000`

---

## How Authentication & Redirect Works

### Login Flow:
1. User visits `/login`
2. Submits credentials
3. `AuthenticatedSessionController` validates & logs in user
4. **Redirects to `/dashboard`** (smart redirect route)
5. `/dashboard` route uses `match($role)` to determine correct dashboard:
   - Role `admin` → `/admin/dashboard`
   - Role `guru` → `/guru/dashboard`
   - Role `siswa` → `/siswa/dashboard`
6. User is redirected to their role-based dashboard

### Register Flow:
1. User visits `/register`
2. Selects role (siswa or guru) - note: admin only via manual creation
3. Submits form
4. `RegisteredUserController` saves user with selected role
5. **Auto-logs in user** using `Auth::login($user)`
6. **Redirects to `/dashboard`** → smart redirect (same as above)

### Why Smart Redirect?
Without it, we'd need to hardcode redirect in controller for each auth scenario. The `/dashboard` route handles it for all cases.

---

## Troubleshooting

### Issue: 403 Forbidden (When Not 407!)
**Expected behavior!** This is correct access control.

Examples:
- Siswa tries to access `/guru/materials` → 403 ✅ Correct
- Guru tries to access `/siswa/dashboard` → 403 ✅ Correct

### Issue: 407 Proxy Authentication Required (WRONG!)
This is unusual and suggests middleware chain issue.

**Fixes:**
1. Clear session: `php artisan cache:clear`
2. Clear cookies: F12 → Application → Cookies → Delete all
3. Check middleware in `bootstrap/app.php`:
   ```php
   'role' => App\Http\Middleware\RoleMiddleware::class,
   ```
4. Verify user role in database:
   ```bash
   php artisan tinker
   >>> auth()->user(); // Should show correct role
   ```

### Issue: Download Not Working
**Symptom:** Click download → error or no file

**Fixes:**
1. Create symlink: `php artisan storage:link`
2. Verify file exists:
   ```bash
   # File should be in:
   storage/app/public/materials/some-file.pdf
   
   # Accessible at:
   public/storage/materials/some-file.pdf
   # or via URL:
   http://127.0.0.1:8000/storage/materials/some-file.pdf
   ```
3. Check file permissions:
   ```bash
   chmod -R 755 storage
   chmod -R 755 public/storage
   ```

### Issue: Material List Empty for Siswa
**Possible causes:**
1. Guru hasn't uploaded any materials yet
2. Materials table empty
3. File path issue

**Check:**
```bash
php artisan tinker
>>> App\Models\Material::all();
>>> App\Models\User::where('role', 'guru')->first()->materials;
```

---

## File Structure for Uploads

```
storage/
├── app/
│   └── public/
│       └── materials/
│           ├── timestamp-filename-1.pdf
│           ├── timestamp-filename-2.docx
│           └── timestamp-filename-3.xlsx
public/
└── storage/ (this is a symlink to storage/app/public)
    └── materials/
        ├── timestamp-filename-1.pdf
        ├── timestamp-filename-2.docx
        └── timestamp-filename-3.xlsx
```

When guru uploads a file, Laravel stores it in `storage/app/public/materials/` with a unique name.
The symlink allows web access via `/public/storage/materials/filename`.

---

## Testing Checklist

- [ ] Database migrated (`php artisan migrate`)
- [ ] Storage symlink created (`php artisan storage:link`)
- [ ] Register as Siswa → Auto-login → Redirect to `/siswa/dashboard` ✅
- [ ] Register as Guru → Auto-login → Redirect to `/guru/dashboard` ✅
- [ ] Login works for both roles ✅
- [ ] Guru can upload materials ✅
- [ ] Siswa can see all materials in dashboard grid ✅
- [ ] Siswa can download material (file downloads to computer) ✅
- [ ] Role-based access: Siswa can't access `/guru/*` routes (403) ✅
- [ ] Role-based access: Guru can't access `/siswa/*` routes (403) ✅

---

## Next Steps

1. Run `php artisan storage:link`
2. Test the flow using [TESTING_GUIDE.md](./TESTING_GUIDE.md)
3. Report any remaining issues with:
   - Error message & URL
   - Browser console errors (F12)
   - Step-by-step how to reproduce

---

## Architecture Notes

### Middleware Chain
Routes use this chain: `['auth', 'role:guru']`
- `auth` - Ensures user is logged in (redirects to login if not)
- `role:guru` - Ensures user's role is 'guru' (aborts 403 if not)

Order matters! We check auth first, then role.

### Authorization Policy
For material edit/delete, we also use `MaterialPolicy`:
- Guru can only edit/delete their own materials
- Siswa can't upload/edit materials at all

### Database Relationships
```
User (1) ─── (Many) Material
         ─── (Many) Report

Material (Many) ─── (1) User
Report (Many) ─── (1) User
```

Each material belongs to the guru who uploaded it.
Each report belongs to the user who submitted it.

---

## Common Error Messages

| Error | Cause | Fix |
|-------|-------|-----|
| 403 Forbidden | Wrong role accessing route | Login as correct role |
| 407 Proxy Auth | Middleware/session issue | Clear cache: `php artisan cache:clear` |
| File not found | Storage symlink missing | `php artisan storage:link` |
| No materials | Guru hasn't uploaded | Login as guru, upload material |
| 404 Not Found | Route doesn't exist | Check `php artisan route:list` |
| CSRF token mismatch | Form token missing | Ensure `@csrf` in form |

