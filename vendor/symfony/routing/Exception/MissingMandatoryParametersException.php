<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Exception;

/**
 * Exception thrown when a route cannot be generated because of missing
 * mandatory parameters.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class MissingMandatoryParametersException extends \InvalidArgumentException implements ExceptionInterface
{
    private string $routeName = '';
    private array $missingParameters = [];

    /**
     * @param string[] $missingParameters
     */
    public function __construct(string $routeName = '', array $missingParameters = [], int $code = 0, ?\Throwable $previous = null)
    {
        $this->routeName = $routeName;
        $this->missingParameters = $missingParameters;
        $message = \sprintf('Some mandatory parameters are missing ("%s") to generate a URL for route "%s".', implode('", "', $missingParameters), $routeName);

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string[]
     */
    public function getMissingParameters(): array
    {
        return $this->missingParameters;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }
}
