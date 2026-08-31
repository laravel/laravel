<?php

namespace Egulias\EmailValidator\Validation;

class DNSRecords
{
    /**
     * @param list<array<array-key, mixed>> $records
     * @param bool $error
     */
    public function __construct(private readonly array $records, private readonly bool $error = false)
    {
    }

    /**
     * @return list<array<array-key, mixed>>
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    public function withError(): bool
    {
        return $this->error;
    }
}
