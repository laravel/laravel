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

class License {
    /** @var string */
    private $name;

    /** @var Url */
    private $url;

    public function __construct(string $name, Url $url) {
        $this->name = $name;
        $this->url  = $url;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getUrl(): Url {
        return $this->url;
    }
}
