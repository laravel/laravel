<?php

declare(strict_types=1);

namespace Lightit\Users\Domain\DataTransferObjects;

use SensitiveParameter;

readonly class UserDto
{
    public function __construct(
        public string $name,
        public string $emailAddress,
        #[SensitiveParameter]
        public string $password,
    ) {
    }
}
