<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Header;

/**
 * A MIME Header.
 *
 * @author Chris Corbyn
 */
interface HeaderInterface
{
    /**
     * Sets the body.
     *
     * The type depends on the Header concrete class.
     */
    public function setBody(mixed $body): void;

    /**
     * Gets the body.
     *
     * The return type depends on the Header concrete class.
     */
    public function getBody(): mixed;

    public function setCharset(string $charset): void;

    public function getCharset(): ?string;

    public function setLanguage(string $lang): void;

    public function getLanguage(): ?string;

    public function getName(): string;

    public function setMaxLineLength(int $lineLength): void;

    public function getMaxLineLength(): int;

    /**
     * Gets this Header rendered as a compliant string.
     */
    public function toString(): string;

    /**
     * Gets the header's body, prepared for folding into a final header value.
     *
     * This is not necessarily RFC 2822 compliant since folding white space is
     * not added at this stage (see {@link toString()} for that).
     */
    public function getBodyAsString(): string;
}
