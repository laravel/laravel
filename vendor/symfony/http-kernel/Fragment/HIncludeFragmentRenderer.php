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
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Twig\Environment;

/**
 * Implements the Hinclude rendering strategy.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HIncludeFragmentRenderer extends RoutableFragmentRenderer
{
    /**
     * @param string|null $globalDefaultTemplate The global default content (it can be a template name or the content)
     */
    public function __construct(
        private ?Environment $twig = null,
        private ?UriSigner $signer = null,
        private ?string $globalDefaultTemplate = null,
        private string $charset = 'utf-8',
    ) {
    }

    /**
     * Checks if a templating engine has been set.
     */
    public function hasTemplating(): bool
    {
        return null !== $this->twig;
    }

    /**
     * Additional available options:
     *
     *  * default:    The default content (it can be a template name or the content)
     *  * id:         An optional hx:include tag id attribute
     *  * attributes: An optional array of hx:include tag attributes
     */
    public function render(string|ControllerReference $uri, Request $request, array $options = []): Response
    {
        if ($uri instanceof ControllerReference) {
            $uri = (new FragmentUriGenerator($this->fragmentPath, $this->signer))->generate($uri, $request);
        }

        // We need to replace ampersands in the URI with the encoded form in order to return valid html/xml content.
        $uri = str_replace('&', '&amp;', $uri);

        $template = $options['default'] ?? $this->globalDefaultTemplate;
        if (null !== $this->twig && $template && $this->twig->getLoader()->exists($template)) {
            $content = $this->twig->render($template);
        } else {
            $content = $template;
        }

        $attributes = isset($options['attributes']) && \is_array($options['attributes']) ? $options['attributes'] : [];
        if (isset($options['id']) && $options['id']) {
            $attributes['id'] = $options['id'];
        }
        $renderedAttributes = '';
        if (\count($attributes) > 0) {
            $flags = \ENT_QUOTES | \ENT_SUBSTITUTE;
            foreach ($attributes as $attribute => $value) {
                $renderedAttributes .= \sprintf(
                    ' %s="%s"',
                    htmlspecialchars($attribute, $flags, $this->charset, false),
                    htmlspecialchars($value, $flags, $this->charset, false)
                );
            }
        }

        return new Response(\sprintf('<hx:include src="%s"%s>%s</hx:include>', $uri, $renderedAttributes, $content));
    }

    public function getName(): string
    {
        return 'hinclude';
    }
}
