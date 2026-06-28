<?php

namespace App\Services;

use App\Contracts\SupplierDriverInterface;
use App\Services\Suppliers\DigiflazzDriver;
use App\Services\Suppliers\VipResellerDriver;
use Illuminate\Support\Facades\Log;

/**
 * SupplierManager
 *
 * Mengatur multiple supplier dengan auto-failover.
 * Jika primary gagal → otomatis coba secondary → dst.
 *
 * Urutan priority: Digiflazz → VIP Reseller
 * Tambah supplier baru: buat Driver baru, implement SupplierDriverInterface,
 * tambahkan ke array $drivers di constructor.
 */
class SupplierManager
{
    /** @var SupplierDriverInterface[] */
    protected array $drivers;

    public function __construct()
    {
        // Priority order: first = primary, last = last resort
        $this->drivers = [
            new DigiflazzDriver(),
            new VipResellerDriver(),
        ];
    }

    /**
     * Place order — tries each active driver in order.
     * Returns success from the first driver that succeeds.
     *
     * @param  string   $sku           Primary supplier_code from Product
     * @param  string   $target        User ID
     * @param  string|null $zone       Server/Zone ID (null if not needed)
     * @param  string   $refId         Unique reference (ARC-{orderId}-{time})
     * @param  array    $supplierCodes Per-driver override codes from product.supplier_codes
     *                                 Example: ['digiflazz' => 'ml-86', 'vipreseller' => 'ML86']
     */
    public function order(
        string $sku,
        string $target,
        ?string $zone,
        string $refId,
        array $supplierCodes = []
    ): array {
        $attempted = [];

        foreach ($this->drivers as $driver) {
            if (! $driver->isActive()) {
                Log::debug("[SupplierManager] {$driver->getName()} is inactive, skip");
                continue;
            }

            // Use supplier-specific SKU if configured, else fall back to primary SKU
            $effectiveSku = $supplierCodes[$driver->getName()] ?? $sku;

            if (empty($effectiveSku)) {
                Log::warning("[SupplierManager] No SKU for {$driver->getName()}, skip");
                continue;
            }

            Log::info("[SupplierManager] Trying {$driver->getName()}", [
                'sku'    => $effectiveSku,
                'target' => $target,
                'ref'    => $refId,
            ]);

            $result = $driver->order($effectiveSku, $target, $zone, $refId);
            $attempted[] = $driver->getName();

            // SUCCESS or PENDING → stop here
            if ($result['success'] || $result['pending']) {
                Log::info("[SupplierManager] {$driver->getName()} succeeded", [
                    'status' => $result['status'],
                ]);
                $result['attempted'] = $attempted;
                return $result;
            }

            Log::warning("[SupplierManager] {$driver->getName()} failed — trying next", [
                'status'  => $result['status'],
                'message' => $result['message'] ?? '-',
            ]);
        }

        Log::error('[SupplierManager] ALL suppliers failed', [
            'ref_id'    => $refId,
            'attempted' => $attempted,
        ]);

        return [
            'success'   => false,
            'pending'   => false,
            'status'    => 'Gagal',
            'sn'        => null,
            'message'   => 'Semua supplier tidak tersedia. Tim kami akan memeriksa.',
            'driver'    => null,
            'attempted' => $attempted,
            'raw'       => [],
        ];
    }

    /**
     * Check status of an existing order by refId.
     * If driverName given, only check that driver. Otherwise check all active.
     */
    public function checkStatus(string $refId, ?string $driverName = null): array
    {
        foreach ($this->drivers as $driver) {
            if ($driverName && $driver->getName() !== $driverName) {
                continue;
            }
            if (! $driver->isActive()) {
                continue;
            }

            $result = $driver->checkStatus($refId);
            return $result;
        }

        return [
            'success' => false,
            'pending' => false,
            'status'  => 'Gagal',
            'message' => 'Driver tidak tersedia',
            'driver'  => $driverName,
        ];
    }

    /** List all drivers and their status */
    public function getDriverStatus(): array
    {
        return collect($this->drivers)->map(fn($d) => [
            'name'   => $d->getName(),
            'active' => $d->isActive(),
        ])->toArray();
    }
}
