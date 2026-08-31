<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util;

use PHPUnit\TextUI\Configuration\Registry as ConfigurationRegistry;
use SebastianBergmann\Exporter\Exporter as OriginalExporter;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class Exporter
{
    private static ?OriginalExporter $exporter = null;

    public static function export(mixed $value): string
    {
        return self::exporter()->export($value);
    }

    /**
     * @param array<mixed> $data
     */
    public static function shortenedRecursiveExport(array $data): string
    {
        return self::exporter()->shortenedRecursiveExport($data);
    }

    public static function shortenedExport(mixed $value): string
    {
        return self::exporter()->shortenedExport($value);
    }

    private static function exporter(): OriginalExporter
    {
        if (self::$exporter !== null) {
            return self::$exporter;
        }

        self::$exporter = new OriginalExporter(
            ConfigurationRegistry::get()->shortenArraysForExportThreshold(),
        );

        return self::$exporter;
    }
}
