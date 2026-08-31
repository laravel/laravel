<?php

namespace Egulias\EmailValidator\Result\Reason;

/**
 * Used on SERVFAIL, TIMEOUT or other runtime and network errors
 */
class UnableToGetDNSRecord extends NoDNSRecord
{
    public function code() : int
    {
        return 3;
    }

    public function description() : string
    {
        return 'Unable to get DNS records for the host';
    }
}
