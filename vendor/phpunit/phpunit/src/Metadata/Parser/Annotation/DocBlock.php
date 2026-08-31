<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata\Annotation\Parser;

use function array_filter;
use function array_map;
use function array_merge;
use function array_values;
use function count;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function preg_split;
use function realpath;
use function substr;
use function trim;
use PharIo\Version\Exception as PharIoVersionException;
use PharIo\Version\VersionConstraintParser;
use PHPUnit\Metadata\AnnotationsAreNotSupportedForInternalClassesException;
use PHPUnit\Metadata\InvalidVersionRequirementException;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionMethod;

/**
 * This is an abstraction around a PHPUnit-specific docBlock,
 * allowing us to ask meaningful questions about a specific
 * reflection symbol.
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class DocBlock
{
    private const REGEX_REQUIRES_VERSION            = '/@requires\s+(?P<name>PHP(?:Unit)?)\s+(?P<operator>[<>=!]{0,2})\s*(?P<version>[\d\.-]+(dev|(RC|alpha|beta)[\d\.])?)[ \t]*\r?$/m';
    private const REGEX_REQUIRES_VERSION_CONSTRAINT = '/@requires\s+(?P<name>PHP(?:Unit)?)\s+(?P<constraint>[\d\t \-.|~^]+)[ \t]*\r?$/m';
    private const REGEX_REQUIRES_OS                 = '/@requires\s+(?P<name>OS(?:FAMILY)?)\s+(?P<value>.+?)[ \t]*\r?$/m';
    private const REGEX_REQUIRES_SETTING            = '/@requires\s+(?P<name>setting)\s+(?P<setting>([^ ]+?))\s*(?P<value>[\w\.-]+[\w\.]?)?[ \t]*\r?$/m';
    private const REGEX_REQUIRES                    = '/@requires\s+(?P<name>function|extension)\s+(?P<value>([^\s<>=!]+))\s*(?P<operator>[<>=!]{0,2})\s*(?P<version>[\d\.-]+[\d\.]?)?[ \t]*\r?$/m';
    private readonly string $docComment;

    /**
     * @var array<string, array<int, string>> pre-parsed annotations indexed by name and occurrence index
     */
    private readonly array $symbolAnnotations;

    /**
     * @psalm-var null|(array<
     *   string,
     *   array<int|string, string>|array{constraint: string}|array{version: string, operator: string}|string
     * >&array{
     *   __OFFSET: array<string, int>&array{__FILE: string},
     *   setting?: array<string, string>,
     *   extension_versions?: array<string, array{version: string, operator: string}>
     * })
     *
     * @phpstan-ignore missingType.iterableValue
     */
    private ?array $parsedRequirements = null;
    private readonly int $startLine;
    private readonly string $fileName;

    /**
     * @throws AnnotationsAreNotSupportedForInternalClassesException
     *
     * @phpstan-ignore missingType.generics
     */
    public static function ofClass(ReflectionClass $class): self
    {
        if ($class->isInternal()) {
            throw new AnnotationsAreNotSupportedForInternalClassesException($class->getName());
        }

        return new self(
            (string) $class->getDocComment(),
            self::extractAnnotationsFromReflector($class),
            $class->getStartLine(),
            $class->getFileName(),
        );
    }

    /**
     * @throws AnnotationsAreNotSupportedForInternalClassesException
     */
    public static function ofMethod(ReflectionMethod $method): self
    {
        if ($method->getDeclaringClass()->isInternal()) {
            throw new AnnotationsAreNotSupportedForInternalClassesException($method->getDeclaringClass()->getName());
        }

        return new self(
            (string) $method->getDocComment(),
            self::extractAnnotationsFromReflector($method),
            $method->getStartLine(),
            $method->getFileName(),
        );
    }

    /**
     * Note: we do not preserve an instance of the reflection object, since it cannot be safely (de-)serialized.
     *
     * @param array<string, array<int, string>> $symbolAnnotations
     */
    private function __construct(string $docComment, array $symbolAnnotations, int $startLine, string $fileName)
    {
        $this->docComment        = $docComment;
        $this->symbolAnnotations = $symbolAnnotations;
        $this->startLine         = $startLine;
        $this->fileName          = $fileName;
    }

    /**
     * @psalm-return array{
     *   __OFFSET: array<string, int>&array{__FILE: string},
     *   setting?: array<string, string>,
     *   extension_versions?: array<string, array{version: string, operator: string}>
     * }&array<
     *   string,
     *   string|array{version: string, operator: string}|array{constraint: string}|array<int|string, string>
     * >
     *
     * @phpstan-ignore missingType.iterableValue
     */
    public function requirements(): array
    {
        if ($this->parsedRequirements !== null) {
            return $this->parsedRequirements;
        }

        $offset            = $this->startLine;
        $requires          = [];
        $recordedSettings  = [];
        $extensionVersions = [];
        $recordedOffsets   = [
            '__FILE' => realpath($this->fileName),
        ];

        // Trim docblock markers, split it into lines and rewind offset to start of docblock
        $lines = preg_replace(['#^/\*{2}#', '#\*/$#'], '', preg_split('/\r\n|\r|\n/', $this->docComment));
        $offset -= count($lines);

        foreach ($lines as $line) {
            if (preg_match(self::REGEX_REQUIRES_OS, $line, $matches)) {
                $requires[$matches['name']]        = $matches['value'];
                $recordedOffsets[$matches['name']] = $offset;
            }

            if (preg_match(self::REGEX_REQUIRES_VERSION, $line, $matches)) {
                $requires[$matches['name']] = [
                    'version'  => $matches['version'],
                    'operator' => $matches['operator'],
                ];

                $recordedOffsets[$matches['name']] = $offset;
            }

            if (preg_match(self::REGEX_REQUIRES_VERSION_CONSTRAINT, $line, $matches)) {
                if (!empty($requires[$matches['name']])) {
                    $offset++;

                    continue;
                }

                try {
                    $versionConstraintParser = new VersionConstraintParser;

                    $requires[$matches['name'] . '_constraint'] = [
                        'constraint' => $versionConstraintParser->parse(trim($matches['constraint'])),
                    ];

                    $recordedOffsets[$matches['name'] . '_constraint'] = $offset;
                } catch (PharIoVersionException $e) {
                    throw new InvalidVersionRequirementException(
                        $e->getMessage(),
                        $e->getCode(),
                        $e,
                    );
                }
            }

            if (preg_match(self::REGEX_REQUIRES_SETTING, $line, $matches)) {
                $recordedSettings[$matches['setting']]               = $matches['value'];
                $recordedOffsets['__SETTING_' . $matches['setting']] = $offset;
            }

            if (preg_match(self::REGEX_REQUIRES, $line, $matches)) {
                $name = $matches['name'] . 's';

                if (!isset($requires[$name])) {
                    $requires[$name] = [];
                }

                $requires[$name][]                                           = $matches['value'];
                $recordedOffsets[$matches['name'] . '_' . $matches['value']] = $offset;

                if ($name === 'extensions' && !empty($matches['version'])) {
                    $extensionVersions[$matches['value']] = [
                        'version'  => $matches['version'],
                        'operator' => $matches['operator'],
                    ];
                }
            }

            $offset++;
        }

        return $this->parsedRequirements = array_merge(
            $requires,
            ['__OFFSET' => $recordedOffsets],
            array_filter(
                [
                    'setting'            => $recordedSettings,
                    'extension_versions' => $extensionVersions,
                ],
            ),
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function symbolAnnotations(): array
    {
        return $this->symbolAnnotations;
    }

    /**
     * @return array<string, array<int, string>>
     */
    private static function parseDocBlock(string $docBlock): array
    {
        // Strip away the docblock header and footer to ease parsing of one line annotations
        $docBlock    = substr($docBlock, 3, -2);
        $annotations = [];

        if (preg_match_all('/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m', $docBlock, $matches)) {
            $numMatches = count($matches[0]);

            for ($i = 0; $i < $numMatches; $i++) {
                $annotations[$matches['name'][$i]][] = $matches['value'][$i];
            }
        }

        return $annotations;
    }

    /**
     * @phpstan-ignore missingType.iterableValue, missingType.generics
     */
    private static function extractAnnotationsFromReflector(ReflectionClass|ReflectionFunctionAbstract $reflector): array
    {
        $annotations = [];

        if ($reflector instanceof ReflectionClass) {
            $annotations = array_merge(
                $annotations,
                ...array_map(
                    static fn (ReflectionClass $trait): array => self::parseDocBlock((string) $trait->getDocComment()),
                    array_values($reflector->getTraits()),
                ),
            );
        }

        return array_merge(
            $annotations,
            self::parseDocBlock((string) $reflector->getDocComment()),
        );
    }
}
