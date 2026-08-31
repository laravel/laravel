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

use Elastic\Elasticsearch\Response\Elasticsearch;
use Throwable;
use RuntimeException;
use Monolog\Level;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\ElasticsearchFormatter;
use InvalidArgumentException;
use Elasticsearch\Common\Exceptions\RuntimeException as ElasticsearchRuntimeException;
use Elasticsearch\Client;
use Monolog\LogRecord;
use Elastic\Elasticsearch\Exception\InvalidArgumentException as ElasticInvalidArgumentException;
use Elastic\Elasticsearch\Client as Client8;

/**
 * Elasticsearch handler
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/index.html
 *
 * Simple usage example:
 *
 *    $client = \Elasticsearch\ClientBuilder::create()
 *        ->setHosts($hosts)
 *        ->build();
 *
 *    $options = array(
 *        'index' => 'elastic_index_name',
 *        'type'  => 'elastic_doc_type',
 *    );
 *    $handler = new ElasticsearchHandler($client, $options);
 *    $log = new Logger('application');
 *    $log->pushHandler($handler);
 *
 * @author Avtandil Kikabidze <akalongman@gmail.com>
 * @phpstan-type Options array{
 *     index: string,
 *     type: string,
 *     ignore_error: bool,
 *     op_type: 'index'|'create'
 * }
 * @phpstan-type InputOptions array{
 *     index?: string,
 *     type?: string,
 *     ignore_error?: bool,
 *     op_type?: 'index'|'create'
 * }
 */
class ElasticsearchHandler extends AbstractProcessingHandler
{
    protected Client|Client8 $client;

    /**
     * @var mixed[] Handler config options
     * @phpstan-var Options
     */
    protected array $options;

    /**
     * @var bool
     */
    private $needsType;

    /**
     * @param Client|Client8 $client  Elasticsearch Client object
     * @param mixed[]        $options Handler configuration
     *
     * @phpstan-param InputOptions $options
     */
    public function __construct(Client|Client8 $client, array $options = [], int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->client = $client;
        $this->options = array_merge(
            [
                'index'        => 'monolog', // Elastic index name
                'type'         => '_doc',    // Elastic document type
                'ignore_error' => false,     // Suppress Elasticsearch exceptions
                'op_type'      => 'index',   // Elastic op_type (index or create) (https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-index_.html#docs-index-api-op_type)
            ],
            $options
        );

        if ($client instanceof Client8 || $client::VERSION[0] === '7') {
            $this->needsType = false;
            // force the type to _doc for ES8/ES7
            $this->options['type'] = '_doc';
        } else {
            $this->needsType = true;
        }
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $this->bulkSend([$record->formatted]);
    }

    /**
     * @inheritDoc
     */
    public function setFormatter(FormatterInterface $formatter): HandlerInterface
    {
        if ($formatter instanceof ElasticsearchFormatter) {
            return parent::setFormatter($formatter);
        }

        throw new InvalidArgumentException('ElasticsearchHandler is only compatible with ElasticsearchFormatter');
    }

    /**
     * Getter options
     *
     * @return mixed[]
     *
     * @phpstan-return Options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new ElasticsearchFormatter($this->options['index'], $this->options['type']);
    }

    /**
     * @inheritDoc
     */
    public function handleBatch(array $records): void
    {
        $documents = $this->getFormatter()->formatBatch($records);
        $this->bulkSend($documents);
    }

    /**
     * Use Elasticsearch bulk API to send list of documents
     *
     * @param  array<array<mixed>> $records Records + _index/_type keys
     * @throws \RuntimeException
     */
    protected function bulkSend(array $records): void
    {
        try {
            $params = [
                'body' => [],
            ];

            foreach ($records as $record) {
                $params['body'][] = [
                    $this->options['op_type'] => $this->needsType ? [
                        '_index' => $record['_index'],
                        '_type'  => $record['_type'],
                    ] : [
                        '_index' => $record['_index'],
                    ],
                ];
                unset($record['_index'], $record['_type']);

                $params['body'][] = $record;
            }

            /** @var Elasticsearch */
            $responses = $this->client->bulk($params);

            if ($responses['errors'] === true) {
                throw $this->createExceptionFromResponses($responses);
            }
        } catch (Throwable $e) {
            if (! $this->options['ignore_error']) {
                throw new RuntimeException('Error sending messages to Elasticsearch', 0, $e);
            }
        }
    }

    /**
     * Creates elasticsearch exception from responses array
     *
     * Only the first error is converted into an exception.
     *
     * @param mixed[]|Elasticsearch $responses returned by $this->client->bulk()
     */
    protected function createExceptionFromResponses($responses): Throwable
    {
        foreach ($responses['items'] ?? [] as $item) {
            if (isset($item['index']['error'])) {
                return $this->createExceptionFromError($item['index']['error']);
            }
        }

        if (class_exists(ElasticInvalidArgumentException::class)) {
            return new ElasticInvalidArgumentException('Elasticsearch failed to index one or more records.');
        }

        if (class_exists(ElasticsearchRuntimeException::class)) {
            return new ElasticsearchRuntimeException('Elasticsearch failed to index one or more records.');
        }

        throw new \LogicException('Unsupported elastic search client version');
    }

    /**
     * Creates elasticsearch exception from error array
     *
     * @param mixed[] $error
     */
    protected function createExceptionFromError(array $error): Throwable
    {
        $previous = isset($error['caused_by']) ? $this->createExceptionFromError($error['caused_by']) : null;

        if (class_exists(ElasticInvalidArgumentException::class)) {
            return new ElasticInvalidArgumentException($error['type'] . ': ' . $error['reason'], 0, $previous);
        }

        if (class_exists(ElasticsearchRuntimeException::class)) {
            return new ElasticsearchRuntimeException($error['type'].': '.$error['reason'], 0, $previous);
        }

        throw new \LogicException('Unsupported elastic search client version');
    }
}
