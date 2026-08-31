<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;
use const PHP_VERSION;
use function assert;
use function defined;
use function dirname;
use function explode;
use function is_numeric;
use function preg_match;
use function realpath;
use function sprintf;
use function str_contains;
use function str_starts_with;
use function strlen;
use function strtolower;
use function substr;
use function trim;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use PHPUnit\Runner\TestSuiteSorter;
use PHPUnit\Runner\Version;
use PHPUnit\TextUI\Configuration\Configuration;
use PHPUnit\TextUI\Configuration\Constant;
use PHPUnit\TextUI\Configuration\ConstantCollection;
use PHPUnit\TextUI\Configuration\Directory;
use PHPUnit\TextUI\Configuration\DirectoryCollection;
use PHPUnit\TextUI\Configuration\ExtensionBootstrap;
use PHPUnit\TextUI\Configuration\ExtensionBootstrapCollection;
use PHPUnit\TextUI\Configuration\File;
use PHPUnit\TextUI\Configuration\FileCollection;
use PHPUnit\TextUI\Configuration\FilterDirectory;
use PHPUnit\TextUI\Configuration\FilterDirectoryCollection;
use PHPUnit\TextUI\Configuration\Group;
use PHPUnit\TextUI\Configuration\GroupCollection;
use PHPUnit\TextUI\Configuration\IniSetting;
use PHPUnit\TextUI\Configuration\IniSettingCollection;
use PHPUnit\TextUI\Configuration\Php;
use PHPUnit\TextUI\Configuration\Source;
use PHPUnit\TextUI\Configuration\TestDirectory;
use PHPUnit\TextUI\Configuration\TestDirectoryCollection;
use PHPUnit\TextUI\Configuration\TestFile;
use PHPUnit\TextUI\Configuration\TestFileCollection;
use PHPUnit\TextUI\Configuration\TestSuite as TestSuiteConfiguration;
use PHPUnit\TextUI\Configuration\TestSuiteCollection;
use PHPUnit\TextUI\Configuration\Variable;
use PHPUnit\TextUI\Configuration\VariableCollection;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\CodeCoverage;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Clover;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Cobertura;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Crap4j;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Html as CodeCoverageHtml;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php as CodeCoveragePhp;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text as CodeCoverageText;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Xml as CodeCoverageXml;
use PHPUnit\TextUI\XmlConfiguration\Logging\Junit;
use PHPUnit\TextUI\XmlConfiguration\Logging\Logging;
use PHPUnit\TextUI\XmlConfiguration\Logging\TeamCity;
use PHPUnit\TextUI\XmlConfiguration\Logging\TestDox\Html as TestDoxHtml;
use PHPUnit\TextUI\XmlConfiguration\Logging\TestDox\Text as TestDoxText;
use PHPUnit\Util\VersionComparisonOperator;
use PHPUnit\Util\Xml\Loader as XmlLoader;
use PHPUnit\Util\Xml\XmlException;
use SebastianBergmann\CodeCoverage\Report\Html\Colors;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use Throwable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Loader
{
    /**
     * @throws Exception
     */
    public function load(string $filename): LoadedFromFileConfiguration
    {
        try {
            $document = (new XmlLoader)->loadFile($filename);
        } catch (XmlException $e) {
            throw new Exception(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }

        $xpath = new DOMXPath($document);

        try {
            $xsdFilename = (new SchemaFinder)->find(Version::series());
        } catch (CannotFindSchemaException $e) {
            throw new Exception(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }

        $configurationFileRealpath = realpath($filename);

        assert($configurationFileRealpath !== false && $configurationFileRealpath !== '');

        $validationResult = (new Validator)->validate($document, $xsdFilename);

        try {
            return new LoadedFromFileConfiguration(
                $configurationFileRealpath,
                $validationResult,
                $this->extensions($xpath),
                $this->source($configurationFileRealpath, $xpath),
                $this->codeCoverage($configurationFileRealpath, $xpath),
                $this->groups($xpath),
                $this->logging($configurationFileRealpath, $xpath),
                $this->php($configurationFileRealpath, $xpath),
                $this->phpunit($configurationFileRealpath, $document),
                $this->testSuite($configurationFileRealpath, $xpath),
            );
        } catch (Throwable $t) {
            $message = sprintf(
                'Cannot load XML configuration file %s',
                $configurationFileRealpath,
            );

            if ($validationResult->hasValidationErrors()) {
                $message .= ' because it has validation errors:' . PHP_EOL . $validationResult->asString();
            }

            throw new Exception($message, previous: $t);
        }
    }

    private function logging(string $filename, DOMXPath $xpath): Logging
    {
        $junit   = null;
        $element = $this->element($xpath, 'logging/junit');

        if ($element) {
            $junit = new Junit(
                new File(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputFile'),
                    ),
                ),
            );
        }

        $teamCity = null;
        $element  = $this->element($xpath, 'logging/teamcity');

        if ($element) {
            $teamCity = new TeamCity(
                new File(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputFile'),
                    ),
                ),
            );
        }

        $testDoxHtml = null;
        $element     = $this->element($xpath, 'logging/testdoxHtml');

        if ($element) {
            $testDoxHtml = new TestDoxHtml(
                new File(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputFile'),
                    ),
                ),
            );
        }

        $testDoxText = null;
        $element     = $this->element($xpath, 'logging/testdoxText');

        if ($element) {
            $testDoxText = new TestDoxText(
                new File(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputFile'),
                    ),
                ),
            );
        }

        return new Logging(
            $junit,
            $teamCity,
            $testDoxHtml,
            $testDoxText,
        );
    }

    private function extensions(DOMXPath $xpath): ExtensionBootstrapCollection
    {
        $extensionBootstrappers = [];

        $bootstrapNodes = $xpath->query('extensions/bootstrap');

        assert($bootstrapNodes instanceof DOMNodeList);

        foreach ($bootstrapNodes as $bootstrap) {
            assert($bootstrap instanceof DOMElement);

            $parameters = [];

            $parameterNodes = $xpath->query('parameter', $bootstrap);

            assert($parameterNodes instanceof DOMNodeList);

            foreach ($parameterNodes as $parameter) {
                assert($parameter instanceof DOMElement);

                $parameters[$parameter->getAttribute('name')] = $parameter->getAttribute('value');
            }

            $className = $bootstrap->getAttribute('class');

            assert($className !== '');

            $extensionBootstrappers[] = new ExtensionBootstrap(
                $className,
                $parameters,
            );
        }

        return ExtensionBootstrapCollection::fromArray($extensionBootstrappers);
    }

    /**
     * @return non-empty-string
     */
    private function toAbsolutePath(string $filename, string $path): string
    {
        $path = trim($path);

        if (str_starts_with($path, '/')) {
            return $path;
        }

        // Matches the following on Windows:
        //  - \\NetworkComputer\Path
        //  - \\.\D:
        //  - \\.\c:
        //  - C:\Windows
        //  - C:\windows
        //  - C:/windows
        //  - c:/windows
        if (defined('PHP_WINDOWS_VERSION_BUILD') &&
            !empty($path) &&
            ($path[0] === '\\' || (strlen($path) >= 3 && preg_match('#^[A-Z]:[/\\\]#i', substr($path, 0, 3))))) {
            return $path;
        }

        if (str_contains($path, '://')) {
            return $path;
        }

        return dirname($filename) . DIRECTORY_SEPARATOR . $path;
    }

    private function source(string $filename, DOMXPath $xpath): Source
    {
        $baseline                           = null;
        $restrictDeprecations               = false;
        $restrictNotices                    = false;
        $restrictWarnings                   = false;
        $ignoreSuppressionOfDeprecations    = false;
        $ignoreSuppressionOfPhpDeprecations = false;
        $ignoreSuppressionOfErrors          = false;
        $ignoreSuppressionOfNotices         = false;
        $ignoreSuppressionOfPhpNotices      = false;
        $ignoreSuppressionOfWarnings        = false;
        $ignoreSuppressionOfPhpWarnings     = false;
        $ignoreSelfDeprecations             = false;
        $ignoreDirectDeprecations           = false;
        $ignoreIndirectDeprecations         = false;
        $identifyIssueTrigger               = true;

        $element = $this->element($xpath, 'source');

        if ($element) {
            $baseline = $this->parseStringAttribute($element, 'baseline');

            if ($baseline !== null) {
                $baseline = $this->toAbsolutePath($filename, $baseline);
            }

            $restrictDeprecations               = $this->parseBooleanAttribute($element, 'restrictDeprecations', false);
            $restrictNotices                    = $this->parseBooleanAttribute($element, 'restrictNotices', false);
            $restrictWarnings                   = $this->parseBooleanAttribute($element, 'restrictWarnings', false);
            $ignoreSuppressionOfDeprecations    = $this->parseBooleanAttribute($element, 'ignoreSuppressionOfDeprecations', false);
            $ignoreSuppressionOfPhpDeprecations = $this->parseBooleanAttribute($element, 'ignoreSuppressionOfPhpDeprecations', false);
            $ignoreSuppressionOfErrors          = $this->parseBooleanAttribute($element, 'ignoreSuppressionOfErrors', false);
            $ignoreSuppressionOfNotices         = $this->parseBooleanAttribute($element, 'ignoreSuppressionOfNotices', false);
            $ignoreSuppressionOfPhpNotices      = $this->parseBooleanAttribute($element, 'ignoreSuppressionOfPhpNotices', false);
            $ignoreSuppressionOfWarnings        = $this->parseBooleanAttribute($element, 'ignoreSuppressionOfWarnings', false);
            $ignoreSuppressionOfPhpWarnings     = $this->parseBooleanAttribute($element, 'ignoreSuppressionOfPhpWarnings', false);
            $ignoreSelfDeprecations             = $this->parseBooleanAttribute($element, 'ignoreSelfDeprecations', false);
            $ignoreDirectDeprecations           = $this->parseBooleanAttribute($element, 'ignoreDirectDeprecations', false);
            $ignoreIndirectDeprecations         = $this->parseBooleanAttribute($element, 'ignoreIndirectDeprecations', false);
            $identifyIssueTrigger               = $this->parseBooleanAttribute($element, 'identifyIssueTrigger', true);
        }

        $deprecationTriggers = [
            'functions' => [],
            'methods'   => [],
        ];

        $functionNodes = $xpath->query('source/deprecationTrigger/function');

        assert($functionNodes instanceof DOMNodeList);

        foreach ($functionNodes as $functionNode) {
            assert($functionNode instanceof DOMElement);

            $deprecationTriggers['functions'][] = $functionNode->textContent;
        }

        $methodNodes = $xpath->query('source/deprecationTrigger/method');

        assert($methodNodes instanceof DOMNodeList);

        foreach ($methodNodes as $methodNode) {
            assert($methodNode instanceof DOMElement);

            $deprecationTriggers['methods'][] = $methodNode->textContent;
        }

        return new Source(
            $baseline,
            false,
            $this->readFilterDirectories($filename, $xpath, 'source/include/directory'),
            $this->readFilterFiles($filename, $xpath, 'source/include/file'),
            $this->readFilterDirectories($filename, $xpath, 'source/exclude/directory'),
            $this->readFilterFiles($filename, $xpath, 'source/exclude/file'),
            $restrictDeprecations,
            $restrictNotices,
            $restrictWarnings,
            $ignoreSuppressionOfDeprecations,
            $ignoreSuppressionOfPhpDeprecations,
            $ignoreSuppressionOfErrors,
            $ignoreSuppressionOfNotices,
            $ignoreSuppressionOfPhpNotices,
            $ignoreSuppressionOfWarnings,
            $ignoreSuppressionOfPhpWarnings,
            $deprecationTriggers,
            $ignoreSelfDeprecations,
            $ignoreDirectDeprecations,
            $ignoreIndirectDeprecations,
            $identifyIssueTrigger,
        );
    }

    private function codeCoverage(string $filename, DOMXPath $xpath): CodeCoverage
    {
        $pathCoverage              = false;
        $includeUncoveredFiles     = true;
        $ignoreDeprecatedCodeUnits = false;
        $disableCodeCoverageIgnore = false;

        $element = $this->element($xpath, 'coverage');

        if ($element) {
            $pathCoverage = $this->parseBooleanAttribute(
                $element,
                'pathCoverage',
                false,
            );

            $includeUncoveredFiles = $this->parseBooleanAttribute(
                $element,
                'includeUncoveredFiles',
                true,
            );

            $ignoreDeprecatedCodeUnits = $this->parseBooleanAttribute(
                $element,
                'ignoreDeprecatedCodeUnits',
                false,
            );

            $disableCodeCoverageIgnore = $this->parseBooleanAttribute(
                $element,
                'disableCodeCoverageIgnore',
                false,
            );
        }

        $clover  = null;
        $element = $this->element($xpath, 'coverage/report/clover');

        if ($element) {
            $clover = new Clover(
                new File(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputFile'),
                    ),
                ),
            );
        }

        $cobertura = null;
        $element   = $this->element($xpath, 'coverage/report/cobertura');

        if ($element) {
            $cobertura = new Cobertura(
                new File(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputFile'),
                    ),
                ),
            );
        }

        $crap4j  = null;
        $element = $this->element($xpath, 'coverage/report/crap4j');

        if ($element) {
            $crap4j = new Crap4j(
                new File(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputFile'),
                    ),
                ),
                $this->parseIntegerAttribute($element, 'threshold', 30),
            );
        }

        $html    = null;
        $element = $this->element($xpath, 'coverage/report/html');

        if ($element) {
            $defaultColors     = Colors::default();
            $defaultThresholds = Thresholds::default();

            $html = new CodeCoverageHtml(
                new Directory(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputDirectory'),
                    ),
                ),
                $this->parseIntegerAttribute($element, 'lowUpperBound', $defaultThresholds->lowUpperBound()),
                $this->parseIntegerAttribute($element, 'highLowerBound', $defaultThresholds->highLowerBound()),
                $this->parseStringAttributeWithDefault($element, 'colorSuccessLow', $defaultColors->successLow()),
                $this->parseStringAttributeWithDefault($element, 'colorSuccessMedium', $defaultColors->successMedium()),
                $this->parseStringAttributeWithDefault($element, 'colorSuccessHigh', $defaultColors->successHigh()),
                $this->parseStringAttributeWithDefault($element, 'colorWarning', $defaultColors->warning()),
                $this->parseStringAttributeWithDefault($element, 'colorDanger', $defaultColors->danger()),
                $this->parseStringAttribute($element, 'customCssFile'),
            );
        }

        $php     = null;
        $element = $this->element($xpath, 'coverage/report/php');

        if ($element) {
            $php = new CodeCoveragePhp(
                new File(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputFile'),
                    ),
                ),
            );
        }

        $text    = null;
        $element = $this->element($xpath, 'coverage/report/text');

        if ($element) {
            $text = new CodeCoverageText(
                new File(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputFile'),
                    ),
                ),
                $this->parseBooleanAttribute($element, 'showUncoveredFiles', false),
                $this->parseBooleanAttribute($element, 'showOnlySummary', false),
            );
        }

        $xml     = null;
        $element = $this->element($xpath, 'coverage/report/xml');

        if ($element) {
            $xml = new CodeCoverageXml(
                new Directory(
                    $this->toAbsolutePath(
                        $filename,
                        (string) $this->parseStringAttribute($element, 'outputDirectory'),
                    ),
                ),
            );
        }

        return new CodeCoverage(
            $pathCoverage,
            $includeUncoveredFiles,
            $ignoreDeprecatedCodeUnits,
            $disableCodeCoverageIgnore,
            $clover,
            $cobertura,
            $crap4j,
            $html,
            $php,
            $text,
            $xml,
        );
    }

    private function booleanFromString(string $value, bool $default): bool
    {
        if (strtolower($value) === 'false') {
            return false;
        }

        if (strtolower($value) === 'true') {
            return true;
        }

        return $default;
    }

    private function valueFromString(string $value): bool|string
    {
        if (strtolower($value) === 'false') {
            return false;
        }

        if (strtolower($value) === 'true') {
            return true;
        }

        return $value;
    }

    private function readFilterDirectories(string $filename, DOMXPath $xpath, string $query): FilterDirectoryCollection
    {
        $directories = [];

        $directoryNodes = $xpath->query($query);

        assert($directoryNodes instanceof DOMNodeList);

        foreach ($directoryNodes as $directoryNode) {
            assert($directoryNode instanceof DOMElement);

            $directoryPath = $directoryNode->textContent;

            if (!$directoryPath) {
                continue;
            }

            $directories[] = new FilterDirectory(
                $this->toAbsolutePath($filename, $directoryPath),
                $directoryNode->hasAttribute('prefix') ? $directoryNode->getAttribute('prefix') : '',
                $directoryNode->hasAttribute('suffix') ? $directoryNode->getAttribute('suffix') : '.php',
            );
        }

        return FilterDirectoryCollection::fromArray($directories);
    }

    private function readFilterFiles(string $filename, DOMXPath $xpath, string $query): FileCollection
    {
        $files = [];

        $fileNodes = $xpath->query($query);

        assert($fileNodes instanceof DOMNodeList);

        foreach ($fileNodes as $fileNode) {
            assert($fileNode instanceof DOMNode);

            $filePath = $fileNode->textContent;

            if ($filePath) {
                $files[] = new File($this->toAbsolutePath($filename, $filePath));
            }
        }

        return FileCollection::fromArray($files);
    }

    private function groups(DOMXPath $xpath): Groups
    {
        $include = [];
        $exclude = [];

        $groupNodes = $xpath->query('groups/include/group');

        assert($groupNodes instanceof DOMNodeList);

        foreach ($groupNodes as $groupNode) {
            assert($groupNode instanceof DOMNode);

            $include[] = new Group($groupNode->textContent);
        }

        $groupNodes = $xpath->query('groups/exclude/group');

        assert($groupNodes instanceof DOMNodeList);

        foreach ($groupNodes as $groupNode) {
            assert($groupNode instanceof DOMNode);

            $exclude[] = new Group($groupNode->textContent);
        }

        return new Groups(
            GroupCollection::fromArray($include),
            GroupCollection::fromArray($exclude),
        );
    }

    private function parseBooleanAttribute(DOMElement $element, string $attribute, bool $default): bool
    {
        if (!$element->hasAttribute($attribute)) {
            return $default;
        }

        return $this->booleanFromString(
            $element->getAttribute($attribute),
            false,
        );
    }

    private function parseIntegerAttribute(DOMElement $element, string $attribute, int $default): int
    {
        if (!$element->hasAttribute($attribute)) {
            return $default;
        }

        return $this->parseInteger(
            $element->getAttribute($attribute),
            $default,
        );
    }

    private function parseStringAttribute(DOMElement $element, string $attribute): ?string
    {
        if (!$element->hasAttribute($attribute)) {
            return null;
        }

        return $element->getAttribute($attribute);
    }

    private function parseStringAttributeWithDefault(DOMElement $element, string $attribute, string $default): string
    {
        if (!$element->hasAttribute($attribute)) {
            return $default;
        }

        return $element->getAttribute($attribute);
    }

    private function parseInteger(string $value, int $default): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }

    private function php(string $filename, DOMXPath $xpath): Php
    {
        $includePaths = [];

        $includePathNodes = $xpath->query('php/includePath');

        assert($includePathNodes instanceof DOMNodeList);

        foreach ($includePathNodes as $includePath) {
            assert($includePath instanceof DOMNode);

            $path = $includePath->textContent;

            if ($path) {
                $includePaths[] = new Directory($this->toAbsolutePath($filename, $path));
            }
        }

        $iniSettings = [];

        $iniNodes = $xpath->query('php/ini');

        assert($iniNodes instanceof DOMNodeList);

        foreach ($iniNodes as $ini) {
            assert($ini instanceof DOMElement);

            $iniSettings[] = new IniSetting(
                $ini->getAttribute('name'),
                $ini->getAttribute('value'),
            );
        }

        $constants = [];

        $constNodes = $xpath->query('php/const');

        assert($constNodes instanceof DOMNodeList);

        foreach ($constNodes as $constNode) {
            assert($constNode instanceof DOMElement);

            $value = $constNode->getAttribute('value');

            $constants[] = new Constant(
                $constNode->getAttribute('name'),
                $this->valueFromString($value),
            );
        }

        $variables = [
            'var'     => [],
            'env'     => [],
            'post'    => [],
            'get'     => [],
            'cookie'  => [],
            'server'  => [],
            'files'   => [],
            'request' => [],
        ];

        foreach (['var', 'env', 'post', 'get', 'cookie', 'server', 'files', 'request'] as $array) {
            $varNodes = $xpath->query('php/' . $array);

            assert($varNodes instanceof DOMNodeList);

            foreach ($varNodes as $var) {
                assert($var instanceof DOMElement);

                $name     = $var->getAttribute('name');
                $value    = $var->getAttribute('value');
                $force    = false;
                $verbatim = false;

                if ($var->hasAttribute('force')) {
                    $force = $this->booleanFromString($var->getAttribute('force'), false);
                }

                if ($var->hasAttribute('verbatim')) {
                    $verbatim = $this->booleanFromString($var->getAttribute('verbatim'), false);
                }

                if (!$verbatim) {
                    $value = $this->valueFromString($value);
                }

                $variables[$array][] = new Variable($name, $value, $force);
            }
        }

        return new Php(
            DirectoryCollection::fromArray($includePaths),
            IniSettingCollection::fromArray($iniSettings),
            ConstantCollection::fromArray($constants),
            VariableCollection::fromArray($variables['var']),
            VariableCollection::fromArray($variables['env']),
            VariableCollection::fromArray($variables['post']),
            VariableCollection::fromArray($variables['get']),
            VariableCollection::fromArray($variables['cookie']),
            VariableCollection::fromArray($variables['server']),
            VariableCollection::fromArray($variables['files']),
            VariableCollection::fromArray($variables['request']),
        );
    }

    private function phpunit(string $filename, DOMDocument $document): PHPUnit
    {
        $executionOrder      = TestSuiteSorter::ORDER_DEFAULT;
        $defectsFirst        = false;
        $resolveDependencies = $this->parseBooleanAttribute($document->documentElement, 'resolveDependencies', true);

        if ($document->documentElement->hasAttribute('executionOrder')) {
            foreach (explode(',', $document->documentElement->getAttribute('executionOrder')) as $order) {
                switch ($order) {
                    case 'default':
                        $executionOrder      = TestSuiteSorter::ORDER_DEFAULT;
                        $defectsFirst        = false;
                        $resolveDependencies = true;

                        break;

                    case 'depends':
                        $resolveDependencies = true;

                        break;

                    case 'no-depends':
                        $resolveDependencies = false;

                        break;

                    case 'defects':
                        $defectsFirst = true;

                        break;

                    case 'duration':
                        $executionOrder = TestSuiteSorter::ORDER_DURATION;

                        break;

                    case 'random':
                        $executionOrder = TestSuiteSorter::ORDER_RANDOMIZED;

                        break;

                    case 'reverse':
                        $executionOrder = TestSuiteSorter::ORDER_REVERSED;

                        break;

                    case 'size':
                        $executionOrder = TestSuiteSorter::ORDER_SIZE;

                        break;
                }
            }
        }

        $cacheDirectory = $this->parseStringAttribute($document->documentElement, 'cacheDirectory');

        if ($cacheDirectory !== null) {
            $cacheDirectory = $this->toAbsolutePath($filename, $cacheDirectory);
        }

        $bootstrap = $this->parseStringAttribute($document->documentElement, 'bootstrap');

        if ($bootstrap !== null) {
            $bootstrap = $this->toAbsolutePath($filename, $bootstrap);
        }

        $extensionsDirectory = $this->parseStringAttribute($document->documentElement, 'extensionsDirectory');

        if ($extensionsDirectory !== null) {
            $extensionsDirectory = $this->toAbsolutePath($filename, $extensionsDirectory);
        }

        $backupStaticProperties = false;

        if ($document->documentElement->hasAttribute('backupStaticProperties')) {
            $backupStaticProperties = $this->parseBooleanAttribute($document->documentElement, 'backupStaticProperties', false);
        }

        $requireCoverageMetadata = false;

        if ($document->documentElement->hasAttribute('requireCoverageMetadata')) {
            $requireCoverageMetadata = $this->parseBooleanAttribute($document->documentElement, 'requireCoverageMetadata', false);
        }

        $beStrictAboutCoverageMetadata = false;

        if ($document->documentElement->hasAttribute('beStrictAboutCoverageMetadata')) {
            $beStrictAboutCoverageMetadata = $this->parseBooleanAttribute($document->documentElement, 'beStrictAboutCoverageMetadata', false);
        }

        $shortenArraysForExportThreshold = $this->parseIntegerAttribute($document->documentElement, 'shortenArraysForExportThreshold', 0);

        if ($shortenArraysForExportThreshold < 0) {
            $shortenArraysForExportThreshold = 0;
        }

        return new PHPUnit(
            $cacheDirectory,
            $this->parseBooleanAttribute($document->documentElement, 'cacheResult', true),
            $this->parseColumns($document),
            $this->parseColors($document),
            $this->parseBooleanAttribute($document->documentElement, 'stderr', false),
            $this->parseBooleanAttribute($document->documentElement, 'displayDetailsOnAllIssues', false),
            $this->parseBooleanAttribute($document->documentElement, 'displayDetailsOnIncompleteTests', false),
            $this->parseBooleanAttribute($document->documentElement, 'displayDetailsOnSkippedTests', false),
            $this->parseBooleanAttribute($document->documentElement, 'displayDetailsOnTestsThatTriggerDeprecations', false),
            $this->parseBooleanAttribute($document->documentElement, 'displayDetailsOnPhpunitDeprecations', false),
            $this->parseBooleanAttribute($document->documentElement, 'displayDetailsOnTestsThatTriggerErrors', false),
            $this->parseBooleanAttribute($document->documentElement, 'displayDetailsOnTestsThatTriggerNotices', false),
            $this->parseBooleanAttribute($document->documentElement, 'displayDetailsOnTestsThatTriggerWarnings', false),
            $this->parseBooleanAttribute($document->documentElement, 'reverseDefectList', false),
            $requireCoverageMetadata,
            $bootstrap,
            $this->parseBooleanAttribute($document->documentElement, 'processIsolation', false),
            $this->parseBooleanAttribute($document->documentElement, 'failOnAllIssues', false),
            $this->parseBooleanAttribute($document->documentElement, 'failOnDeprecation', false),
            $this->parseBooleanAttribute($document->documentElement, 'failOnPhpunitDeprecation', false),
            $this->parseBooleanAttribute($document->documentElement, 'failOnPhpunitWarning', true),
            $this->parseBooleanAttribute($document->documentElement, 'failOnEmptyTestSuite', false),
            $this->parseBooleanAttribute($document->documentElement, 'failOnIncomplete', false),
            $this->parseBooleanAttribute($document->documentElement, 'failOnNotice', false),
            $this->parseBooleanAttribute($document->documentElement, 'failOnRisky', false),
            $this->parseBooleanAttribute($document->documentElement, 'failOnSkipped', false),
            $this->parseBooleanAttribute($document->documentElement, 'failOnWarning', false),
            $this->parseBooleanAttribute($document->documentElement, 'stopOnDefect', false),
            $this->parseBooleanAttribute($document->documentElement, 'stopOnDeprecation', false),
            $this->parseBooleanAttribute($document->documentElement, 'stopOnError', false),
            $this->parseBooleanAttribute($document->documentElement, 'stopOnFailure', false),
            $this->parseBooleanAttribute($document->documentElement, 'stopOnIncomplete', false),
            $this->parseBooleanAttribute($document->documentElement, 'stopOnNotice', false),
            $this->parseBooleanAttribute($document->documentElement, 'stopOnRisky', false),
            $this->parseBooleanAttribute($document->documentElement, 'stopOnSkipped', false),
            $this->parseBooleanAttribute($document->documentElement, 'stopOnWarning', false),
            $extensionsDirectory,
            $this->parseBooleanAttribute($document->documentElement, 'beStrictAboutChangesToGlobalState', false),
            $this->parseBooleanAttribute($document->documentElement, 'beStrictAboutOutputDuringTests', false),
            $this->parseBooleanAttribute($document->documentElement, 'beStrictAboutTestsThatDoNotTestAnything', true),
            $beStrictAboutCoverageMetadata,
            $this->parseBooleanAttribute($document->documentElement, 'enforceTimeLimit', false),
            $this->parseIntegerAttribute($document->documentElement, 'defaultTimeLimit', 1),
            $this->parseIntegerAttribute($document->documentElement, 'timeoutForSmallTests', 1),
            $this->parseIntegerAttribute($document->documentElement, 'timeoutForMediumTests', 10),
            $this->parseIntegerAttribute($document->documentElement, 'timeoutForLargeTests', 60),
            $this->parseStringAttribute($document->documentElement, 'defaultTestSuite'),
            $executionOrder,
            $resolveDependencies,
            $defectsFirst,
            $this->parseBooleanAttribute($document->documentElement, 'backupGlobals', false),
            $backupStaticProperties,
            $this->parseBooleanAttribute($document->documentElement, 'testdox', false),
            $this->parseBooleanAttribute($document->documentElement, 'testdoxSummary', false),
            $this->parseBooleanAttribute($document->documentElement, 'controlGarbageCollector', false),
            $this->parseIntegerAttribute($document->documentElement, 'numberOfTestsBeforeGarbageCollection', 100),
            $shortenArraysForExportThreshold,
        );
    }

    private function parseColors(DOMDocument $document): string
    {
        $colors = Configuration::COLOR_DEFAULT;

        if ($document->documentElement->hasAttribute('colors')) {
            /* only allow boolean for compatibility with previous versions
              'always' only allowed from command line */
            if ($this->booleanFromString($document->documentElement->getAttribute('colors'), false)) {
                $colors = Configuration::COLOR_AUTO;
            } else {
                $colors = Configuration::COLOR_NEVER;
            }
        }

        return $colors;
    }

    private function parseColumns(DOMDocument $document): int|string
    {
        $columns = 80;

        if ($document->documentElement->hasAttribute('columns')) {
            $columns = $document->documentElement->getAttribute('columns');

            if ($columns !== 'max') {
                $columns = $this->parseInteger($columns, 80);
            }
        }

        return $columns;
    }

    private function testSuite(string $filename, DOMXPath $xpath): TestSuiteCollection
    {
        $testSuites = [];

        foreach ($this->parseTestSuiteElements($xpath) as $element) {
            $exclude = [];

            foreach ($element->getElementsByTagName('exclude') as $excludeNode) {
                $excludeFile = $excludeNode->textContent;

                if ($excludeFile) {
                    $exclude[] = new File($this->toAbsolutePath($filename, $excludeFile));
                }
            }

            $directories = [];

            foreach ($element->getElementsByTagName('directory') as $directoryNode) {
                assert($directoryNode instanceof DOMElement);

                $directory = $directoryNode->textContent;

                if (empty($directory)) {
                    continue;
                }

                $prefix = '';

                if ($directoryNode->hasAttribute('prefix')) {
                    $prefix = $directoryNode->getAttribute('prefix');
                }

                $suffix = 'Test.php';

                if ($directoryNode->hasAttribute('suffix')) {
                    $suffix = $directoryNode->getAttribute('suffix');
                }

                $phpVersion = PHP_VERSION;

                if ($directoryNode->hasAttribute('phpVersion')) {
                    $phpVersion = $directoryNode->getAttribute('phpVersion');
                }

                $phpVersionOperator = new VersionComparisonOperator('>=');

                if ($directoryNode->hasAttribute('phpVersionOperator')) {
                    $phpVersionOperator = new VersionComparisonOperator($directoryNode->getAttribute('phpVersionOperator'));
                }

                $groups = [];

                if ($directoryNode->hasAttribute('groups')) {
                    foreach (explode(',', $directoryNode->getAttribute('groups')) as $group) {
                        $group = trim($group);

                        if (empty($group)) {
                            continue;
                        }

                        $groups[] = $group;
                    }
                }

                $directories[] = new TestDirectory(
                    $this->toAbsolutePath($filename, $directory),
                    $prefix,
                    $suffix,
                    $phpVersion,
                    $phpVersionOperator,
                    $groups,
                );
            }

            $files = [];

            foreach ($element->getElementsByTagName('file') as $fileNode) {
                assert($fileNode instanceof DOMElement);

                $file = $fileNode->textContent;

                if (empty($file)) {
                    continue;
                }

                $phpVersion = PHP_VERSION;

                if ($fileNode->hasAttribute('phpVersion')) {
                    $phpVersion = $fileNode->getAttribute('phpVersion');
                }

                $phpVersionOperator = new VersionComparisonOperator('>=');

                if ($fileNode->hasAttribute('phpVersionOperator')) {
                    $phpVersionOperator = new VersionComparisonOperator($fileNode->getAttribute('phpVersionOperator'));
                }

                $groups = [];

                if ($fileNode->hasAttribute('groups')) {
                    foreach (explode(',', $fileNode->getAttribute('groups')) as $group) {
                        $group = trim($group);

                        if (empty($group)) {
                            continue;
                        }

                        $groups[] = $group;
                    }
                }

                $files[] = new TestFile(
                    $this->toAbsolutePath($filename, $file),
                    $phpVersion,
                    $phpVersionOperator,
                    $groups,
                );
            }

            $name = $element->getAttribute('name');

            assert(!empty($name));

            $testSuites[] = new TestSuiteConfiguration(
                $name,
                TestDirectoryCollection::fromArray($directories),
                TestFileCollection::fromArray($files),
                FileCollection::fromArray($exclude),
            );
        }

        return TestSuiteCollection::fromArray($testSuites);
    }

    /**
     * @return list<DOMElement>
     */
    private function parseTestSuiteElements(DOMXPath $xpath): array
    {
        $elements = [];

        $testSuiteNodes = $xpath->query('testsuites/testsuite');

        assert($testSuiteNodes instanceof DOMNodeList);

        if ($testSuiteNodes->length === 0) {
            $testSuiteNodes = $xpath->query('testsuite');

            assert($testSuiteNodes instanceof DOMNodeList);
        }

        if ($testSuiteNodes->length === 1) {
            $element = $testSuiteNodes->item(0);

            assert($element instanceof DOMElement);

            $elements[] = $element;
        } else {
            foreach ($testSuiteNodes as $testSuiteNode) {
                assert($testSuiteNode instanceof DOMElement);

                $elements[] = $testSuiteNode;
            }
        }

        return $elements;
    }

    private function element(DOMXPath $xpath, string $element): ?DOMElement
    {
        $nodes = $xpath->query($element);

        assert($nodes instanceof DOMNodeList);

        if ($nodes->length === 1) {
            $node = $nodes->item(0);

            assert($node instanceof DOMElement);

            return $node;
        }

        return null;
    }
}
