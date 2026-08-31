<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Transport;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Exception\HttpTransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Victor Bocharsky <victor@symfonycasts.com>
 */
abstract class AbstractHttpTransport extends AbstractTransport
{
    protected ?string $host = null;
    protected ?int $port = null;

    public function __construct(
        protected ?HttpClientInterface $client = null,
        ?EventDispatcherInterface $dispatcher = null,
        ?LoggerInterface $logger = null,
    ) {
        if (null === $client) {
            if (!class_exists(HttpClient::class)) {
                throw new \LogicException(\sprintf('You cannot use "%s" as the HttpClient component is not installed. Try running "composer require symfony/http-client".', __CLASS__));
            }

            $this->client = HttpClient::create();
        }

        parent::__construct($dispatcher, $logger);
    }

    /**
     * @return $this
     */
    public function setHost(?string $host): static
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return $this
     */
    public function setPort(?int $port): static
    {
        $this->port = $port;

        return $this;
    }

    abstract protected function doSendHttp(SentMessage $message): ResponseInterface;

    protected function doSend(SentMessage $message): void
    {
        try {
            $response = $this->doSendHttp($message);
            $message->appendDebug($response->getInfo('debug') ?? '');
        } catch (HttpTransportException $e) {
            $e->appendDebug($e->getResponse()->getInfo('debug') ?? '');

            throw $e;
        }
    }
}
