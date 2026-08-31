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

use BackedEnum;
use Deprecated;
use League\Uri\Exceptions\SyntaxError;
use Stringable;

use function array_filter;
use function array_map;
use function array_reduce;
use function array_unique;
use function preg_match_all;
use function preg_replace;
use function str_replace;
use function strpbrk;

use const PREG_SET_ORDER;

/**
 * @internal The class exposes the internal representation of a Template and its usage
 */
final class Template implements Stringable
{
    /**
     * Expression regular expression pattern.
     */
    private const REGEXP_EXPRESSION_DETECTOR = '/(?<expression>\{[^}]*})/x';

    /** @var array<Expression> */
    private readonly array $expressions;
    /** @var array<string> */
    public readonly array $variableNames;

    private function __construct(public readonly string $value, Expression ...$expressions)
    {
        $this->expressions = $expressions;
        $this->variableNames = array_unique(
            array_merge(
                ...array_map(
                    static fn (Expression $expression): array => $expression->variableNames,
                    $expressions
                )
            )
        );
    }

    /**
     * @throws SyntaxError if the template contains invalid expressions
     * @throws SyntaxError if the template contains invalid variable specification
     */
    public static function new(BackedEnum|Stringable|string $template): self
    {
        if ($template instanceof BackedEnum) {
            $template = $template->value;
        }

        $template = (string) $template;
        /** @var string $remainder */
        $remainder = preg_replace(self::REGEXP_EXPRESSION_DETECTOR, '', $template);
        false === strpbrk($remainder, '{}') || throw new SyntaxError('The template "'.$template.'" contains invalid expressions.');

        preg_match_all(self::REGEXP_EXPRESSION_DETECTOR, $template, $founds, PREG_SET_ORDER);

        return new self($template, ...array_values(
            array_reduce($founds, function (array $carry, array $found): array {
                if (!isset($carry[$found['expression']])) {
                    $carry[$found['expression']] = Expression::new($found['expression']);
                }

                return $carry;
            }, [])
        ));
    }

    /**
     * @throws TemplateCanNotBeExpanded if the variables are invalid
     */
    public function expand(iterable $variables = []): string
    {
        if (!$variables instanceof VariableBag) {
            $variables = new VariableBag($variables);
        }

        return $this->expandAll($variables);
    }

    /**
     * @throws TemplateCanNotBeExpanded if the variables are invalid or missing
     */
    public function expandOrFail(iterable $variables = []): string
    {
        if (!$variables instanceof VariableBag) {
            $variables = new VariableBag($variables);
        }

        $missing = array_filter($this->variableNames, fn (string $name): bool => !isset($variables[$name]));
        if ([] !== $missing) {
            throw TemplateCanNotBeExpanded::dueToMissingVariables(...$missing);
        }

        return $this->expandAll($variables);
    }

    private function expandAll(VariableBag $variables): string
    {
        return array_reduce(
            $this->expressions,
            fn (string $uri, Expression $expr): string => str_replace($expr->value, $expr->expand($variables), $uri),
            $this->value
        );
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @throws SyntaxError if the template contains invalid expressions
     * @throws SyntaxError if the template contains invalid variable specification
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Template::new()
     *
     * Create a new instance from a string.
     *
     */
    #[Deprecated(message:'use League\Uri\UriTemplate\Template::new() instead', since:'league/uri:7.0.0')]
    public static function createFromString(Stringable|string $template): self
    {
        return self::new($template);
    }
}
