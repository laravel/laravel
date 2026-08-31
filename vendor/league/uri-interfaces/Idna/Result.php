<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\Idna;

/**
 * @see https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/uidna_8h.html
 */
final class Result
{
    private function __construct(
        private readonly string $domain,
        private readonly bool $isTransitionalDifferent,
        /** @var array<Error> */
        private readonly array $errors
    ) {
    }

    /**
     * @param array{result:string, isTransitionalDifferent:bool, errors:int} $infos
     */
    public static function fromIntl(array $infos): self
    {
        return new self($infos['result'], $infos['isTransitionalDifferent'], Error::filterByErrorBytes($infos['errors']));
    }

    public function domain(): string
    {
        return $this->domain;
    }

    public function isTransitionalDifferent(): bool
    {
        return $this->isTransitionalDifferent;
    }

    /**
     * @return array<Error>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return [] !== $this->errors;
    }

    public function hasError(Error $error): bool
    {
        return in_array($error, $this->errors, true);
    }
}
