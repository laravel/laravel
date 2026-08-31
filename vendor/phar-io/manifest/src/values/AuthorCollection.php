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

/** @template-implements IteratorAggregate<int,Author> */
class AuthorCollection implements Countable, IteratorAggregate {
    /** @var Author[] */
    private $authors = [];

    public function add(Author $author): void {
        $this->authors[] = $author;
    }

    /**
     * @return Author[]
     */
    public function getAuthors(): array {
        return $this->authors;
    }

    public function count(): int {
        return count($this->authors);
    }

    public function getIterator(): AuthorCollectionIterator {
        return new AuthorCollectionIterator($this);
    }
}
