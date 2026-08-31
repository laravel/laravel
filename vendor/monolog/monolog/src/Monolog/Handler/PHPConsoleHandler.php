<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Level;
use Monolog\Utils;
use PhpConsole\Connector;
use PhpConsole\Handler as VendorPhpConsoleHandler;
use PhpConsole\Helper;
use Monolog\LogRecord;
use PhpConsole\Storage;

/**
 * Monolog handler for Google Chrome extension "PHP Console"
 *
 * Display PHP error/debug log messages in Google Chrome console and notification popups, executes PHP code remotely
 *
 * Usage:
 * 1. Install Google Chrome extension [now dead and removed from the chrome store]
 * 2. See overview https://github.com/barbushin/php-console#overview
 * 3. Install PHP Console library https://github.com/barbushin/php-console#installation
 * 4. Example (result will looks like http://i.hizliresim.com/vg3Pz4.png)
 *
 *      $logger = new \Monolog\Logger('all', array(new \Monolog\Handler\PHPConsoleHandler()));
 *      \Monolog\ErrorHandler::register($logger);
 *      echo $undefinedVar;
 *      $logger->debug('SELECT * FROM users', array('db', 'time' => 0.012));
 *      PC::debug($_SERVER); // PHP Console debugger for any type of vars
 *
 * @author Sergey Barbushin https://www.linkedin.com/in/barbushin
 * @phpstan-type Options array{
 *     enabled: bool,
 *     classesPartialsTraceIgnore: string[],
 *     debugTagsKeysInContext: array<int|string>,
 *     useOwnErrorsHandler: bool,
 *     useOwnExceptionsHandler: bool,
 *     sourcesBasePath: string|null,
 *     registerHelper: bool,
 *     serverEncoding: string|null,
 *     headersLimit: int|null,
 *     password: string|null,
 *     enableSslOnlyMode: bool,
 *     ipMasks: string[],
 *     enableEvalListener: bool,
 *     dumperDetectCallbacks: bool,
 *     dumperLevelLimit: int,
 *     dumperItemsCountLimit: int,
 *     dumperItemSizeLimit: int,
 *     dumperDumpSizeLimit: int,
 *     detectDumpTraceAndSource: bool,
 *     dataStorage: Storage|null
 * }
 * @phpstan-type InputOptions array{
 *     enabled?: bool,
 *     classesPartialsTraceIgnore?: string[],
 *     debugTagsKeysInContext?: array<int|string>,
 *     useOwnErrorsHandler?: bool,
 *     useOwnExceptionsHandler?: bool,
 *     sourcesBasePath?: string|null,
 *     registerHelper?: bool,
 *     serverEncoding?: string|null,
 *     headersLimit?: int|null,
 *     password?: string|null,
 *     enableSslOnlyMode?: bool,
 *     ipMasks?: string[],
 *     enableEvalListener?: bool,
 *     dumperDetectCallbacks?: bool,
 *     dumperLevelLimit?: int,
 *     dumperItemsCountLimit?: int,
 *     dumperItemSizeLimit?: int,
 *     dumperDumpSizeLimit?: int,
 *     detectDumpTraceAndSource?: bool,
 *     dataStorage?: Storage|null
 * }
 *
 * @deprecated Since 2.8.0 and 3.2.0, PHPConsole is abandoned and thus we will drop this handler in Monolog 4
 */
class PHPConsoleHandler extends AbstractProcessingHandler
{
    /**
     * @phpstan-var Options
     */
    private array $options = [
        'enabled' => true, // bool Is PHP Console server enabled
        'classesPartialsTraceIgnore' => ['Monolog\\'], // array Hide calls of classes started with...
        'debugTagsKeysInContext' => [0, 'tag'], // bool Is PHP Console server enabled
        'useOwnErrorsHandler' => false, // bool Enable errors handling
        'useOwnExceptionsHandler' => false, // bool Enable exceptions handling
        'sourcesBasePath' => null, // string Base path of all project sources to strip in errors source paths
        'registerHelper' => true, // bool Register PhpConsole\Helper that allows short debug calls like PC::debug($var, 'ta.g.s')
        'serverEncoding' => null, // string|null Server internal encoding
        'headersLimit' => null, // int|null Set headers size limit for your web-server
        'password' => null, // string|null Protect PHP Console connection by password
        'enableSslOnlyMode' => false, // bool Force connection by SSL for clients with PHP Console installed
        'ipMasks' => [], // array Set IP masks of clients that will be allowed to connect to PHP Console: array('192.168.*.*', '127.0.0.1')
        'enableEvalListener' => false, // bool Enable eval request to be handled by eval dispatcher(if enabled, 'password' option is also required)
        'dumperDetectCallbacks' => false, // bool Convert callback items in dumper vars to (callback SomeClass::someMethod) strings
        'dumperLevelLimit' => 5, // int Maximum dumped vars array or object nested dump level
        'dumperItemsCountLimit' => 100, // int Maximum dumped var same level array items or object properties number
        'dumperItemSizeLimit' => 5000, // int Maximum length of any string or dumped array item
        'dumperDumpSizeLimit' => 500000, // int Maximum approximate size of dumped vars result formatted in JSON
        'detectDumpTraceAndSource' => false, // bool Autodetect and append trace data to debug
        'dataStorage' => null, // \PhpConsole\Storage|null Fixes problem with custom $_SESSION handler (see https://github.com/barbushin/php-console#troubleshooting-with-_session-handler-overridden-in-some-frameworks)
    ];

    private Connector $connector;

    /**
     * @param  array<string, mixed> $options   See \Monolog\Handler\PHPConsoleHandler::$options for more details
     * @param  Connector|null       $connector Instance of \PhpConsole\Connector class (optional)
     * @throws \RuntimeException
     * @phpstan-param InputOptions $options
     */
    public function __construct(array $options = [], ?Connector $connector = null, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        if (!class_exists('PhpConsole\Connector')) {
            throw new \RuntimeException('PHP Console library not found. See https://github.com/barbushin/php-console#installation');
        }
        parent::__construct($level, $bubble);
        $this->options = $this->initOptions($options);
        $this->connector = $this->initConnector($connector);
    }

    /**
     * @param  array<string, mixed> $options
     * @return array<string, mixed>
     *
     * @phpstan-param InputOptions $options
     * @phpstan-return Options
     */
    private function initOptions(array $options): array
    {
        $wrongOptions = array_diff(array_keys($options), array_keys($this->options));
        if (\count($wrongOptions) > 0) {
            throw new \RuntimeException('Unknown options: ' . implode(', ', $wrongOptions));
        }

        return array_replace($this->options, $options);
    }

    private function initConnector(?Connector $connector = null): Connector
    {
        if (null === $connector) {
            if ($this->options['dataStorage'] instanceof Storage) {
                Connector::setPostponeStorage($this->options['dataStorage']);
            }
            $connector = Connector::getInstance();
        }

        if ($this->options['registerHelper'] && !Helper::isRegistered()) {
            Helper::register();
        }

        if ($this->options['enabled'] && $connector->isActiveClient()) {
            if ($this->options['useOwnErrorsHandler'] || $this->options['useOwnExceptionsHandler']) {
                $handler = VendorPhpConsoleHandler::getInstance();
                $handler->setHandleErrors($this->options['useOwnErrorsHandler']);
                $handler->setHandleExceptions($this->options['useOwnExceptionsHandler']);
                $handler->start();
            }
            if (null !== $this->options['sourcesBasePath']) {
                $connector->setSourcesBasePath($this->options['sourcesBasePath']);
            }
            if (null !== $this->options['serverEncoding']) {
                $connector->setServerEncoding($this->options['serverEncoding']);
            }
            if (null !== $this->options['password']) {
                $connector->setPassword($this->options['password']);
            }
            if ($this->options['enableSslOnlyMode']) {
                $connector->enableSslOnlyMode();
            }
            if (\count($this->options['ipMasks']) > 0) {
                $connector->setAllowedIpMasks($this->options['ipMasks']);
            }
            if (null !== $this->options['headersLimit'] && $this->options['headersLimit'] > 0) {
                $connector->setHeadersLimit($this->options['headersLimit']);
            }
            if ($this->options['detectDumpTraceAndSource']) {
                $connector->getDebugDispatcher()->detectTraceAndSource = true;
            }
            $dumper = $connector->getDumper();
            $dumper->levelLimit = $this->options['dumperLevelLimit'];
            $dumper->itemsCountLimit = $this->options['dumperItemsCountLimit'];
            $dumper->itemSizeLimit = $this->options['dumperItemSizeLimit'];
            $dumper->dumpSizeLimit = $this->options['dumperDumpSizeLimit'];
            $dumper->detectCallbacks = $this->options['dumperDetectCallbacks'];
            if ($this->options['enableEvalListener']) {
                $connector->startEvalRequestsListener();
            }
        }

        return $connector;
    }

    public function getConnector(): Connector
    {
        return $this->connector;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function handle(LogRecord $record): bool
    {
        if ($this->options['enabled'] && $this->connector->isActiveClient()) {
            return parent::handle($record);
        }

        return !$this->bubble;
    }

    /**
     * Writes the record down to the log of the implementing handler
     */
    protected function write(LogRecord $record): void
    {
        if ($record->level->isLowerThan(Level::Notice)) {
            $this->handleDebugRecord($record);
        } elseif (isset($record->context['exception']) && $record->context['exception'] instanceof \Throwable) {
            $this->handleExceptionRecord($record);
        } else {
            $this->handleErrorRecord($record);
        }
    }

    private function handleDebugRecord(LogRecord $record): void
    {
        [$tags, $filteredContext] = $this->getRecordTags($record);
        $message = $record->message;
        if (\count($filteredContext) > 0) {
            $message .= ' ' . Utils::jsonEncode($this->connector->getDumper()->dump(array_filter($filteredContext)), null, true);
        }
        $this->connector->getDebugDispatcher()->dispatchDebug($message, $tags, $this->options['classesPartialsTraceIgnore']);
    }

    private function handleExceptionRecord(LogRecord $record): void
    {
        $this->connector->getErrorsDispatcher()->dispatchException($record->context['exception']);
    }

    private function handleErrorRecord(LogRecord $record): void
    {
        $context = $record->context;

        $this->connector->getErrorsDispatcher()->dispatchError(
            $context['code'] ?? null,
            $context['message'] ?? $record->message,
            $context['file'] ?? null,
            $context['line'] ?? null,
            $this->options['classesPartialsTraceIgnore']
        );
    }

    /**
     * @return array{string, mixed[]}
     */
    private function getRecordTags(LogRecord $record): array
    {
        $tags = null;
        $filteredContext = [];
        if ($record->context !== []) {
            $filteredContext = $record->context;
            foreach ($this->options['debugTagsKeysInContext'] as $key) {
                if (isset($filteredContext[$key])) {
                    $tags = $filteredContext[$key];
                    if ($key === 0) {
                        array_shift($filteredContext);
                    } else {
                        unset($filteredContext[$key]);
                    }
                    break;
                }
            }
        }

        return [$tags ?? $record->level->toPsrLogLevel(), $filteredContext];
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter('%message%');
    }
}
