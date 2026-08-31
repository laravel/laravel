<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\DeprecationCollector;

use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Event\UnknownSubscriberTypeException;
use PHPUnit\TestRunner\IssueFilter;
use PHPUnit\TextUI\Configuration\Registry as ConfigurationRegistry;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Facade
{
    private static null|Collector|InIsolationCollector $collector = null;
    private static bool $inIsolation                              = false;

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    public static function init(): void
    {
        self::collector();
    }

    public static function initForIsolation(): void
    {
        self::collector();

        self::$inIsolation = true;
    }

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     *
     * @return list<non-empty-string>
     */
    public static function deprecations(): array
    {
        return self::collector()->deprecations();
    }

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     *
     * @return list<non-empty-string>
     */
    public static function filteredDeprecations(): array
    {
        return self::collector()->filteredDeprecations();
    }

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    public static function collector(): Collector|InIsolationCollector
    {
        if (self::$collector !== null) {
            return self::$collector;
        }

        $issueFilter = new IssueFilter(
            ConfigurationRegistry::get()->source(),
        );

        if (self::$inIsolation) {
            self::$collector = new InIsolationCollector(
                $issueFilter,
            );

            return self::$collector;
        }

        self::$collector = new Collector(
            EventFacade::instance(),
            $issueFilter,
        );

        return self::$collector;
    }
}
