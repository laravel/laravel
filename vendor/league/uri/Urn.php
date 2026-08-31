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

namespace League\Uri;

use BackedEnum;
use Closure;
use JsonSerializable;
use League\Uri\Contracts\Conditionable;
use League\Uri\Contracts\Transformable;
use League\Uri\Contracts\UriComponentInterface;
use League\Uri\Contracts\UriInterface;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\UriTemplate\Template;
use Stringable;
use Uri\Rfc3986\Uri as Rfc3986Uri;
use Uri\WhatWg\Url as WhatWgUrl;

use function is_bool;
use function preg_match;
use function str_replace;
use function strtolower;

/**
 * @phpstan-type UrnSerialize array{0: array{urn: non-empty-string}, 1: array{}}
 * @phpstan-import-type InputComponentMap from UriString
 * @phpstan-type UrnMap array{
 *      scheme: 'urn',
 *      nid: string,
 *      nss: string,
 *      r_component: ?string,
 *      q_component: ?string,
 *      f_component: ?string,
 *  }
 */
final class Urn implements Conditionable, Stringable, JsonSerializable, Transformable
{
    /**
     * RFC8141 regular expression URN splitter.
     *
     * The regexp does not perform any look-ahead.
     * Not all invalid URN are caught. Some
     * post-regexp-validation checks
     * are mandatory.
     *
     * @link https://datatracker.ietf.org/doc/html/rfc8141#section-2
     *
     * @var string
     */
    private const REGEXP_URN_PARTS = '/^
        urn:
        (?<nid>[a-z0-9](?:[a-z0-9-]{0,30}[a-z0-9])?): # NID
        (?<nss>.*?)                                   # NSS
        (?<frc>\?\+(?<rcomponent>.*?))?               # r-component
        (?<fqc>\?\=(?<qcomponent>.*?))?               # q-component
        (?:\#(?<fcomponent>.*))?                      # f-component
    $/xi';

    /**
     * RFC8141 namespace identifier regular expression.
     *
     * @link https://datatracker.ietf.org/doc/html/rfc8141#section-2
     *
     * @var string
     */
    private const REGEX_NID_SEQUENCE = '/^[a-z0-9]([a-z0-9-]{0,30})[a-z0-9]$/xi';

    /** @var non-empty-string */
    private readonly string $uriString;
    /** @var non-empty-string */
    private readonly string $nid;
    /** @var non-empty-string */
    private readonly string $nss;
    /** @var non-empty-string|null */
    private readonly ?string $rComponent;
    /** @var non-empty-string|null */
    private readonly ?string $qComponent;
    /** @var non-empty-string|null */
    private readonly ?string $fComponent;

    /**
     * @param Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $urn the percent-encoded URN
     */
    public static function parse(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $urn): ?Urn
    {
        try {
            return self::fromString($urn);
        } catch (SyntaxError) {
            return null;
        }
    }

    /**
     * @param Rfc3986Uri|WhatWgUrl|Stringable|string $urn the percent-encoded URN
     * @see self::fromString()
     *
     * @throws SyntaxError if the URN is invalid
     */
    public static function new(Rfc3986Uri|WhatWgUrl|Stringable|string $urn): self
    {
        return self::fromString($urn);
    }

    /**
     * @param Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $urn the percent-encoded URN
     *
     * @throws SyntaxError if the URN is invalid
     */
    public static function fromString(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $urn): self
    {
        $urn = match (true) {
            $urn instanceof Rfc3986Uri => $urn->toRawString(),
            $urn instanceof WhatWgUrl => $urn->toAsciiString(),
            $urn instanceof BackedEnum => (string) $urn->value,
            default => (string) $urn,
        };

        UriString::containsRfc3986Chars($urn) || throw new SyntaxError('The URN is malformed, it contains invalid characters.');
        1 === preg_match(self::REGEXP_URN_PARTS, $urn, $matches) || throw new SyntaxError('The URN string is invalid.');

        return new self(
            nid: $matches['nid'],
            nss: $matches['nss'],
            rComponent: (isset($matches['frc']) && '' !== $matches['frc']) ? $matches['rcomponent'] : null,
            qComponent: (isset($matches['fqc']) && '' !== $matches['fqc']) ? $matches['qcomponent'] : null,
            fComponent: $matches['fcomponent'] ?? null,
        );
    }

    /**
     * Create a new instance from a hash representation of the URI similar
     * to PHP parse_url function result.
     *
     * @param InputComponentMap $components a hash representation of the URI similar to PHP parse_url function result
     */
    public static function fromComponents(array $components = []): self
    {
        $components += [
            'scheme' => null, 'user' => null, 'pass' => null, 'host' => null,
            'port' => null, 'path' => '', 'query' => null, 'fragment' => null,
        ];

        return self::fromString(UriString::build($components));
    }

    /**
     * @param Stringable|string $nss the percent-encoded NSS
     *
     * @throws SyntaxError if the URN is invalid
     */
    public static function fromRfc2141(BackedEnum|Stringable|string $nid, BackedEnum|Stringable|string $nss): self
    {
        if ($nid instanceof BackedEnum) {
            $nid = $nid->value;
        }

        if ($nss instanceof BackedEnum) {
            $nss = $nss->value;
        }

        return new self((string) $nid, (string) $nss);
    }

    /**
     * @param string $nss the percent-encoded NSS
     * @param ?string $rComponent the percent-encoded r-component
     * @param ?string $qComponent the percent-encoded q-component
     * @param ?string $fComponent the percent-encoded f-component
     *
     * @throws SyntaxError if one of the URN part is invalid
     */
    private function __construct(
        string $nid,
        string $nss,
        ?string $rComponent = null,
        ?string $qComponent = null,
        ?string $fComponent = null,
    ) {
        ('' !== $nid && 1 === preg_match(self::REGEX_NID_SEQUENCE, $nid)) || throw new SyntaxError('The URN is malformed, the NID is invalid.');
        ('' !== $nss && Encoder::isPathEncoded($nss)) || throw new SyntaxError('The URN is malformed, the NSS is invalid.');

        /** @param Closure(string): ?non-empty-string $closure */
        $validateComponent = static fn (?string $value, Closure $closure, string $name): ?string => match (true) {
            null === $value,
            ('' !== $value && 1 !== preg_match('/[#?]/', $value) && $closure($value)) => $value,
            default => throw new SyntaxError('The URN is malformed, the `'.$name.'` component is invalid.'),
        };

        $this->nid = $nid;
        $this->nss = $nss;
        $this->rComponent = $validateComponent($rComponent, Encoder::isPathEncoded(...), 'r-component');
        $this->qComponent = $validateComponent($qComponent, Encoder::isQueryEncoded(...), 'q-component');
        $this->fComponent = $validateComponent($fComponent, Encoder::isFragmentEncoded(...), 'f-component');
        $this->uriString = $this->setUriString();
    }

    /**
     * @return non-empty-string
     */
    private function setUriString(): string
    {
        $str = $this->toRfc2141();
        if (null !== $this->rComponent) {
            $str .= '?+'.$this->rComponent;
        }

        if (null !== $this->qComponent) {
            $str .= '?='.$this->qComponent;
        }

        if (null !== $this->fComponent) {
            $str .= '#'.$this->fComponent;
        }

        return $str;
    }

    /**
     * Returns the NID.
     *
     * @return non-empty-string
     */
    public function getNid(): string
    {
        return $this->nid;
    }

    /**
     * Returns the percent-encoded NSS.
     *
     * @return non-empty-string
     */
    public function getNss(): string
    {
        return $this->nss;
    }

    /**
     * Returns the percent-encoded r-component string or null if it is not set.
     *
     * @return ?non-empty-string
     */
    public function getRComponent(): ?string
    {
        return $this->rComponent;
    }

    /**
     * Returns the percent-encoded q-component string or null if it is not set.
     *
     * @return ?non-empty-string
     */
    public function getQComponent(): ?string
    {
        return $this->qComponent;
    }

    /**
     * Returns the percent-encoded f-component string or null if it is not set.
     *
     * @return ?non-empty-string
     */
    public function getFComponent(): ?string
    {
        return $this->fComponent;
    }

    /**
     * Returns the RFC8141 URN string representation.
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->uriString;
    }

    /**
     * Returns the RFC2141 URN string representation.
     *
     * @return non-empty-string
     */
    public function toRfc2141(): string
    {
        return 'urn:'.$this->nid.':'.$this->nss;
    }

    /**
     * Returns the human-readable string representation of the URN as an IRI.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3987
     */
    public function toDisplayString(): string
    {
        return UriString::toIriString($this->uriString);
    }

    /**
     * Returns the RFC8141 URN string representation.
     *
     * @see self::toString()
     *
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Returns the RFC8141 URN string representation.
     * @see self::toString()
     *
     * @return non-empty-string
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * Returns the RFC3986 representation of the current URN.
     *
     * If a template URI is used the following variables as present
     * {nid} for the namespace identifier
     * {nss} for the namespace specific string
     * {r_component} for the r-component without its delimiter
     * {q_component} for the q-component without its delimiter
     * {f_component} for the f-component without its delimiter
     */
    public function resolve(UriTemplate|Template|BackedEnum|string|null $template = null): UriInterface
    {
        return null !== $template ? Uri::fromTemplate($template, $this->toComponents()) : Uri::new($this->uriString);
    }

    public function hasRComponent(): bool
    {
        return null !== $this->rComponent;
    }

    public function hasQComponent(): bool
    {
        return null !== $this->qComponent;
    }

    public function hasFComponent(): bool
    {
        return null !== $this->fComponent;
    }

    public function hasOptionalComponent(): bool
    {
        return null !== $this->rComponent
            || null !== $this->qComponent
            || null !== $this->fComponent;
    }

    /**
     * Return an instance with the specified NID.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified NID.
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in an object in invalid state.
     */
    public function withNid(BackedEnum|Stringable|string $nid): self
    {
        if ($nid instanceof BackedEnum) {
            $nid = $nid->value;
        }

        $nid = (string) $nid;

        return $this->nid === $nid ? $this : new self(
            nid: $nid,
            nss: $this->nss,
            rComponent: $this->rComponent,
            qComponent: $this->qComponent,
            fComponent: $this->fComponent,
        );
    }

    /**
     * Return an instance with the specified NSS.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified NSS.
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in an object in invalid state.
     */
    public function withNss(BackedEnum|Stringable|string $nss): self
    {
        $nss = Encoder::encodePath($nss);

        return $this->nss === $nss ? $this : new self(
            nid: $this->nid,
            nss: $nss,
            rComponent: $this->rComponent,
            qComponent: $this->qComponent,
            fComponent: $this->fComponent,
        );
    }

    /**
     * Return an instance with the specified r-component.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified r-component.
     *
     * The component is removed if the value is null.
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in an object in invalid state.
     */
    public function withRComponent(BackedEnum|Stringable|string|null $component): self
    {
        if ($component instanceof BackedEnum) {
            $component = (string) $component->value;
        }

        if ($component instanceof UriComponentInterface) {
            $component = $component->value();
        }

        if (null !== $component) {
            $component = self::formatComponent(Encoder::encodePath($component));
        }

        return $this->rComponent === $component ? $this : new self(
            nid: $this->nid,
            nss: $this->nss,
            rComponent: $component,
            qComponent: $this->qComponent,
            fComponent: $this->fComponent,
        );
    }

    private static function formatComponent(?string $component): ?string
    {
        return null === $component ? null : str_replace(['?', '#'], ['%3F', '%23'], $component);
    }

    /**
     * Return an instance with the specified q-component.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified q-component.
     *
     * The component is removed if the value is null.
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in an object in invalid state.
     */
    public function withQComponent(BackedEnum|Stringable|string|null $component): self
    {
        if ($component instanceof UriComponentInterface) {
            $component = $component->value();
        }

        $component = self::formatComponent(Encoder::encodeQueryOrFragment($component));

        return $this->qComponent === $component ? $this : new self(
            nid: $this->nid,
            nss: $this->nss,
            rComponent: $this->rComponent,
            qComponent: $component,
            fComponent: $this->fComponent,
        );
    }

    /**
     * Return an instance with the specified f-component.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified f-component.
     *
     * The component is removed if the value is null.
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in an object in invalid state.
     */
    public function withFComponent(BackedEnum|Stringable|string|null $component): self
    {
        if ($component instanceof UriComponentInterface) {
            $component = $component->value();
        }

        $component = self::formatComponent(Encoder::encodeQueryOrFragment($component));

        return $this->fComponent === $component ? $this : new self(
            nid: $this->nid,
            nss: $this->nss,
            rComponent: $this->rComponent,
            qComponent: $this->qComponent,
            fComponent: $component,
        );
    }

    public function normalize(): self
    {
        $copy = new self(
            nid: strtolower($this->nid),
            nss: (string) Encoder::normalizePath($this->nss),
            rComponent: null === $this->rComponent ? $this->rComponent : Encoder::normalizePath($this->rComponent),
            qComponent: Encoder::normalizeQuery($this->qComponent),
            fComponent: Encoder::normalizeFragment($this->fComponent),
        );

        return $copy->uriString === $this->uriString ? $this : $copy;
    }

    public function equals(Urn|Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $other, UrnComparisonMode $urnComparisonMode = UrnComparisonMode::ExcludeComponents): bool
    {
        if (!$other instanceof Urn) {
            $other = self::parse($other);
        }

        return (null !== $other) && match ($urnComparisonMode) {
            UrnComparisonMode::ExcludeComponents => $other->normalize()->toRfc2141() === $this->normalize()->toRfc2141(),
            UrnComparisonMode::IncludeComponents => $other->normalize()->toString() === $this->normalize()->toString(),
        };
    }

    public function when(callable|bool $condition, callable $onSuccess, ?callable $onFail = null): static
    {
        if (!is_bool($condition)) {
            $condition = $condition($this);
        }

        return match (true) {
            $condition => $onSuccess($this),
            null !== $onFail => $onFail($this),
            default => $this,
        } ?? $this;
    }

    public function transform(callable $callback): static
    {
        return $callback($this);
    }

    /**
     * @return UrnSerialize
     */
    public function __serialize(): array
    {
        return [['urn' => $this->toString()], []];
    }

    /**
     * @param UrnSerialize $data
     *
     * @throws SyntaxError
     */
    public function __unserialize(array $data): void
    {
        [$properties] = $data;
        $uri = self::fromString($properties['urn'] ?? throw new SyntaxError('The `urn` property is missing from the serialized object.'));

        $this->nid = $uri->nid;
        $this->nss = $uri->nss;
        $this->rComponent = $uri->rComponent;
        $this->qComponent = $uri->qComponent;
        $this->fComponent = $uri->fComponent;
        $this->uriString = $uri->uriString;
    }

    /**
     * @return UrnMap
     */
    public function toComponents(): array
    {
        return [
            'scheme' => 'urn',
            'nid' => $this->nid,
            'nss' => $this->nss,
            'r_component' => $this->rComponent,
            'q_component' => $this->qComponent,
            'f_component' => $this->fComponent,
        ];
    }

    /**
     * @return UrnMap
     */
    public function __debugInfo(): array
    {
        return $this->toComponents();
    }
}
