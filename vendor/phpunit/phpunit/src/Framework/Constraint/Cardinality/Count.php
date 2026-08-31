<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint;

use function count;
use function is_countable;
use function iterator_count;
use function sprintf;
use EmptyIterator;
use Generator;
use Iterator;
use IteratorAggregate;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\GeneratorNotSupportedException;
use SebastianBergmann\RecursionContext\Context;
use Traversable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
class Count extends Constraint
{
    private readonly int $expectedCount;

    public function __construct(int $expected)
    {
        $this->expectedCount = $expected;
    }

    public function toString(): string
    {
        return sprintf(
            'count matches %d',
            $this->expectedCount,
        );
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @throws Exception
     */
    protected function matches(mixed $other): bool
    {
        return $this->expectedCount === $this->getCountOf($other);
    }

    /**
     * @throws Exception
     */
    protected function getCountOf(mixed $other): ?int
    {
        if (is_countable($other)) {
            return count($other);
        }

        if ($other instanceof EmptyIterator) {
            return 0;
        }

        if ($other instanceof Traversable) {
            $context = new Context;

            while ($other instanceof IteratorAggregate) {
                if ($context->contains($other) !== false) {
                    throw new Exception('IteratorAggregate::getIterator() returned an object that was already seen');
                }

                $context->add($other);

                try {
                    $other = $other->getIterator();
                } catch (\Exception $e) {
                    throw new Exception(
                        $e->getMessage(),
                        $e->getCode(),
                        $e,
                    );
                }
            }

            $iterator = $other;

            if ($iterator instanceof Generator) {
                throw new GeneratorNotSupportedException;
            }

            if (!$iterator instanceof Iterator) {
                return iterator_count($iterator);
            }

            $key   = $iterator->key();
            $count = iterator_count($iterator);

            // Manually rewind $iterator to previous key, since iterator_count
            // moves pointer.
            if ($key !== null) {
                $iterator->rewind();

                while ($iterator->valid() && $key !== $iterator->key()) {
                    $iterator->next();
                }
            }

            return $count;
        }

        return null;
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @throws Exception
     */
    protected function failureDescription(mixed $other): string
    {
        return sprintf(
            'actual size %d matches expected size %d',
            (int) $this->getCountOf($other),
            $this->expectedCount,
        );
    }
}
