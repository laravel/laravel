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

use Deprecated;
use League\Uri\Exceptions\SyntaxError;
use Stringable;

use function array_filter;
use function array_map;
use function array_unique;
use function explode;
use function implode;

/**
 * @internal The class exposes the internal representation of an Expression and its usage
 * @link https://www.rfc-editor.org/rfc/rfc6570#section-2.2
 */
final class Expression
{
    /** @var array<VarSpecifier> */
    private readonly array $varSpecifiers;
    /** @var array<string> */
    public readonly array $variableNames;
    public readonly string $value;

    private function __construct(public readonly Operator $operator, VarSpecifier ...$varSpecifiers)
    {
        $this->varSpecifiers = $varSpecifiers;
        $this->variableNames = array_unique(
            array_map(
                static fn (VarSpecifier $varSpecifier): string => $varSpecifier->name,
                $varSpecifiers
            )
        );
        $this->value = '{'.$operator->value.implode(',', array_map(
            static fn (VarSpecifier $varSpecifier): string => $varSpecifier->toString(),
            $varSpecifiers
        )).'}';
    }

    /**
     * @throws SyntaxError if the expression is invalid
     */
    public static function new(Stringable|string $expression): self
    {
        $parts = Operator::parseExpression($expression);

        return new Expression($parts['operator'], ...array_map(
            static fn (string $varSpec): VarSpecifier => VarSpecifier::new($varSpec),
            explode(',', $parts['variables'])
        ));
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @throws SyntaxError if the expression is invalid
     * @see Expression::new()
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     */
    #[Deprecated(message:'use League\Uri\UriTemplate\Exppression::new() instead', since:'league/uri:7.0.0')]
    public static function createFromString(Stringable|string $expression): self
    {
        return self::new($expression);
    }

    public function expand(VariableBag $variables): string
    {
        $expanded = implode(
            $this->operator->separator(),
            array_filter(
                array_map(
                    fn (VarSpecifier $varSpecifier): string => $this->operator->expand($varSpecifier, $variables),
                    $this->varSpecifiers
                ),
                static fn ($value): bool => '' !== $value
            )
        );

        return match ('') {
            $expanded => '',
            default => $this->operator->first().$expanded,
        };
    }
}
