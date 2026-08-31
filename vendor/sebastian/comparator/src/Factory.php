<?php declare(strict_types=1);
/*
 * This file is part of sebastian/comparator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Comparator;

use const PHP_VERSION;
use function array_unshift;
use function extension_loaded;
use function version_compare;

final class Factory
{
    private static ?Factory $instance = null;

    /**
     * @var array<non-negative-int, Comparator>
     */
    private array $customComparators = [];

    /**
     * @var list<Comparator>
     */
    private array $defaultComparators = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self; // @codeCoverageIgnore
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->registerDefaultComparators();
    }

    public function getComparatorFor(mixed $expected, mixed $actual): Comparator
    {
        foreach ($this->customComparators as $comparator) {
            if ($comparator->accepts($expected, $actual)) {
                return $comparator;
            }
        }

        foreach ($this->defaultComparators as $comparator) {
            if ($comparator->accepts($expected, $actual)) {
                return $comparator;
            }
        }

        throw new RuntimeException('No suitable Comparator implementation found');
    }

    /**
     * Registers a new comparator.
     *
     * This comparator will be returned by getComparatorFor() if its accept() method
     * returns TRUE for the compared values. It has higher priority than the
     * existing comparators, meaning that its accept() method will be invoked
     * before those of the other comparators.
     */
    public function register(Comparator $comparator): void
    {
        array_unshift($this->customComparators, $comparator);

        $comparator->setFactory($this);
    }

    /**
     * Unregisters a comparator.
     *
     * This comparator will no longer be considered by getComparatorFor().
     */
    public function unregister(Comparator $comparator): void
    {
        foreach ($this->customComparators as $key => $_comparator) {
            if ($comparator === $_comparator) {
                unset($this->customComparators[$key]);
            }
        }
    }

    public function reset(): void
    {
        $this->customComparators = [];
    }

    private function registerDefaultComparators(): void
    {
        $this->registerDefaultComparator(new MockObjectComparator);
        $this->registerDefaultComparator(new DateTimeComparator);
        $this->registerDefaultComparator(new DOMNodeComparator);
        $this->registerDefaultComparator(new SplObjectStorageComparator);
        $this->registerDefaultComparator(new ExceptionComparator);
        $this->registerDefaultComparator(new EnumerationComparator);

        if (extension_loaded('bcmath') && version_compare(PHP_VERSION, '8.4.0', '>=')) {
            $this->registerDefaultComparator(new NumberComparator);
        }

        $this->registerDefaultComparator(new ObjectComparator);
        $this->registerDefaultComparator(new ResourceComparator);
        $this->registerDefaultComparator(new ArrayComparator);
        $this->registerDefaultComparator(new NumericComparator);
        $this->registerDefaultComparator(new ScalarComparator);
        $this->registerDefaultComparator(new TypeComparator);
    }

    private function registerDefaultComparator(Comparator $comparator): void
    {
        $this->defaultComparators[] = $comparator;

        $comparator->setFactory($this);
    }
}
