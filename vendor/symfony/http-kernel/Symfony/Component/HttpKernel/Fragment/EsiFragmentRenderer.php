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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Symfony\Component\HttpKernel\UriSigner;

/**
 * Implements the ESI rendering strategy.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class EsiFragmentRenderer extends RoutableFragmentRenderer
{
    private $esi;
    private $inlineStrategy;
    private $signer;

    /**
     * Constructor.
     *
     * The "fallback" strategy when ESI is not available should always be an
     * instance of InlineFragmentRenderer.
     *
     * @param Esi                       $esi            An Esi instance
     * @param FragmentRendererInterface $inlineStrategy The inline strategy to use when ESI is not supported
     * @param UriSigner                 $signer
     */
    public function __construct(Esi $esi = null, InlineFragmentRenderer $inlineStrategy, UriSigner $signer = null)
    {
        $this->esi = $esi;
        $this->inlineStrategy = $inlineStrategy;
        $this->signer = $signer;
    }

    /**
     * {@inheritdoc}
     *
     * Note that if the current Request has no ESI capability, this method
     * falls back to use the inline rendering strategy.
     *
     * Additional available options:
     *
     *  * alt: an alternative URI to render in case of an error
     *  * comment: a comment to add when returning an esi:include tag
     *
     * @see Symfony\Component\HttpKernel\HttpCache\ESI
     */
    public function render($uri, Request $request, array $options = array())
    {
        if (!$this->esi || !$this->esi->hasSurrogateEsiCapability($request)) {
            return $this->inlineStrategy->render($uri, $request, $options);
        }

        if ($uri instanceof ControllerReference) {
            $uri = $this->generateSignedFragmentUri($uri, $request);
        }

        $alt = isset($options['alt']) ? $options['alt'] : null;
        if ($alt instanceof ControllerReference) {
            $alt = $this->generateSignedFragmentUri($alt, $request);
        }

        $tag = $this->esi->renderIncludeTag($uri, $alt, isset($options['ignore_errors']) ? $options['ignore_errors'] : false, isset($options['comment']) ? $options['comment'] : '');

        return new Response($tag);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'esi';
    }

    private function generateSignedFragmentUri($uri, Request $request)
    {
        if (null === $this->signer) {
            throw new \LogicException('You must use a URI when using the ESI rendering strategy or set a URL signer.');
        }

        // we need to sign the absolute URI, but want to return the path only.
        $fragmentUri = $this->signer->sign($this->generateFragmentUri($uri, $request, true));

        return substr($fragmentUri, strlen($request->getSchemeAndHttpHost()));
    }
}
