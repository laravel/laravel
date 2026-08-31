<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Mailer\Exception\RuntimeException;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Header\MailboxListHeader;
use Symfony\Component\Mime\Message;

/**
 * Manipulates the headers and the body of a Message.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MessageListener implements EventSubscriberInterface
{
    public const HEADER_SET_IF_EMPTY = 1;
    public const HEADER_ADD = 2;
    public const HEADER_REPLACE = 3;
    public const DEFAULT_RULES = [
        'from' => self::HEADER_SET_IF_EMPTY,
        'return-path' => self::HEADER_SET_IF_EMPTY,
        'reply-to' => self::HEADER_ADD,
        'to' => self::HEADER_SET_IF_EMPTY,
        'cc' => self::HEADER_ADD,
        'bcc' => self::HEADER_ADD,
    ];

    private array $headerRules = [];

    public function __construct(
        private ?Headers $headers = null,
        private ?BodyRendererInterface $renderer = null,
        array $headerRules = self::DEFAULT_RULES,
    ) {
        foreach ($headerRules as $headerName => $rule) {
            $this->addHeaderRule($headerName, $rule);
        }
    }

    public function addHeaderRule(string $headerName, int $rule): void
    {
        if ($rule < 1 || $rule > 3) {
            throw new InvalidArgumentException(\sprintf('The "%d" rule is not supported.', $rule));
        }

        $this->headerRules[strtolower($headerName)] = $rule;
    }

    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if (!$message instanceof Message) {
            return;
        }

        $this->setHeaders($message);
        $this->renderMessage($message);
    }

    private function setHeaders(Message $message): void
    {
        if (!$this->headers) {
            return;
        }

        $headers = $message->getHeaders();
        foreach ($this->headers->all() as $name => $header) {
            if (!$headers->has($name)) {
                $headers->add($header);

                continue;
            }

            switch ($this->headerRules[$name] ?? self::HEADER_SET_IF_EMPTY) {
                case self::HEADER_SET_IF_EMPTY:
                    break;

                case self::HEADER_REPLACE:
                    $headers->remove($name);
                    $headers->add($header);

                    break;

                case self::HEADER_ADD:
                    if (!Headers::isUniqueHeader($name)) {
                        $headers->add($header);

                        break;
                    }

                    $h = $headers->get($name);
                    if (!$h instanceof MailboxListHeader) {
                        throw new RuntimeException(\sprintf('Unable to set header "%s".', $name));
                    }

                    Headers::checkHeaderClass($header);
                    foreach ($header->getAddresses() as $address) {
                        $h->addAddress($address);
                    }
            }
        }
    }

    private function renderMessage(Message $message): void
    {
        if (!$this->renderer) {
            return;
        }

        $this->renderer->render($message);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }
}
