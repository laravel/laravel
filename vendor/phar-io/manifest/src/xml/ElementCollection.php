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
use Iterator;
use ReturnTypeWillChange;
use function count;
use function get_class;
use function sprintf;

/** @template-implements Iterator<int,DOMElement> */
abstract class ElementCollection implements Iterator {
    /** @var DOMElement[] */
    private $nodes = [];

    /** @var int */
    private $position;

    public function __construct(DOMNodeList $nodeList) {
        $this->position = 0;
        $this->importNodes($nodeList);
    }

    #[ReturnTypeWillChange]
    abstract public function current();

    public function next(): void {
        $this->position++;
    }

    public function key(): int {
        return $this->position;
    }

    public function valid(): bool {
        return $this->position < count($this->nodes);
    }

    public function rewind(): void {
        $this->position = 0;
    }

    protected function getCurrentElement(): DOMElement {
        return $this->nodes[$this->position];
    }

    private function importNodes(DOMNodeList $nodeList): void {
        foreach ($nodeList as $node) {
            if (!$node instanceof DOMElement) {
                throw new ElementCollectionException(
                    sprintf('\DOMElement expected, got \%s', get_class($node))
                );
            }

            $this->nodes[] = $node;
        }
    }
}
