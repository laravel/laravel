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

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\JsonFormatter;
use Monolog\Level;
use Monolog\LogRecord;

/**
 * CouchDB handler
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 * @phpstan-type Options array{
 *     host: string,
 *     port: int,
 *     dbname: string,
 *     username: string|null,
 *     password: string|null
 * }
 * @phpstan-type InputOptions array{
 *     host?: string,
 *     port?: int,
 *     dbname?: string,
 *     username?: string|null,
 *     password?: string|null
 * }
 */
class CouchDBHandler extends AbstractProcessingHandler
{
    /**
     * @var mixed[]
     * @phpstan-var Options
     */
    private array $options;

    /**
     * @param mixed[] $options
     *
     * @phpstan-param InputOptions $options
     */
    public function __construct(array $options = [], int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        $this->options = array_merge([
            'host'     => 'localhost',
            'port'     => 5984,
            'dbname'   => 'logger',
            'username' => null,
            'password' => null,
        ], $options);

        parent::__construct($level, $bubble);
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $basicAuth = null;
        if (null !== $this->options['username'] && null !== $this->options['password']) {
            $basicAuth = sprintf('%s:%s@', $this->options['username'], $this->options['password']);
        }

        $url = 'http://'.$basicAuth.$this->options['host'].':'.$this->options['port'].'/'.$this->options['dbname'];
        $context = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'content'       => $record->formatted,
                'ignore_errors' => true,
                'max_redirects' => 0,
                'header'        => 'Content-type: application/json',
            ],
        ]);

        if (false === @file_get_contents($url, false, $context)) {
            throw new \RuntimeException(sprintf('Could not connect to %s', $url));
        }
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new JsonFormatter(JsonFormatter::BATCH_MODE_JSON, false);
    }
}
