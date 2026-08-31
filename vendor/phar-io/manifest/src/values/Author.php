<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Manifest.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Manifest;

use function sprintf;

class Author {
    /** @var string */
    private $name;

    /** @var null|Email */
    private $email;

    public function __construct(string $name, ?Email $email = null) {
        $this->name  = $name;
        $this->email = $email;
    }

    public function asString(): string {
        if (!$this->hasEmail()) {
            return $this->name;
        }

        return sprintf(
            '%s <%s>',
            $this->name,
            $this->email->asString()
        );
    }

    public function getName(): string {
        return $this->name;
    }

    /**
     * @psalm-assert-if-true Email $this->email
     */
    public function hasEmail(): bool {
        return $this->email !== null;
    }

    public function getEmail(): Email {
        if (!$this->hasEmail()) {
            throw new NoEmailAddressException();
        }

        return $this->email;
    }
}
