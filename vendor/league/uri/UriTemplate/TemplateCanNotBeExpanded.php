<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\UriTemplate;

use InvalidArgumentException;
use League\Uri\Contracts\UriException;

class TemplateCanNotBeExpanded extends InvalidArgumentException implements UriException
{
    public readonly array $variablesNames;

    public function __construct(string $message = '', string ...$variableNames)
    {
        parent::__construct($message, 0, null);

        $this->variablesNames = $variableNames;
    }

    public static function dueToUnableToProcessValueListWithPrefix(string $variableName): self
    {
        return new self('The ":" modifier cannot be applied on "'.$variableName.'" since it is a list of values.', $variableName);
    }

    public static function dueToNestedListOfValue(string $variableName): self
    {
        return new self('The "'.$variableName.'" cannot be a nested list.', $variableName);
    }

    public static function dueToMissingVariables(string ...$variableNames): self
    {
        return new self('The following required variables are missing: `'.implode('`, `', $variableNames).'`.', ...$variableNames);
    }
}
