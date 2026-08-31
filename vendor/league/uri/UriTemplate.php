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
use Deprecated;
use League\Uri\Contracts\UriException;
use League\Uri\Contracts\UriInterface;
use League\Uri\Exceptions\MissingFeature;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\UriTemplate\Template;
use League\Uri\UriTemplate\TemplateCanNotBeExpanded;
use League\Uri\UriTemplate\VariableBag;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface as Psr7UriInterface;
use Stringable;
use Uri\InvalidUriException;
use Uri\Rfc3986\Uri as Rfc3986Uri;
use Uri\WhatWg\InvalidUrlException;
use Uri\WhatWg\Url as WhatWgUrl;

use function array_fill_keys;
use function array_key_exists;
use function class_exists;

/**
 * Defines the URI Template syntax and the process for expanding a URI Template into a URI reference.
 *
 * @link    https://tools.ietf.org/html/rfc6570
 * @package League\Uri
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since   6.1.0
 *
 * @phpstan-import-type InputValue from VariableBag
 */
final class UriTemplate implements Stringable
{
    private readonly Template $template;
    private readonly VariableBag $defaultVariables;

    /**
     * @throws SyntaxError if the template syntax is invalid
     * @throws TemplateCanNotBeExpanded if the template or the variables are invalid
     */
    public function __construct(BackedEnum|Stringable|string $template, iterable $defaultVariables = [])
    {
        $this->template = $template instanceof Template ? $template : Template::new($template);
        $this->defaultVariables = $this->filterVariables($defaultVariables);
    }

    private function filterVariables(iterable $variables): VariableBag
    {
        if (!$variables instanceof VariableBag) {
            $variables = new VariableBag($variables);
        }

        return $variables
            ->filter(fn ($value, string|int $name) => array_key_exists(
                $name,
                array_fill_keys($this->template->variableNames, 1)
            ));
    }

    /**
     * Returns the string representation of the UriTemplate.
     */
    public function __toString(): string
    {
        return $this->template->value;
    }

    /**
     * Returns the distinct variables placeholders used in the template.
     *
     * @return array<string>
     */
    public function getVariableNames(): array
    {
        return $this->template->variableNames;
    }

    /**
     * @return array<string, InputValue>
     */
    public function getDefaultVariables(): array
    {
        return iterator_to_array($this->defaultVariables);
    }

    /**
     * Returns a new instance with the updated default variables.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified default variables.
     *
     * If present, variables whose name is not part of the current template
     * possible variable names are removed.
     *
     * @throws TemplateCanNotBeExpanded if the variables are invalid
     */
    public function withDefaultVariables(iterable $defaultVariables): self
    {
        $defaultVariables = $this->filterVariables($defaultVariables);
        if ($this->defaultVariables->equals($defaultVariables)) {
            return $this;
        }

        return new self($this->template, $defaultVariables);
    }

    private function templateExpanded(iterable $variables = []): string
    {
        return $this->template->expand($this->filterVariables($variables)->replace($this->defaultVariables));
    }

    private function templateExpandedOrFail(iterable $variables = []): string
    {
        return $this->template->expandOrFail($this->filterVariables($variables)->replace($this->defaultVariables));
    }

    /**
     * @throws TemplateCanNotBeExpanded if the variables are invalid
     * @throws UriException if the resulting expansion cannot be converted to a UriInterface instance
     */
    public function expand(iterable $variables = [], Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUri = null): UriInterface
    {
        $expanded = $this->templateExpanded($variables);

        return null === $baseUri ? Uri::new($expanded) : (Uri::parse($expanded, $baseUri) ?? throw new SyntaxError('Unable to expand URI'));
    }

    /**
     * @throws MissingFeature if no Uri\Rfc3986\Uri class is found
     * @throws TemplateCanNotBeExpanded if the variables are invalid
     * @throws InvalidUriException if the base URI cannot be converted to a Uri\Rfc3986\Uri instance
     * @throws InvalidUriException if the resulting expansion cannot be converted to a Uri\Rfc3986\Uri instance
     */
    public function expandToUri(iterable $variables = [], Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUri = null): Rfc3986Uri
    {
        class_exists(Rfc3986Uri::class) || throw new MissingFeature('Support for '.Rfc3986Uri::class.' requires PHP8.5+ or a polyfill. Run "composer require league/uri-polyfill" or use you own polyfill.');

        return new Rfc3986Uri($this->templateExpanded($variables), $this->newRfc3986Uri($baseUri));
    }

    /**
     * @throws MissingFeature if no Uri\Whatwg\Url class is found
     * @throws TemplateCanNotBeExpanded if the variables are invalid
     * @throws InvalidUrlException if the base URI cannot be converted to a Uri\Whatwg\Url instance
     * @throws InvalidUrlException if the resulting expansion cannot be converted to a Uri\Whatwg\Url instance
     */
    public function expandToUrl(iterable $variables = [], Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUrl = null, array|null &$errors = []): WhatWgUrl
    {
        class_exists(WhatWgUrl::class) || throw new MissingFeature('Support for '.WhatWgUrl::class.' requires PHP8.5+ or a polyfill. Run "composer require league/uri-polyfill" or use you own polyfill.');

        return new WhatWgUrl($this->templateExpanded($variables), $this->newWhatWgUrl($baseUrl), $errors);
    }

    /**
     * @throws TemplateCanNotBeExpanded if the variables are invalid
     * @throws UriException if the resulting expansion cannot be converted to a UriInterface instance
     */
    public function expandToPsr7Uri(
        iterable $variables = [],
        Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUrl = null,
        UriFactoryInterface $uriFactory = new HttpFactory()
    ): Psr7UriInterface {
        $uriString = $this->templateExpandedOrFail($variables);

        return $uriFactory->createUri(
            null === $baseUrl
            ? $uriString
            : UriString::resolve($uriString, match (true) {
                $baseUrl instanceof Rfc3986Uri => $baseUrl->toRawString(),
                $baseUrl instanceof WhatWgUrl => $baseUrl->toUnicodeString(),
                default => $baseUrl,
            })
        );
    }

    /**
     * @throws TemplateCanNotBeExpanded if the variables are invalid or missing
     * @throws UriException if the resulting expansion cannot be converted to a UriInterface instance
     */
    public function expandOrFail(iterable $variables = [], Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUri = null): UriInterface
    {
        $expanded = $this->templateExpandedOrFail($variables);

        return null === $baseUri ? Uri::new($expanded) : (Uri::parse($expanded, $baseUri) ?? throw new SyntaxError('Unable to expand URI'));
    }

    /**
     * @throws MissingFeature if no Uri\Rfc3986\Uri class is found
     * @throws TemplateCanNotBeExpanded if the variables are invalid
     * @throws InvalidUriException if the base URI cannot be converted to a Uri\Rfc3986\Uri instance
     * @throws InvalidUriException if the resulting expansion cannot be converted to a Uri\Rfc3986\Uri instance
     */
    public function expandToUriOrFail(iterable $variables = [], Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUri = null): Rfc3986Uri
    {
        class_exists(Rfc3986Uri::class) || throw new MissingFeature('Support for '.Rfc3986Uri::class.' requires PHP8.5+ or a polyfill. Run "composer require league/uri-polyfill" or use you own polyfill.');

        return new Rfc3986Uri($this->templateExpandedOrFail($variables), $this->newRfc3986Uri($baseUri));
    }

    /**
     * @throws MissingFeature if no Uri\Whatwg\Url class is found
     * @throws TemplateCanNotBeExpanded if the variables are invalid
     * @throws InvalidUrlException if the base URI cannot be converted to a Uri\Whatwg\Url instance
     * @throws InvalidUrlException if the resulting expansion cannot be converted to a Uri\Whatwg\Url instance
     */
    public function expandToUrlOrFail(iterable $variables = [], Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUrl = null, array|null &$errors = []): WhatWgUrl
    {
        class_exists(WhatWgUrl::class) || throw new MissingFeature('Support for '.WhatWgUrl::class.' requires PHP8.5+ or a polyfill. Run "composer require league/uri-polyfill" or use you own polyfill.');

        return new WhatWgUrl($this->templateExpandedOrFail($variables), $this->newWhatWgUrl($baseUrl), $errors);
    }

    /**
     * @throws TemplateCanNotBeExpanded if the variables are invalid
     * @throws UriException if the resulting expansion cannot be converted to a UriInterface instance
     */
    public function expandToPsr7UriOrFail(
        iterable $variables = [],
        Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUrl = null,
        UriFactoryInterface $uriFactory = new HttpFactory()
    ): Psr7UriInterface {
        $uriString = $this->templateExpandedOrFail($variables);

        return $uriFactory->createUri(
            null === $baseUrl
            ? $uriString
            : UriString::resolve($uriString, match (true) {
                $baseUrl instanceof Rfc3986Uri => $baseUrl->toRawString(),
                $baseUrl instanceof WhatWgUrl => $baseUrl->toUnicodeString(),
                default => $baseUrl,
            })
        );
    }

    /**
     * @throws InvalidUrlException
     */
    private function newWhatWgUrl(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $url = null): ?WhatWgUrl
    {
        return match (true) {
            null === $url => null,
            $url instanceof WhatWgUrl => $url,
            $url instanceof Rfc3986Uri => new WhatWgUrl($url->toRawString()),
            $url instanceof BackedEnum => new WhatWgUrl((string) $url->value),
            default => new WhatWgUrl((string) $url),
        };
    }

    /**
     * @throws InvalidUriException
     */
    private function newRfc3986Uri(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $uri = null): ?Rfc3986Uri
    {
        return match (true) {
            null === $uri => null,
            $uri instanceof Rfc3986Uri => $uri,
            $uri instanceof WhatWgUrl => new Rfc3986Uri($uri->toAsciiString()),
            $uri instanceof BackedEnum => new Rfc3986Uri((string) $uri->value),
            default => new Rfc3986Uri((string) $uri),
        };
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.6.0
     * @codeCoverageIgnore
     * @see UriTemplate::toString()
     *
     * Create a new instance from the environment.
     */
    #[Deprecated(message:'use League\Uri\UriTemplate::__toString() instead', since:'league/uri:7.6.0')]
    public function getTemplate(): string
    {
        return $this->__toString();
    }
}
