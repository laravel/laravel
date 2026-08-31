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

use const PHP_OS_FAMILY;
use function class_exists;
use function defined;
use function dirname;
use function is_dir;
use function realpath;
use function str_starts_with;
use function sys_get_temp_dir;
use Composer\Autoload\ClassLoader;
use DeepCopy\DeepCopy;
use PharIo\Manifest\Manifest;
use PharIo\Version\Version as PharIoVersion;
use PhpParser\Parser;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SebastianBergmann\CliParser\Parser as CliParser;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeUnit\CodeUnit;
use SebastianBergmann\CodeUnitReverseLookup\Wizard;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Complexity\Calculator;
use SebastianBergmann\Diff\Diff;
use SebastianBergmann\Environment\Runtime;
use SebastianBergmann\Exporter\Exporter;
use SebastianBergmann\FileIterator\Facade as FileIteratorFacade;
use SebastianBergmann\GlobalState\Snapshot;
use SebastianBergmann\Invoker\Invoker;
use SebastianBergmann\LinesOfCode\Counter;
use SebastianBergmann\ObjectEnumerator\Enumerator;
use SebastianBergmann\ObjectReflector\ObjectReflector;
use SebastianBergmann\RecursionContext\Context;
use SebastianBergmann\Template\Template;
use SebastianBergmann\Timer\Timer;
use SebastianBergmann\Type\TypeName;
use SebastianBergmann\Version;
use staabm\SideEffectsDetector\SideEffectsDetector;
use TheSeer\Tokenizer\Tokenizer;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class ExcludeList
{
    /**
     * @var array<string,int>
     */
    private const EXCLUDED_CLASS_NAMES = [
        // composer
        ClassLoader::class => 1,

        // myclabs/deepcopy
        DeepCopy::class => 1,

        // nikic/php-parser
        Parser::class => 1,

        // phar-io/manifest
        Manifest::class => 1,

        // phar-io/version
        PharIoVersion::class => 1,

        // phpunit/phpunit
        TestCase::class => 2,

        // phpunit/php-code-coverage
        CodeCoverage::class => 1,

        // phpunit/php-file-iterator
        FileIteratorFacade::class => 1,

        // phpunit/php-invoker
        Invoker::class => 1,

        // phpunit/php-text-template
        Template::class => 1,

        // phpunit/php-timer
        Timer::class => 1,

        // sebastian/cli-parser
        CliParser::class => 1,

        // sebastian/code-unit
        CodeUnit::class => 1,

        // sebastian/code-unit-reverse-lookup
        Wizard::class => 1,

        // sebastian/comparator
        Comparator::class => 1,

        // sebastian/complexity
        Calculator::class => 1,

        // sebastian/diff
        Diff::class => 1,

        // sebastian/environment
        Runtime::class => 1,

        // sebastian/exporter
        Exporter::class => 1,

        // sebastian/global-state
        Snapshot::class => 1,

        // sebastian/lines-of-code
        Counter::class => 1,

        // sebastian/object-enumerator
        Enumerator::class => 1,

        // sebastian/object-reflector
        ObjectReflector::class => 1,

        // sebastian/recursion-context
        Context::class => 1,

        // sebastian/type
        TypeName::class => 1,

        // sebastian/version
        Version::class => 1,

        // staabm/side-effects-detector
        SideEffectsDetector::class => 1,

        // theseer/tokenizer
        Tokenizer::class => 1,
    ];

    /**
     * @var list<string>
     */
    private static array $directories = [];
    private static bool $initialized  = false;
    private readonly bool $enabled;

    /**
     * @param non-empty-string $directory
     *
     * @throws InvalidDirectoryException
     */
    public static function addDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            throw new InvalidDirectoryException($directory);
        }

        self::$directories[] = realpath($directory);
    }

    public function __construct(?bool $enabled = null)
    {
        if ($enabled === null) {
            $enabled = !defined('PHPUNIT_TESTSUITE');
        }

        $this->enabled = $enabled;
    }

    /**
     * @return list<string>
     */
    public function getExcludedDirectories(): array
    {
        self::initialize();

        return self::$directories;
    }

    public function isExcluded(string $file): bool
    {
        if (!$this->enabled) {
            return false;
        }

        self::initialize();

        foreach (self::$directories as $directory) {
            if (str_starts_with($file, $directory)) {
                return true;
            }
        }

        return false;
    }

    private static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        foreach (self::EXCLUDED_CLASS_NAMES as $className => $parent) {
            if (!class_exists($className)) {
                continue;
            }

            $directory = (new ReflectionClass($className))->getFileName();

            for ($i = 0; $i < $parent; $i++) {
                $directory = dirname($directory);
            }

            self::$directories[] = $directory;
        }

        /**
         * Hide process isolation workaround on Windows:
         * tempnam() prefix is limited to first 3 characters.
         *
         * @see https://php.net/manual/en/function.tempnam.php
         */
        if (PHP_OS_FAMILY === 'Windows') {
            // @codeCoverageIgnoreStart
            self::$directories[] = sys_get_temp_dir() . '\\PHP';
            // @codeCoverageIgnoreEnd
        }

        self::$initialized = true;
    }
}
