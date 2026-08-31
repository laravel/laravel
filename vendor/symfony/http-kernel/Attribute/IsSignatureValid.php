<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Attribute;

/**
 * Validates the request signature for specific HTTP methods.
 *
 * This class determines whether a request's signature should be validated
 * based on the configured HTTP methods. If the request method matches one
 * of the specified methods (or if no methods are specified), the signature
 * is checked.
 *
 * If the signature is invalid, a {@see \Symfony\Component\HttpFoundation\Exception\SignedUriException}
 * is thrown during validation.
 *
 * @author Santiago San Martin <sanmartindev@gmail.com>
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
final class IsSignatureValid
{
    /** @var string[] */
    public readonly array $methods;

    /**
     * @param string[]|string $methods HTTP methods that require signature validation. An empty array means that no method filtering is done
     */
    public function __construct(
        array|string $methods = [],
    ) {
        if (\in_array('GET', $methods = array_map('strtoupper', (array) $methods), true)) {
            $methods[] = 'HEAD';
        }
        $this->methods = $methods;
    }
}
