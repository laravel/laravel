<?php

namespace App\Contracts;

interface SupplierDriverInterface
{
    /** Driver identifier, e.g. 'digiflazz' */
    public function getName(): string;

    /** Whether this driver is configured & enabled */
    public function isActive(): bool;

    /**
     * Place an order with the supplier.
     *
     * @param  string       $sku      Product code for this supplier
     * @param  string       $target   User ID / customer number
     * @param  string|null  $zone     Server/Zone ID (null for single-input games)
     * @param  string       $refId    Our unique reference (ARC-{id}-{time})
     *
     * @return array{
     *   success: bool,
     *   pending: bool,
     *   status: string,
     *   sn: string|null,
     *   message: string,
     *   driver: string,
     *   raw: array
     * }
     */
    public function order(string $sku, string $target, ?string $zone, string $refId): array;

    /**
     * Check existing order status by our refId.
     *
     * @return array{success: bool, pending: bool, status: string, sn: string|null}
     */
    public function checkStatus(string $refId): array;
}
