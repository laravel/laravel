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

use Monolog\Level;
use Monolog\Utils;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

/**
 * Class to record a log on a NewRelic application.
 * Enabling New Relic High Security mode may prevent capture of useful information.
 *
 * This handler requires a NormalizerFormatter to function and expects an array in $record->formatted
 *
 * @see https://docs.newrelic.com/docs/agents/php-agent
 * @see https://docs.newrelic.com/docs/accounts-partnerships/accounts/security/high-security
 */
class NewRelicHandler extends AbstractProcessingHandler
{
    /**
     * @inheritDoc
     */
    public function __construct(
        int|string|Level $level = Level::Error,
        bool $bubble = true,

        /**
         * Name of the New Relic application that will receive logs from this handler.
         */
        protected string|null $appName = null,

        /**
         * Some context and extra data is passed into the handler as arrays of values. Do we send them as is
         * (useful if we are using the API), or explode them for display on the NewRelic RPM website?
         */
        protected bool $explodeArrays = false,

        /**
         * Name of the current transaction
         */
        protected string|null $transactionName = null
    ) {
        parent::__construct($level, $bubble);
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        if (!$this->isNewRelicEnabled()) {
            throw new MissingExtensionException('The newrelic PHP extension is required to use the NewRelicHandler');
        }

        if (null !== ($appName = $this->getAppName($record->context))) {
            $this->setNewRelicAppName($appName);
        }

        if (null !== ($transactionName = $this->getTransactionName($record->context))) {
            $this->setNewRelicTransactionName($transactionName);
            unset($record->formatted['context']['transaction_name']);
        }

        if (isset($record->context['exception']) && $record->context['exception'] instanceof \Throwable) {
            newrelic_notice_error($record->message, $record->context['exception']);
            unset($record->formatted['context']['exception']);
        } else {
            newrelic_notice_error($record->message);
        }

        if (isset($record->formatted['context']) && \is_array($record->formatted['context'])) {
            foreach ($record->formatted['context'] as $key => $parameter) {
                if (\is_array($parameter) && $this->explodeArrays) {
                    foreach ($parameter as $paramKey => $paramValue) {
                        $this->setNewRelicParameter('context_' . $key . '_' . $paramKey, $paramValue);
                    }
                } else {
                    $this->setNewRelicParameter('context_' . $key, $parameter);
                }
            }
        }

        if (isset($record->formatted['extra']) && \is_array($record->formatted['extra'])) {
            foreach ($record->formatted['extra'] as $key => $parameter) {
                if (\is_array($parameter) && $this->explodeArrays) {
                    foreach ($parameter as $paramKey => $paramValue) {
                        $this->setNewRelicParameter('extra_' . $key . '_' . $paramKey, $paramValue);
                    }
                } else {
                    $this->setNewRelicParameter('extra_' . $key, $parameter);
                }
            }
        }
    }

    /**
     * Checks whether the NewRelic extension is enabled in the system.
     */
    protected function isNewRelicEnabled(): bool
    {
        return \extension_loaded('newrelic');
    }

    /**
     * Returns the appname where this log should be sent. Each log can override the default appname, set in this
     * handler's constructor, by providing the appname in it's context.
     *
     * @param mixed[] $context
     */
    protected function getAppName(array $context): ?string
    {
        if (isset($context['appname'])) {
            return $context['appname'];
        }

        return $this->appName;
    }

    /**
     * Returns the name of the current transaction. Each log can override the default transaction name, set in this
     * handler's constructor, by providing the transaction_name in it's context
     *
     * @param mixed[] $context
     */
    protected function getTransactionName(array $context): ?string
    {
        if (isset($context['transaction_name'])) {
            return $context['transaction_name'];
        }

        return $this->transactionName;
    }

    /**
     * Sets the NewRelic application that should receive this log.
     */
    protected function setNewRelicAppName(string $appName): void
    {
        newrelic_set_appname($appName);
    }

    /**
     * Overwrites the name of the current transaction
     */
    protected function setNewRelicTransactionName(string $transactionName): void
    {
        newrelic_name_transaction($transactionName);
    }

    /**
     * @param mixed $value
     */
    protected function setNewRelicParameter(string $key, $value): void
    {
        if (null === $value || \is_scalar($value)) {
            newrelic_add_custom_parameter($key, $value);
        } else {
            newrelic_add_custom_parameter($key, Utils::jsonEncode($value, null, true));
        }
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new NormalizerFormatter();
    }
}
