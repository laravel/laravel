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

use DOMElement;
use DOMNodeList;
use function sprintf;

class ManifestElement {
    public const XMLNS = 'https://phar.io/xml/manifest/1.0';

    /** @var DOMElement */
    private $element;

    public function __construct(DOMElement $element) {
        $this->element = $element;
    }

    protected function getAttributeValue(string $name): string {
        if (!$this->element->hasAttribute($name)) {
            throw new ManifestElementException(
                sprintf(
                    'Attribute %s not set on element %s',
                    $name,
                    $this->element->localName
                )
            );
        }

        return $this->element->getAttribute($name);
    }

    protected function hasAttribute(string $name): bool {
        return $this->element->hasAttribute($name);
    }

    protected function getChildByName(string $elementName): DOMElement {
        $element = $this->element->getElementsByTagNameNS(self::XMLNS, $elementName)->item(0);

        if (!$element instanceof DOMElement) {
            throw new ManifestElementException(
                sprintf('Element %s missing', $elementName)
            );
        }

        return $element;
    }

    protected function getChildrenByName(string $elementName): DOMNodeList {
        $elementList = $this->element->getElementsByTagNameNS(self::XMLNS, $elementName);

        if ($elementList->length === 0) {
            throw new ManifestElementException(
                sprintf('Element(s) %s missing', $elementName)
            );
        }

        return $elementList;
    }

    protected function hasChild(string $elementName): bool {
        return $this->element->getElementsByTagNameNS(self::XMLNS, $elementName)->length !== 0;
    }
}
