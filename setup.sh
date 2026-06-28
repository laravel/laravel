#!/bin/bash
# ─────────────────────────────────────────────────────────
#  ArcanePay — Apply Part 2 Fixes
#  Jalankan di terminal GitHub Codespaces
# ─────────────────────────────────────────────────────────
set -e

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  🚀 ArcanePay — Applying Fixes"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# 1. Cek ZIP ada
if [ ! -f "arcanepay-v2.zip" ]; then
    echo "❌ ERROR: arcanepay-v2.zip tidak ditemukan!"
    echo "   Upload dulu filenya ke Codespaces, lalu jalankan lagi."
    exit 1
fi

# 2. Extract
echo "📦 Extracting arcanepay-v2.zip..."
unzip -q -o arcanepay-v2.zip
echo "   ✓ Done"

# 3. Buat direktori baru yang dibutuhkan
echo ""
echo "📁 Creating new directories..."
mkdir -p docker
mkdir -p app/Contracts
mkdir -p app/Services/Suppliers
mkdir -p app/Jobs
mkdir -p app/Http/Middleware
mkdir -p app/Filament/Resources/OrderResource/Pages
echo "   ✓ Done"

# 4. Copy semua file
echo ""
echo "🔄 Copying fixed files..."

# --- Infra ---
cp arcanepay-v2/bootstrap/app.php                bootstrap/app.php
echo "  ✅ bootstrap/app.php          (Fix #1: API routes aktif)"

cp arcanepay-v2/Dockerfile                       Dockerfile
cp arcanepay-v2/docker/start.sh                  docker/start.sh
chmod +x docker/start.sh
echo "  ✅ Dockerfile + docker/start.sh (Fix #2: Apache DocumentRoot)"

cp arcanepay-v2/.env.example                     .env.example
echo "  ✅ .env.example               (semua env vars lengkap)"

cp arcanepay-v2/railway.json                     railway.json
echo "  ✅ railway.json               (Railway deployment config)"

cp arcanepay-v2/config/services.php              config/services.php
echo "  ✅ config/services.php        (Fonnte, Tripay, Digiflazz, VIPReseller)"

cp arcanepay-v2/routes/api.php                   routes/api.php
echo "  ✅ routes/api.php             (webhook + callback routes)"

# --- Services ---
cp arcanepay-v2/app/Contracts/SupplierDriverInterface.php \
                                                 app/Contracts/SupplierDriverInterface.php
cp arcanepay-v2/app/Services/Suppliers/DigiflazzDriver.php \
                                                 app/Services/Suppliers/DigiflazzDriver.php
cp arcanepay-v2/app/Services/Suppliers/VipResellerDriver.php \
                                                 app/Services/Suppliers/VipResellerDriver.php
cp arcanepay-v2/app/Services/SupplierManager.php app/Services/SupplierManager.php
echo "  ✅ SupplierManager + 2 Drivers (multi-supplier failover)"

cp arcanepay-v2/app/Jobs/ProcessSupplierOrder.php \
                                                 app/Jobs/ProcessSupplierOrder.php
echo "  ✅ ProcessSupplierOrder Job   (async + retry 3x)"

cp arcanepay-v2/app/Http/Middleware/VerifyFonnteWebhook.php \
                                                 app/Http/Middleware/VerifyFonnteWebhook.php
echo "  ✅ VerifyFonnteWebhook        (webhook security)"

# --- Controllers ---
cp arcanepay-v2/app/Http/Controllers/Api/FonnteController.php \
                                                 app/Http/Controllers/Api/FonnteController.php
echo "  ✅ FonnteController           (Fix #3: complete order flow)"

cp arcanepay-v2/app/Http/Controllers/Api/PaymentCallbackController.php \
                                                 app/Http/Controllers/Api/PaymentCallbackController.php
echo "  ✅ PaymentCallbackController  (Fix #4 & #5: job dispatch)"

# --- Models ---
cp arcanepay-v2/app/Models/Category.php          app/Models/Category.php
cp arcanepay-v2/app/Models/Product.php           app/Models/Product.php
cp arcanepay-v2/app/Models/Transaction.php       app/Models/Transaction.php
echo "  ✅ Models                     (Category, Product, Transaction)"

# --- Filament Resources ---
cp arcanepay-v2/app/Filament/Resources/CategoryResource.php \
                                                 app/Filament/Resources/CategoryResource.php
cp arcanepay-v2/app/Filament/Resources/OrderResource.php \
                                                 app/Filament/Resources/OrderResource.php
cp arcanepay-v2/app/Filament/Resources/ProductResource.php \
                                                 app/Filament/Resources/ProductResource.php
echo "  ✅ Filament Resources         (admin panel updated)"

# Buat ViewOrder Page kalau belum ada
VIEWORDER="app/Filament/Resources/OrderResource/Pages/ViewOrder.php"
if [ ! -f "$VIEWORDER" ]; then
cat > "$VIEWORDER" << 'PHPEOF'
<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;
}
PHPEOF
    echo "  ✅ ViewOrder.php            (created)"
fi

# --- Migrations ---
cp arcanepay-v2/database/migrations/2026_06_28_000001_update_transactions_add_payment_fields.php \
                                                 database/migrations/
cp arcanepay-v2/database/migrations/2026_06_28_000002_update_categories_add_zone_fields.php \
                                                 database/migrations/
cp arcanepay-v2/database/migrations/2026_06_28_000003_update_products_add_supplier_codes.php \
                                                 database/migrations/
echo "  ✅ 3 new migrations           (payment_url, need_zone, supplier_codes)"

# --- Seeder ---
cp arcanepay-v2/database/seeders/DatabaseSeeder.php \
                                                 database/seeders/DatabaseSeeder.php
echo "  ✅ DatabaseSeeder             (ML & FF dengan need_zone)"

# 5. Cleanup
echo ""
echo "🧹 Cleanup..."
rm -rf arcanepay-v2 arcanepay-v2.zip

# Hapus render.yaml — kita pakai Railway, bukan Render
if [ -f "render.yaml" ]; then
    rm render.yaml
    echo "   ✓ render.yaml dihapus (pakai railway.json sekarang)"
fi

# 6. Commit & Push
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  📤 Committing to GitHub..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

git add .
echo ""
echo "Files changed:"
git status --short
echo ""

git commit -m "fix: critical bugs + multi-supplier + complete WA order flow [Part 2]"
git push

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  🎉 STEP 1 DONE!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "25 files applied & pushed to GitHub ✓"
echo ""
echo "Balik ke chat, ketik: NEXT"
echo "(lanjut Step 2: Supabase Database)"
echo ""
