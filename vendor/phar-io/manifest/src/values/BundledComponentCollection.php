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

use Countable;
use IteratorAggregate;
use function count;

/** @template-implements IteratorAggregate<int,BundledComponent> */
class BundledComponentCollection implements Countable, IteratorAggregate {
    /** @var BundledComponent[] */
    private $bundledComponents = [];

    public function add(BundledComponent $bundledComponent): void {
        $this->bundledComponents[] = $bundledComponent;
    }

    /**
     * @return BundledComponent[]
     */
    public function getBundledComponents(): array {
        return $this->bundledComponents;
    }

    public function count(): int {
        return count($this->bundledComponents);
    }

    public function getIterator(): BundledComponentCollectionIterator {
        return new BundledComponentCollectionIterator($this);
    }
}
