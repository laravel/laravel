<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Configuration;

use function array_keys;
use function assert;
use SebastianBergmann\CodeCoverage\Filter;

/**
 * CLI options and XML configuration are static within a single PHPUnit process.
 * It is therefore okay to use a Singleton registry here.
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class CodeCoverageFilterRegistry
{
    private static ?self $instance = null;
    private ?Filter $filter        = null;
    private bool $configured       = false;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * @codeCoverageIgnore
     */
    public function get(): Filter
    {
        assert($this->filter !== null);

        return $this->filter;
    }

    /**
     * @codeCoverageIgnore
     */
    public function init(Configuration $configuration, bool $force = false): void
    {
        if (!$configuration->hasCoverageReport() && !$force) {
            return;
        }

        if ($this->configured && !$force) {
            return;
        }

        $this->filter = new Filter;

        if ($configuration->source()->notEmpty()) {
            $this->filter->includeFiles(array_keys((new SourceMapper)->map($configuration->source())));

            $this->configured = true;
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function configured(): bool
    {
        return $this->configured;
    }
}
