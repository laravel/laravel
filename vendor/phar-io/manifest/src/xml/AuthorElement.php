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

class AuthorElement extends ManifestElement {
    public function getName(): string {
        return $this->getAttributeValue('name');
    }

    public function getEmail(): string {
        return $this->getAttributeValue('email');
    }

    public function hasEMail(): bool {
        return $this->hasAttribute('email');
    }
}
