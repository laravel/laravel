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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/**
 * Generates a fragment URI.
 *
 * @author Kévin Dunglas <kevin@dunglas.fr>
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class FragmentUriGenerator implements FragmentUriGeneratorInterface
{
    public function __construct(
        private string $fragmentPath,
        private ?UriSigner $signer = null,
        private ?RequestStack $requestStack = null,
    ) {
    }

    public function generate(ControllerReference $controller, ?Request $request = null, bool $absolute = false, bool $strict = true, bool $sign = true): string
    {
        if (null === $request && (null === $this->requestStack || null === $request = $this->requestStack->getCurrentRequest())) {
            throw new \LogicException('Generating a fragment URL can only be done when handling a Request.');
        }

        if ($sign && null === $this->signer) {
            throw new \LogicException('You must use a URI when using the ESI rendering strategy or set a URL signer.');
        }

        if ($strict) {
            $this->checkNonScalar($controller->attributes);
        }

        // We need to forward the current _format and _locale values as we don't have
        // a proper routing pattern to do the job for us.
        // This makes things inconsistent if you switch from rendering a controller
        // to rendering a route if the route pattern does not contain the special
        // _format and _locale placeholders.
        if (!isset($controller->attributes['_format'])) {
            $controller->attributes['_format'] = $request->getRequestFormat();
        }
        if (!isset($controller->attributes['_locale'])) {
            $controller->attributes['_locale'] = $request->getLocale();
        }

        $controller->attributes['_controller'] = $controller->controller;
        $controller->query['_path'] = http_build_query($controller->attributes, '', '&');
        $path = $this->fragmentPath.'?'.http_build_query($controller->query, '', '&');

        // we need to sign the absolute URI, but want to return the path only.
        $fragmentUri = $sign || $absolute ? $request->getUriForPath($path) : $request->getBaseUrl().$path;

        if (!$sign) {
            return $fragmentUri;
        }

        $fragmentUri = $this->signer->sign($fragmentUri);

        return $absolute ? $fragmentUri : substr($fragmentUri, \strlen($request->getSchemeAndHttpHost()));
    }

    private function checkNonScalar(array $values): void
    {
        foreach ($values as $key => $value) {
            if (\is_array($value)) {
                $this->checkNonScalar($value);
            } elseif (!\is_scalar($value) && null !== $value) {
                throw new \LogicException(\sprintf('Controller attributes cannot contain non-scalar/non-null values (value for key "%s" is not a scalar or null).', $key));
            }
        }
    }
}
