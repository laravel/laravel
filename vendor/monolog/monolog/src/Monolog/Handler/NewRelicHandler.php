<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Formatter\NormalizerFormatter;

/**
 * Class to record a log on a NewRelic application.
 * Enabling New Relic High Security mode may prevent capture of useful information.
 *
 * @see https://docs.newrelic.com/docs/agents/php-agent
 * @see https://docs.newrelic.com/docs/accounts-partnerships/accounts/security/high-security
 */
class NewRelicHandler extends AbstractProcessingHandler
{
    /**
     * Name of the New Relic application that will receive logs from this handler.
     *
     * @var string
     */
    protected $appName;

    /**
     * Name of the current transaction
     *
     * @var string
     */
    protected $transactionName;

    /**
     * Some context and extra data is passed into the handler as arrays of values. Do we send them as is
     * (useful if we are using the API), or explode them for display on the NewRelic RPM website?
     *
     * @var bool
     */
    protected $explodeArrays;

    /**
     * {@inheritDoc}
     *
     * @param string $appName
     * @param bool   $explodeArrays
     * @param string $transactionName
     */
    public function __construct(
        $level = Logger::ERROR,
        $bubble = true,
        $appName = null,
        $explodeArrays = false,
        $transactionName = null
    ) {
        parent::__construct($level, $bubble);

        $this->appName       = $appName;
        $this->explodeArrays = $explodeArrays;
        $this->transactionName = $transactionName;
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        if (!$this->isNewRelicEnabled()) {
            throw new MissingExtensionException('The newrelic PHP extension is required to use the NewRelicHandler');
        }

        if ($appName = $this->getAppName($record['context'])) {
            $this->setNewRelicAppName($appName);
        }

        if ($transactionName = $this->getTransactionName($record['context'])) {
            $this->setNewRelicTransactionName($transactionName);
            unset($record['formatted']['context']['transaction_name']);
        }

        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Exception) {
            newrelic_notice_error($record['message'], $record['context']['exception']);
            unset($record['formatted']['context']['exception']);
        } else {
            newrelic_notice_error($record['message']);
        }

        foreach ($record['formatted']['context'] as $key => $parameter) {
            if (is_array($parameter) && $this->explodeArrays) {
                foreach ($parameter as $paramKey => $paramValue) {
                    $this->setNewRelicParameter('context_' . $key . '_' . $paramKey, $paramValue);
                }
            } else {
                $this->setNewRelicParameter('context_' . $key, $parameter);
            }
        }

        foreach ($record['formatted']['extra'] as $key => $parameter) {
            if (is_array($parameter) && $this->explodeArrays) {
                foreach ($parameter as $paramKey => $paramValue) {
                    $this->setNewRelicParameter('extra_' . $key . '_' . $paramKey, $paramValue);
                }
            } else {
                $this->setNewRelicParameter('extra_' . $key, $parameter);
            }
        }
    }

    /**
     * Checks whether the NewRelic extension is enabled in the system.
     *
     * @return bool
     */
    protected function isNewRelicEnabled()
    {
        return extension_loaded('newrelic');
    }

    /**
     * Returns the appname where this log should be sent. Each log can override the default appname, set in this
     * handler's constructor, by providing the appname in it's context.
     *
     * @param  array       $context
     * @return null|string
     */
    protected function getAppName(array $context)
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
     * @param array $context
     *
     * @return null|string
     */
    protected function getTransactionName(array $context)
    {
        if (isset($context['transaction_name'])) {
            return $context['transaction_name'];
        }

        return $this->transactionName;
    }

    /**
     * Sets the NewRelic application that should receive this log.
     *
     * @param string $appName
     */
    protected function setNewRelicAppName($appName)
    {
        newrelic_set_appname($appName);
    }

    /**
     * Overwrites the name of the current transaction
     *
     * @param string $transactionName
     */
    protected function setNewRelicTransactionName($transactionName)
    {
        newrelic_name_transaction($transactionName);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    protected function setNewRelicParameter($key, $value)
    {
        if (null === $value || is_scalar($value)) {
            newrelic_add_custom_parameter($key, $value);
        } else {
            newrelic_add_custom_parameter($key, @json_encode($value));
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new NormalizerFormatter();
    }
}
