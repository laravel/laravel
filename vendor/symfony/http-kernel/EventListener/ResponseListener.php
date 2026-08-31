<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ResponseListener fixes the Response headers based on the Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class ResponseListener implements EventSubscriberInterface
{
    public function __construct(
        private string $charset,
        private bool $addContentLanguageHeader = false,
    ) {
    }

    /**
     * Filters the Response.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();

        if (null === $response->getCharset()) {
            $response->setCharset($this->charset);
        }

        if ($this->addContentLanguageHeader && !$response->isInformational() && !$response->isEmpty() && !$response->headers->has('Content-Language')) {
            $response->headers->set('Content-Language', $event->getRequest()->getLocale());
        }

        if ($event->getRequest()->attributes->get('_vary_by_language')) {
            $response->setVary('Accept-Language', false);
        }

        $response->prepare($event->getRequest());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
