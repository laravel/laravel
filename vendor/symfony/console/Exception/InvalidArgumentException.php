<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Exception;

/**
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @internal
     */
    public static function fromEnumValue(string $name, string $value, array|\Closure $suggestedValues): self
    {
        $error = \sprintf('The value "%s" is not valid for the "%s" argument.', $value, $name);

        if (\is_array($suggestedValues)) {
            $error .= \sprintf(' Supported values are "%s".', implode('", "', $suggestedValues));
        }

        return new self($error);
    }
}
