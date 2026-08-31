<?php

declare(strict_types=1);

namespace League\Flysystem;

use InvalidArgumentException as BaseInvalidArgumentException;

class InvalidStreamProvided extends BaseInvalidArgumentException implements FilesystemException
{
}
