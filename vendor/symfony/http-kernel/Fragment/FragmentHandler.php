<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Fragment;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Renders a URI that represents a resource fragment.
 *
 * This class handles the rendering of resource fragments that are included into
 * a main resource. The handling of the rendering is managed by specialized renderers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @see FragmentRendererInterface
 */
class FragmentHandler
{
    /** @var array<string, FragmentRendererInterface> */
    private array $renderers = [];

    /**
     * @param FragmentRendererInterface[] $renderers An array of FragmentRendererInterface instances
     * @param bool                        $debug     Whether the debug mode is enabled or not
     */
    public function __construct(
        private RequestStack $requestStack,
        array $renderers = [],
        private bool $debug = false,
    ) {
        foreach ($renderers as $renderer) {
            $this->addRenderer($renderer);
        }
    }

    /**
     * Adds a renderer.
     */
    public function addRenderer(FragmentRendererInterface $renderer): void
    {
        $this->renderers[$renderer->getName()] = $renderer;
    }

    /**
     * Renders a URI and returns the Response content.
     *
     * Available options:
     *
     *  * ignore_errors: true to return an empty string in case of an error
     *
     * @throws \InvalidArgumentException when the renderer does not exist
     * @throws \LogicException           when no main request is being handled
     */
    public function render(string|ControllerReference $uri, string $renderer = 'inline', array $options = []): ?string
    {
        if (!isset($options['ignore_errors'])) {
            $options['ignore_errors'] = !$this->debug;
        }

        if (!isset($this->renderers[$renderer])) {
            throw new \InvalidArgumentException(\sprintf('The "%s" renderer does not exist.', $renderer));
        }

        if (!$request = $this->requestStack->getCurrentRequest()) {
            throw new \LogicException('Rendering a fragment can only be done when handling a Request.');
        }

        return $this->deliver($this->renderers[$renderer]->render($uri, $request, $options));
    }

    /**
     * Delivers the Response as a string.
     *
     * When the Response is a StreamedResponse, the content is streamed immediately
     * instead of being returned.
     *
     * @return string|null The Response content or null when the Response is streamed
     *
     * @throws \RuntimeException when the Response is not successful
     */
    protected function deliver(Response $response): ?string
    {
        if (!$response->isSuccessful()) {
            $responseStatusCode = $response->getStatusCode();
            throw new \RuntimeException(\sprintf('Error when rendering "%s" (Status code is %d).', $this->requestStack->getCurrentRequest()->getUri(), $responseStatusCode), 0, new HttpException($responseStatusCode));
        }

        if (!$response instanceof StreamedResponse) {
            return $response->getContent();
        }

        $response->sendContent();

        return null;
    }
}
