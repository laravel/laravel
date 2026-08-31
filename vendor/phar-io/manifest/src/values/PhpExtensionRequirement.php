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

class PhpExtensionRequirement implements Requirement {
    /** @var string */
    private $extension;

    public function __construct(string $extension) {
        $this->extension = $extension;
    }

    public function asString(): string {
        return $this->extension;
    }
}
