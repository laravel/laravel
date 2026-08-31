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

use PharIo\Version\Version;

class BundledComponent {
    /** @var string */
    private $name;

    /** @var Version */
    private $version;

    public function __construct(string $name, Version $version) {
        $this->name    = $name;
        $this->version = $version;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getVersion(): Version {
        return $this->version;
    }
}
