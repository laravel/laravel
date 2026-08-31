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
use Symfony\Component\Mime\Crypto\SMimeEncrypter;
use Symfony\Component\Mime\Message;

/**
 * Encrypts messages using S/MIME.
 *
 * @author Elías Fernández
 */
final class SmimeEncryptedMessageListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly SmimeCertificateRepositoryInterface $smimeRepository,
        private readonly ?int $cipher = null,
    ) {
    }

    public function onMessage(MessageEvent $event): void
    {
        $message = $event->getMessage();
        if (!$message instanceof Message) {
            return;
        }
        if (!$message->getHeaders()->has('X-SMime-Encrypt')) {
            return;
        }
        $message->getHeaders()->remove('X-SMime-Encrypt');
        $certificatePaths = [];
        foreach ($event->getEnvelope()->getRecipients() as $recipient) {
            $certificatePath = $this->smimeRepository->findCertificatePathFor($recipient->getAddress());
            if (null === $certificatePath) {
                return;
            }
            $certificatePaths[] = $certificatePath;
        }
        if (0 === \count($certificatePaths)) {
            return;
        }
        $encrypter = new SMimeEncrypter($certificatePaths, $this->cipher);

        $event->setMessage($encrypter->encrypt($message));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => ['onMessage', -128],
        ];
    }
}
