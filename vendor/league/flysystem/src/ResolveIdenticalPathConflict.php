<?php
declare(strict_types=1);

namespace League\Flysystem;

class ResolveIdenticalPathConflict
{
    public const IGNORE = 'ignore';
    public const FAIL = 'fail';
    public const TRY = 'try';
}
