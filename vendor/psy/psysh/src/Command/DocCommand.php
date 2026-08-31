<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command;

use Psy\CommandArgumentCompletionAware;
use Psy\Completion\AnalysisResult;
use Psy\Completion\SymbolCatalog;
use Psy\Configuration;
use Psy\Exception\UnexpectedTargetException;
use Psy\Formatter\DocblockFormatter;
use Psy\Formatter\ManualFormatter;
use Psy\Formatter\SignatureFormatter;
use Psy\Input\CodeArgument;
use Psy\ManualUpdater\ManualUpdate;
use Psy\Output\ShellOutputAdapter;
use Psy\Reflection\ReflectionConstant;
use Psy\Reflection\ReflectionLanguageConstruct;
use Psy\Util\Tty;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Read the documentation for an object, class, constant, method or property.
 */
class DocCommand extends ReflectingCommand implements CommandArgumentCompletionAware
{
    const INHERIT_DOC_TAG = '{@inheritdoc}';

    private ?Configuration $config = null;
    private SymbolCatalog $symbolCatalog;
    private ?string $completionCandidateCacheKey = null;
    /** @var string[] */
    private array $completionCandidateCache = [];

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->symbolCatalog = new SymbolCatalog();
    }

    /**
     * Set the configuration instance.
     *
     * @param \Psy\Configuration $config
     */
    public function setConfiguration(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('doc')
            ->setAliases(['rtfm', 'man'])
            ->setDefinition([
                new InputOption('all', 'a', InputOption::VALUE_NONE, 'Show documentation for superclasses as well as the current class.'),
                new InputOption('update-manual', null, InputOption::VALUE_OPTIONAL, 'Download and install the latest PHP manual (optional language code)', false),
                new CodeArgument('target', CodeArgument::OPTIONAL, 'Function, class, instance, constant, method or property to document.'),
            ])
            ->setDescription('Read the documentation for an object, class, constant, method or property.')
            ->setHelp(
                <<<HELP
Read the documentation for an object, class, constant, method or property.

It's awesome for well-documented code, not quite as awesome for poorly documented code.

e.g.
<return>>>> doc preg_replace</return>
<return>>>> doc Psy\Shell</return>
<return>>>> doc Psy\Shell::debug</return>
<return>>>> \$s = new Psy\Shell</return>
<return>>>> doc \$s->run</return>
<return>>>> doc --update-manual</return>
<return>>>> doc --update-manual=fr</return>
HELP
            );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsArgumentCompletion(AnalysisResult $analysis): bool
    {
        return !\preg_match('/(\?->|->|::)/', $this->completionTarget($analysis->input));
    }

    /**
     * {@inheritdoc}
     */
    public function getArgumentCompletions(AnalysisResult $analysis): array
    {
        $manual = $this->getShell()->getManual();
        $cacheKey = $this->symbolCatalog->getVersion().':'.($manual ? \spl_object_id($manual).':'.$manual->getVersion() : 'none');

        if ($this->completionCandidateCacheKey === $cacheKey) {
            return $this->completionCandidateCache;
        }

        $candidates = \array_merge(
            $this->getRuntimeTargetCandidates(),
            $this->getLanguageConstructCandidates(),
            $this->getManualIds()
        );

        $candidates = \array_values(\array_unique($candidates));
        \sort($candidates);

        $this->completionCandidateCacheKey = $cacheKey;

        return $this->completionCandidateCache = $candidates;
    }

    /**
     * {@inheritdoc}
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shellOutput = $this->shellOutput($output);

        if ($input->getOption('update-manual') !== false) {
            return $this->handleUpdateManual($input, $output);
        }

        $value = $input->getArgument('target');
        if (!$value) {
            throw new RuntimeException('Not enough arguments (missing: "target").');
        }

        if ($this->looksLikeManualPageName($value)) {
            if (($status = $this->tryWriteManualPageTarget($output, $shellOutput, $value, true)) !== null) {
                return $status;
            }

            if ($suggestions = $this->getManualPageSuggestions($value, true)) {
                $this->writeManualTargetSuggestions($output, $value, $suggestions);

                return 1;
            }
        }

        $docFromManual = false;

        if (ReflectionLanguageConstruct::isLanguageConstruct($value)) {
            $reflector = new ReflectionLanguageConstruct($value);
            $doc = $this->getManualDocById($value, $output);
            $docFromManual = $doc !== null;
        } else {
            try {
                list($target, $reflector) = $this->getTargetAndReflector($value, $output);
            } catch (UnexpectedTargetException $e) {
                throw $e;
            } catch (\RuntimeException|\InvalidArgumentException $e) {
                if (($status = $this->tryWriteManualPageTarget($output, $shellOutput, $value)) !== null) {
                    return $status;
                }

                if ($suggestions = $this->getDocTargetSuggestions($value)) {
                    $this->writeManualTargetSuggestions($output, $value, $suggestions);

                    return 1;
                }

                throw $e;
            }

            $doc = $this->getManualDoc($reflector, $output);
            $docFromManual = $doc !== null;
            if (!$docFromManual) {
                $doc = DocblockFormatter::format($reflector);
            }
        }

        $hasManual = $this->getShell()->getManual() !== null;

        $shellOutput->startPaging();

        // Maybe include the declaring class
        if ($reflector instanceof \ReflectionMethod || $reflector instanceof \ReflectionProperty) {
            $output->writeln(SignatureFormatter::format($reflector->getDeclaringClass()));
        }

        $output->writeln(SignatureFormatter::format($reflector));
        $output->writeln('');

        if (empty($doc) && !$hasManual) {
            $output->writeln('<warning>PHP manual not found</warning>');
            $output->writeln('    To document core PHP functionality, download the PHP reference manual:');
            $output->writeln('    https://github.com/bobthecow/psysh/wiki/PHP-manual');
        } elseif ($doc !== null) {
            $output->writeln($doc);
            if ($docFromManual && (ReflectionLanguageConstruct::isLanguageConstruct($value) || $this->looksLikeManualPageName($value))) {
                $this->writeManualPageSuggestions($output, $value);
            }
        }

        // Implicit --all if the original docblock has an {@inheritdoc} tag.
        if ($input->getOption('all') || ($doc && \stripos($doc, self::INHERIT_DOC_TAG) !== false)) {
            $parent = $reflector;
            foreach ($this->getParentReflectors($reflector) as $parent) {
                $output->writeln('');
                $output->writeln('---');
                $output->writeln('');

                // Maybe include the declaring class
                if ($parent instanceof \ReflectionMethod || $parent instanceof \ReflectionProperty) {
                    $output->writeln(SignatureFormatter::format($parent->getDeclaringClass()));
                }

                $output->writeln(SignatureFormatter::format($parent));
                $output->writeln('');

                if ($doc = $this->getManualDoc($parent, $output) ?: DocblockFormatter::format($parent)) {
                    $output->writeln($doc);
                }
            }
        }

        $shellOutput->stopPaging();

        // Set some magic local variables
        $this->setCommandScopeVariables($reflector);

        return 0;
    }

    /**
     * Handle the manual update operation.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int 0 if everything went fine, or an exit code
     */
    private function handleUpdateManual(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->config) {
            $output->writeln('<error>Configuration not available for manual updates.</error>');

            return 1;
        }

        // Create a synthetic input with the update-manual option
        $definition = new InputDefinition([
            new InputOption('update-manual', null, InputOption::VALUE_OPTIONAL, '', false),
        ]);

        // Get the language value: if true (no value), use null to preserve current language
        $lang = $input->getOption('update-manual');
        $updateValue = ($lang === true) ? null : $lang;

        $updateInput = new ArrayInput(['--update-manual' => $updateValue], $definition);
        $updateInput->setInteractive($input->isInteractive());

        try {
            $manualUpdate = ManualUpdate::fromConfig($this->config, $updateInput, $output);
            $result = $manualUpdate->run($updateInput, $output);

            if ($result === 0) {
                $output->writeln('');
                $output->writeln('Restart PsySH to use the updated manual.');
            }

            return $result;
        } catch (\RuntimeException $e) {
            $output->writeln(\sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }
    }

    private function getManualDoc($reflector, ?OutputInterface $output = null)
    {
        switch (\get_class($reflector)) {
            case \ReflectionClass::class:
            case \ReflectionObject::class:
            case \ReflectionFunction::class:
                $id = $reflector->name;
                break;

            case \ReflectionMethod::class:
                $id = $reflector->class.'::'.$reflector->name;
                break;

            case \ReflectionProperty::class:
                $id = $reflector->class.'::$'.$reflector->name;
                break;

            case \ReflectionClassConstant::class:
                // @todo this is going to collide with ReflectionMethod ids
                // someday... start running the query by id + type if the DB
                // supports it.
                $id = $reflector->class.'::'.$reflector->name;
                break;

            case ReflectionConstant::class:
                $id = $reflector->name;
                break;

            default:
                return null;
        }

        return $this->getManualDocById($id, $output);
    }

    /**
     * Get all all parent Reflectors for a given Reflector.
     *
     * For example, passing a Class, Object or TraitReflector will yield all
     * traits and parent classes. Passing a Method or PropertyReflector will
     * yield Reflectors for the same-named method or property on all traits and
     * parent classes.
     *
     * @return \Generator a whole bunch of \Reflector instances
     */
    private function getParentReflectors($reflector): \Generator
    {
        $seenClasses = [];

        switch (\get_class($reflector)) {
            case \ReflectionClass::class:
            case \ReflectionObject::class:
                foreach ($reflector->getTraits() as $trait) {
                    if (!\in_array($trait->getName(), $seenClasses)) {
                        $seenClasses[] = $trait->getName();
                        yield $trait;
                    }
                }

                foreach ($reflector->getInterfaces() as $interface) {
                    if (!\in_array($interface->getName(), $seenClasses)) {
                        $seenClasses[] = $interface->getName();
                        yield $interface;
                    }
                }

                while ($reflector = $reflector->getParentClass()) {
                    yield $reflector;

                    foreach ($reflector->getTraits() as $trait) {
                        if (!\in_array($trait->getName(), $seenClasses)) {
                            $seenClasses[] = $trait->getName();
                            yield $trait;
                        }
                    }

                    foreach ($reflector->getInterfaces() as $interface) {
                        if (!\in_array($interface->getName(), $seenClasses)) {
                            $seenClasses[] = $interface->getName();
                            yield $interface;
                        }
                    }
                }

                return;

            case \ReflectionMethod::class:
                foreach ($this->getParentReflectors($reflector->getDeclaringClass()) as $parent) {
                    if ($parent->hasMethod($reflector->getName())) {
                        $parentMethod = $parent->getMethod($reflector->getName());
                        if (!\in_array($parentMethod->getDeclaringClass()->getName(), $seenClasses)) {
                            $seenClasses[] = $parentMethod->getDeclaringClass()->getName();
                            yield $parentMethod;
                        }
                    }
                }

                return;

            case \ReflectionProperty::class:
                foreach ($this->getParentReflectors($reflector->getDeclaringClass()) as $parent) {
                    if ($parent->hasProperty($reflector->getName())) {
                        $parentProperty = $parent->getProperty($reflector->getName());
                        if (!\in_array($parentProperty->getDeclaringClass()->getName(), $seenClasses)) {
                            $seenClasses[] = $parentProperty->getDeclaringClass()->getName();
                            yield $parentProperty;
                        }
                    }
                }
                break;
        }
    }

    private function getManualDocById($id, ?OutputInterface $output = null)
    {
        if ($manual = $this->getShell()->getManual()) {
            switch ($manual->getVersion()) {
                case 2:
                    // v2 manual docs are pre-formatted and should be rendered as-is
                    return $manual->get($id);

                case 3:
                    if ($doc = $manual->get($id)) {
                        $width = Tty::getWidth();
                        $formatter = new ManualFormatter($width, $manual, $output ? $output->getFormatter() : null);

                        return $formatter->format($doc);
                    }
                    break;
            }
        }

        return null;
    }

    private function writeManualPageSuggestions(OutputInterface $output, string $target): void
    {
        if (!$suggestions = $this->getManualPageSuggestions($target)) {
            return;
        }

        $output->writeln('');
        $output->writeln($this->formatManualPageSuggestions($suggestions));
    }

    private function writeManualPageDoc(ShellOutputAdapter $shellOutput, string $doc, string $target): void
    {
        $shellOutput->page(function (OutputInterface $pagedOutput) use ($doc, $target): void {
            $pagedOutput->writeln($doc);
            $this->writeManualPageSuggestions($pagedOutput, $target);
        });
    }

    private function tryWriteManualPageTarget(OutputInterface $output, ShellOutputAdapter $shellOutput, string $target, bool $allowCaseInsensitiveLookup = false): ?int
    {
        if ($doc = $this->getManualDocById($target, $output)) {
            $this->writeManualPageDoc($shellOutput, $doc, $target);

            return 0;
        }

        if (!$allowCaseInsensitiveLookup) {
            return null;
        }

        if (($manualPageId = $this->findManualPageId($target)) === null) {
            return null;
        }

        if ($doc = $this->getManualDocById($manualPageId, $output)) {
            $this->writeManualPageDoc($shellOutput, $doc, $manualPageId);

            return 0;
        }

        $this->writeManualPageLoadError($output, $manualPageId);

        return 1;
    }

    /**
     * @param string[] $suggestions
     */
    private function writeManualTargetSuggestions(OutputInterface $output, string $target, array $suggestions): void
    {
        $output->writeln($this->formatErrorLabel('Unknown target').' '.$target);
        $output->writeln('');
        $output->writeln('<comment>Did you mean?</comment>');
        foreach ($suggestions as $suggestion) {
            $output->writeln('  doc '.$suggestion);
        }
    }

    private function writeManualPageLoadError(OutputInterface $output, string $manualPageId): void
    {
        $output->writeln($this->formatErrorLabel('Manual page exists but could not be loaded').' '.$manualPageId);
    }

    private function formatErrorLabel(string $label): string
    {
        $indent = $this->config && $this->config->theme()->compact() ? '' : '  ';

        return \sprintf('%s<error> %s </error>', $indent, $label);
    }

    private function looksLikeManualPageName(string $target): bool
    {
        return \strpos($target, '.') !== false;
    }

    /**
     * @return string[]
     */
    private function getManualPageSuggestions(string $target, bool $broad = false): array
    {
        $target = \strtolower(\trim($target));
        if ($target === '') {
            return [];
        }

        $manualIds = $this->getManualIds();
        $suffixes = ['.'.$target];

        $suggestions = [];
        foreach ($manualIds as $id) {
            $normalizedId = \strtolower($id);
            if ($normalizedId === $target) {
                continue;
            }

            foreach ($suffixes as $suffix) {
                if (\substr_compare($normalizedId, $suffix, -\strlen($suffix)) === 0) {
                    $suggestions[$id] = true;
                    break;
                }
            }
        }

        $suggestions = \array_keys($suggestions);
        \sort($suggestions);

        if (!empty($suggestions)) {
            return \array_slice($suggestions, 0, 5);
        }

        if (!$broad) {
            return [];
        }

        return $this->getFuzzySuggestions($target, $manualIds);
    }

    /**
     * @return string[]
     */
    private function getDocTargetSuggestions(string $target): array
    {
        if ($suggestions = $this->getManualPageSuggestions($target, true)) {
            return $suggestions;
        }

        return $this->getFuzzyRuntimeTargetSuggestions($target);
    }

    /**
     * @return string[]
     */
    private function getFuzzyRuntimeTargetSuggestions(string $target): array
    {
        $target = \strtolower(\trim($target, " \t\n\r\0\x0B\\"));
        if ($target === '') {
            return [];
        }

        $candidates = \array_merge(
            $this->getRuntimeTargetCandidates(),
            $this->getLanguageConstructCandidates()
        );

        return $this->getFuzzySuggestions($target, $candidates, function ($candidate) {
            return \strtolower(\trim($candidate, '\\'));
        });
    }

    /**
     * @param string[] $candidates
     *
     * @return string[]
     */
    private function getFuzzySuggestions(string $target, array $candidates, ?callable $normalize = null): array
    {
        $normalize = $normalize ?? function ($candidate) {
            return \strtolower($candidate);
        };

        $maxDistance = $this->suggestionDistance($target);
        $suggestions = [];

        foreach ($candidates as $candidate) {
            $normalizedCandidate = $normalize($candidate);
            if ($normalizedCandidate === $target || \abs(\strlen($normalizedCandidate) - \strlen($target)) > $maxDistance) {
                continue;
            }

            $distance = \levenshtein($target, $normalizedCandidate);
            if ($distance <= $maxDistance) {
                $suggestions[] = [$distance, $candidate];
            }
        }

        \usort($suggestions, function ($left, $right) {
            return [$left[0], $left[1]] <=> [$right[0], $right[1]];
        });

        return \array_slice(\array_map(function ($suggestion) {
            return $suggestion[1];
        }, $suggestions), 0, 5);
    }

    /**
     * @return string[]
     */
    private function getRuntimeTargetCandidates(): array
    {
        $candidates = \array_merge(
            $this->symbolCatalog->getFunctions(),
            $this->symbolCatalog->getClasses(),
            $this->symbolCatalog->getInterfaces(),
            $this->symbolCatalog->getTraits(),
            $this->symbolCatalog->getConstants()
        );

        $candidates = \array_values(\array_unique($candidates));
        \sort($candidates);

        return $candidates;
    }

    /**
     * @return string[]
     */
    private function getLanguageConstructCandidates(): array
    {
        return ReflectionLanguageConstruct::getNames();
    }

    private function findManualPageId(string $target): ?string
    {
        $target = \strtolower(\trim($target));
        foreach ($this->getManualIds() as $id) {
            if (\strtolower($id) === $target) {
                return $id;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    private function getManualIds(): array
    {
        $manual = $this->getShell()->getManual();
        if (!$manual) {
            return [];
        }

        return $manual->getIds();
    }

    /**
     * @param string[] $suggestions
     */
    private function formatManualPageSuggestions(array $suggestions): string
    {
        return \sprintf('<comment>Related manual pages:</comment> %s', \implode(', ', $suggestions));
    }

    private function completionTarget(string $input): string
    {
        if (!\preg_match('/^\s*[^\s]+\s+(.*)$/s', $input, $matches)) {
            return '';
        }

        return $matches[1];
    }

    private function suggestionDistance(string $target): int
    {
        return \max(2, (int) \floor(\strlen($target) / 6));
    }
}
