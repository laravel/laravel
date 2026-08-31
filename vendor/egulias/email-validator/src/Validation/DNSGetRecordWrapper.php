<?php

namespace Egulias\EmailValidator\Validation;

class DNSGetRecordWrapper
{
    /**
     * @param string $host
     * @param int $type
     *
     * @return DNSRecords
     */
    public function getRecords(string $host, int $type): DNSRecords
    {
        // A workaround to fix https://bugs.php.net/bug.php?id=73149
        set_error_handler(
            static function (int $errorLevel, string $errorMessage): never {
                throw new \RuntimeException("Unable to get DNS record for the host: $errorMessage");
            }
        );
        try {
            // Get all MX, A and AAAA DNS records for host
            return new DNSRecords(dns_get_record($host, $type));
        } catch (\RuntimeException $exception) {
            return new DNSRecords([], true);
        } finally {
            restore_error_handler();
        }
    }
}
