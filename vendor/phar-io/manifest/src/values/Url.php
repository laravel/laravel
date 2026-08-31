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

use const FILTER_VALIDATE_URL;
use function filter_var;

class Url {
    /** @var string */
    private $url;

    public function __construct(string $url) {
        $this->ensureUrlIsValid($url);

        $this->url = $url;
    }

    public function asString(): string {
        return $this->url;
    }

    /**
     * @throws InvalidUrlException
     */
    private function ensureUrlIsValid(string $url): void {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlException;
        }
    }
}
