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

class ContainsElement extends ManifestElement {
    public function getName(): string {
        return $this->getAttributeValue('name');
    }

    public function getVersion(): string {
        return $this->getAttributeValue('version');
    }

    public function getType(): string {
        return $this->getAttributeValue('type');
    }

    public function getExtensionElement(): ExtensionElement {
        return new ExtensionElement(
            $this->getChildByName('extension')
        );
    }
}
