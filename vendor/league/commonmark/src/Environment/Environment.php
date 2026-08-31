<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Environment;

use League\CommonMark\Delimiter\DelimiterParser;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorCollection;
use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Event\ListenerData;
use League\CommonMark\Exception\AlreadyInitializedException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Normalizer\SlugNormalizer;
use League\CommonMark\Normalizer\TextNormalizerInterface;
use League\CommonMark\Normalizer\UniqueSlugNormalizer;
use League\CommonMark\Normalizer\UniqueSlugNormalizerInterface;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Block\SkipLinesStartingWithLettersParser;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlFilter;
use League\CommonMark\Util\PrioritizedList;
use League\Config\Configuration;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;
use Nette\Schema\Expect;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

final class Environment implements EnvironmentInterface, EnvironmentBuilderInterface, ListenerProviderInterface
{
    /**
     * @var ExtensionInterface[]
     *
     * @psalm-readonly-allow-private-mutation
     */
    private array $extensions = [];

    /**
     * @var ExtensionInterface[]
     *
     * @psalm-readonly-allow-private-mutation
     */
    private array $uninitializedExtensions = [];

    /** @psalm-readonly-allow-private-mutation */
    private bool $extensionsInitialized = false;

    /**
     * @var PrioritizedList<BlockStartParserInterface>
     *
     * @psalm-readonly
     */
    private PrioritizedList $blockStartParsers;

    /**
     * @var PrioritizedList<InlineParserInterface>
     *
     * @psalm-readonly
     */
    private PrioritizedList $inlineParsers;

    /** @psalm-readonly */
    private DelimiterProcessorCollection $delimiterProcessors;

    /**
     * @var array<string, PrioritizedList<NodeRendererInterface>>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private array $renderersByClass = [];

    /**
     * @var PrioritizedList<ListenerData>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private PrioritizedList $listenerData;

    private ?EventDispatcherInterface $eventDispatcher = null;

    /** @psalm-readonly */
    private Configuration $config;

    private ?TextNormalizerInterface $slugNormalizer = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = self::createDefaultConfiguration();
        $this->config->merge($config);

        $this->blockStartParsers   = new PrioritizedList();
        $this->inlineParsers       = new PrioritizedList();
        $this->listenerData        = new PrioritizedList();
        $this->delimiterProcessors = new DelimiterProcessorCollection();

        // Performance optimization: always include a block "parser" that aborts parsing if a line starts with a letter
        // and is therefore unlikely to match any lines as a block start.
        $this->addBlockStartParser(new SkipLinesStartingWithLettersParser(), 249);
    }

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->config->reader();
    }

    /**
     * @deprecated Environment::mergeConfig() is deprecated since league/commonmark v2.0 and will be removed in v3.0. Configuration should be set when instantiating the environment instead.
     *
     * @param array<string, mixed> $config
     */
    public function mergeConfig(array $config): void
    {
        @\trigger_error('Environment::mergeConfig() is deprecated since league/commonmark v2.0 and will be removed in v3.0. Configuration should be set when instantiating the environment instead.', \E_USER_DEPRECATED);

        $this->assertUninitialized('Failed to modify configuration.');

        $this->config->merge($config);
    }

    public function addBlockStartParser(BlockStartParserInterface $parser, int $priority = 0): EnvironmentBuilderInterface
    {
        $this->assertUninitialized('Failed to add block start parser.');

        $this->blockStartParsers->add($parser, $priority);
        $this->injectEnvironmentAndConfigurationIfNeeded($parser);

        return $this;
    }

    public function addInlineParser(InlineParserInterface $parser, int $priority = 0): EnvironmentBuilderInterface
    {
        $this->assertUninitialized('Failed to add inline parser.');

        $this->inlineParsers->add($parser, $priority);
        $this->injectEnvironmentAndConfigurationIfNeeded($parser);

        return $this;
    }

    public function addDelimiterProcessor(DelimiterProcessorInterface $processor): EnvironmentBuilderInterface
    {
        $this->assertUninitialized('Failed to add delimiter processor.');
        $this->delimiterProcessors->add($processor);
        $this->injectEnvironmentAndConfigurationIfNeeded($processor);

        return $this;
    }

    public function addRenderer(string $nodeClass, NodeRendererInterface $renderer, int $priority = 0): EnvironmentBuilderInterface
    {
        $this->assertUninitialized('Failed to add renderer.');

        if (! isset($this->renderersByClass[$nodeClass])) {
            $this->renderersByClass[$nodeClass] = new PrioritizedList();
        }

        $this->renderersByClass[$nodeClass]->add($renderer, $priority);
        $this->injectEnvironmentAndConfigurationIfNeeded($renderer);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockStartParsers(): iterable
    {
        if (! $this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return $this->blockStartParsers->getIterator();
    }

    public function getDelimiterProcessors(): DelimiterProcessorCollection
    {
        if (! $this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return $this->delimiterProcessors;
    }

    /**
     * {@inheritDoc}
     */
    public function getRenderersForClass(string $nodeClass): iterable
    {
        if (! $this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        // If renderers are defined for this specific class, return them immediately
        if (isset($this->renderersByClass[$nodeClass])) {
            return $this->renderersByClass[$nodeClass];
        }

        /** @psalm-suppress TypeDoesNotContainType -- Bug: https://github.com/vimeo/psalm/issues/3332 */
        while (\class_exists($parent ??= $nodeClass) && $parent = \get_parent_class($parent)) {
            if (! isset($this->renderersByClass[$parent])) {
                continue;
            }

            // "Cache" this result to avoid future loops
            return $this->renderersByClass[$nodeClass] = $this->renderersByClass[$parent];
        }

        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getExtensions(): iterable
    {
        return $this->extensions;
    }

    /**
     * Add a single extension
     *
     * @return $this
     */
    public function addExtension(ExtensionInterface $extension): EnvironmentBuilderInterface
    {
        $this->assertUninitialized('Failed to add extension.');

        $this->extensions[]              = $extension;
        $this->uninitializedExtensions[] = $extension;

        if ($extension instanceof ConfigurableExtensionInterface) {
            $extension->configureSchema($this->config);
        }

        return $this;
    }

    private function initializeExtensions(): void
    {
        // Initialize the slug normalizer
        $this->getSlugNormalizer();

        // Ask all extensions to register their components
        while (\count($this->uninitializedExtensions) > 0) {
            foreach ($this->uninitializedExtensions as $i => $extension) {
                $extension->register($this);
                unset($this->uninitializedExtensions[$i]);
            }
        }

        $this->extensionsInitialized = true;

        // Create the special delimiter parser if any processors were registered
        if ($this->delimiterProcessors->count() > 0) {
            $this->inlineParsers->add(new DelimiterParser($this->delimiterProcessors), PHP_INT_MIN);
        }
    }

    private function injectEnvironmentAndConfigurationIfNeeded(object $object): void
    {
        if ($object instanceof EnvironmentAwareInterface) {
            $object->setEnvironment($this);
        }

        if ($object instanceof ConfigurationAwareInterface) {
            $object->setConfiguration($this->config->reader());
        }
    }

    /**
     * @deprecated Instantiate the environment and add the extension yourself
     *
     * @param array<string, mixed> $config
     */
    public static function createCommonMarkEnvironment(array $config = []): Environment
    {
        $environment = new self($config);
        $environment->addExtension(new CommonMarkCoreExtension());

        return $environment;
    }

    /**
     * @deprecated Instantiate the environment and add the extension yourself
     *
     * @param array<string, mixed> $config
     */
    public static function createGFMEnvironment(array $config = []): Environment
    {
        $environment = new self($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        return $environment;
    }

    public function addEventListener(string $eventClass, callable $listener, int $priority = 0): EnvironmentBuilderInterface
    {
        $this->assertUninitialized('Failed to add event listener.');

        $this->listenerData->add(new ListenerData($eventClass, $listener), $priority);

        if (\is_object($listener)) {
            $this->injectEnvironmentAndConfigurationIfNeeded($listener);
        } elseif (\is_array($listener) && \is_object($listener[0])) {
            $this->injectEnvironmentAndConfigurationIfNeeded($listener[0]);
        }

        return $this;
    }

    public function dispatch(object $event): object
    {
        if (! $this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        if ($this->eventDispatcher !== null) {
            return $this->eventDispatcher->dispatch($event);
        }

        foreach ($this->getListenersForEvent($event) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }

            $listener($event);
        }

        return $event;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable<callable>
     */
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->listenerData as $listenerData) {
            \assert($listenerData instanceof ListenerData);

            /** @psalm-suppress ArgumentTypeCoercion */
            if (! \is_a($event, $listenerData->getEvent())) {
                continue;
            }

            yield function (object $event) use ($listenerData) {
                if (! $this->extensionsInitialized) {
                    $this->initializeExtensions();
                }

                return \call_user_func($listenerData->getListener(), $event);
            };
        }
    }

    /**
     * @return iterable<InlineParserInterface>
     */
    public function getInlineParsers(): iterable
    {
        if (! $this->extensionsInitialized) {
            $this->initializeExtensions();
        }

        return $this->inlineParsers->getIterator();
    }

    public function getSlugNormalizer(): TextNormalizerInterface
    {
        if ($this->slugNormalizer === null) {
            $normalizer = $this->config->get('slug_normalizer/instance');
            \assert($normalizer instanceof TextNormalizerInterface);
            $this->injectEnvironmentAndConfigurationIfNeeded($normalizer);

            if ($this->config->get('slug_normalizer/unique') !== UniqueSlugNormalizerInterface::DISABLED && ! $normalizer instanceof UniqueSlugNormalizer) {
                $normalizer = new UniqueSlugNormalizer($normalizer);
            }

            if ($normalizer instanceof UniqueSlugNormalizer) {
                if ($this->config->get('slug_normalizer/unique') === UniqueSlugNormalizerInterface::PER_DOCUMENT) {
                    $this->addEventListener(DocumentParsedEvent::class, [$normalizer, 'clearHistory'], -1000);
                }
            }

            $this->slugNormalizer = $normalizer;
        }

        return $this->slugNormalizer;
    }

    /**
     * @throws AlreadyInitializedException
     */
    private function assertUninitialized(string $message): void
    {
        if ($this->extensionsInitialized) {
            throw new AlreadyInitializedException($message . ' Extensions have already been initialized.');
        }
    }

    public static function createDefaultConfiguration(): Configuration
    {
        return new Configuration([
            'html_input' => Expect::anyOf(HtmlFilter::STRIP, HtmlFilter::ALLOW, HtmlFilter::ESCAPE)->default(HtmlFilter::ALLOW),
            'allow_unsafe_links' => Expect::bool(true),
            'max_nesting_level' => Expect::type('int')->default(PHP_INT_MAX),
            'max_delimiters_per_line' => Expect::type('int')->default(PHP_INT_MAX),
            'renderer' => Expect::structure([
                'block_separator' => Expect::string("\n"),
                'inner_separator' => Expect::string("\n"),
                'soft_break' => Expect::string("\n"),
            ]),
            'slug_normalizer' => Expect::structure([
                'instance' => Expect::type(TextNormalizerInterface::class)->default(new SlugNormalizer()),
                'max_length' => Expect::int()->min(0)->default(255),
                'unique' => Expect::anyOf(UniqueSlugNormalizerInterface::DISABLED, UniqueSlugNormalizerInterface::PER_ENVIRONMENT, UniqueSlugNormalizerInterface::PER_DOCUMENT)->default(UniqueSlugNormalizerInterface::PER_DOCUMENT),
            ]),
        ]);
    }
}
