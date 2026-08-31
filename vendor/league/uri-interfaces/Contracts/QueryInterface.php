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

namespace League\Uri\Contracts;

use BackedEnum;
use Countable;
use Deprecated;
use Iterator;
use IteratorAggregate;
use League\Uri\QueryComposeMode;
use League\Uri\StringCoercionMode;
use Stringable;

/**
 * @extends IteratorAggregate<array{0:string, 1:string|null}>
 *
 * @method string|null toFormData() Returns the string representation using the application/www-form-urlencoded rules
 * @method string|null toRFC3986() Returns the string representation using RFC3986 rules
 * @method string|null first(string $key) Returns the first value associated with the given name
 * @method string|null last(string $key) Returns the first value associated with the given name
 * @method int|null indexOf(string $key, int $nth = 0) Returns the offset of the pair based on its key and its nth occurrence; negative occurrences are supported
 * @method int|null indexOfValue(?string $value, int $nth = 0) Returns the offset of the pair based on its value and its nth occurrence; negative occurrences are supported
 * @method array pair(int $offset) Returns the key/value pair at the given numeric offset; negative occurrences are supported
 * @method int countDistinctKeys() Returns the total number of distinct keys
 * @method string|null valueAt(int $offset): Returns the value at the given numeric offset; negative occurrences are supported
 * @method string keyAt(int $offset): Returns the key at the given numeric offset; negative occurrences are supported
 * @method self normalize() returns the normalized string representation of the component
 * @method self withoutPairByKey(string ...$keys) Returns an instance without pairs with the specified keys.
 * @method self withoutPairByValue(array|BackedEnum|Stringable|string|int|bool|null $values, StringCoercionMode $coercionMode = StringCoercionMode::Native) Returns an instance without pairs with the specified values.
 * @method self withoutPairByKeyValue(string $key, BackedEnum|Stringable|string|int|bool|null $value, StringCoercionMode $coercionMode = StringCoercionMode::Native) Returns an instance without pairs with the specified key/value pair
 * @method bool hasPair(string $key, ?string $value) Tells whether the pair exists in the query.
 * @method array getList(string $name) Returns the list associated with the given name or an empty array if it does not exist.
 * @method bool hasList(string ...$names) Tells whether the parameter list exists in the query.
 * @method self appendList(string $name, array $values, QueryComposeMode $composeMode = QueryComposeMode::Native) Appends a parameter to the query string
 * @method self withList(string $name, array $values, QueryComposeMode $composeMode = QueryComposeMode::Native) Adds a new parameter to the query string and remove any previously set values
 * @method self withoutList(string ...$names) Removes any given list associated with the given names
 * @method self withoutLists() Removes all lists from the query string
 * @method self onlyLists() Removes all pairs without a valid PHP's bracket notation
 */
interface QueryInterface extends Countable, IteratorAggregate, UriComponentInterface
{
    /**
     * Returns the query separator.
     *
     * @return non-empty-string
     */
    public function getSeparator(): string;

    /**
     * Returns the number of key/value pairs present in the object.
     */
    public function count(): int;

    /**
     * Returns an iterator allowing to go through all key/value pairs contained in this object.
     *
     * The pair is represented as an array where the first value is the pair key
     * and the second value the pair value.
     *
     * The key of each pair is a string
     * The value of each pair is a scalar or the null value
     *
     * @return Iterator<int, array{0:string, 1:string|null}>
     */
    public function getIterator(): Iterator;

    /**
     * Returns an iterator allowing to go through all key/value pairs contained in this object.
     *
     * The return type is as an Iterator where its offset is the pair key and its value the pair value.
     *
     * The key of each pair is a string
     * The value of each pair is a scalar or the null value
     *
     * @return iterable<string, string|null>
     */
    public function pairs(): iterable;

    /**
     * Tells whether a list of pair with a specific key exists.
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-has
     */
    public function has(string ...$keys): bool;

    /**
     * Returns the first value associated to the given pair name.
     *
     * If no value is found null is returned
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-get
     */
    public function get(string $key): ?string;

    /**
     * Returns all the values associated to the given pair name as an array or all
     * the instance pairs.
     *
     * If no value is found an empty array is returned
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-getall
     *
     * @return array<int, string|null>
     */
    public function getAll(string $key): array;

    /**
     * Returns the store PHP variables as elements of an array.
     *
     * The result is similar as PHP parse_str when used with its
     * second argument with the difference that variable names are
     * not mangled.
     *
     * @see http://php.net/parse_str
     * @see https://wiki.php.net/rfc/on_demand_name_mangling
     *
     * @return array the collection of stored PHP variables or the empty array if no input is given,
     */
    public function parameters(): array;

    /**
     * Returns the value attached to the specific key.
     *
     * The result is similar to PHP parse_str with the difference that variable
     * names are not mangled.
     *
     * If a key is submitted it will return the value attached to it or null
     *
     * @see http://php.net/parse_str
     * @see https://wiki.php.net/rfc/on_demand_name_mangling
     *
     * @return mixed the collection of stored PHP variables or the empty array if no input is given,
     *               the single value of a stored PHP variable or null if the variable is not present in the collection
     */
    public function parameter(string $name): mixed;

    /**
     * Tells whether a list of variable with specific names exists.
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-has
     */
    public function hasParameter(string ...$names): bool;

    /**
     * Returns the RFC1738 encoded query.
     */
    public function toRFC1738(): ?string;

    /**
     * Returns an instance with a different separator.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the query component with a different separator
     */
    public function withSeparator(string $separator): self;

    /**
     * Returns an instance with the new pairs set to it.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified query
     *
     * @see ::withPair
     */
    public function merge(Stringable|string $query): self;

    /**
     * Returns an instance with the new pairs appended to it.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified query
     *
     * If the pair already exists the value will be added to it.
     */
    public function append(Stringable|string $query): self;

    /**
     * Returns a new instance with a specified key/value pair appended as a new pair.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified query
     */
    public function appendTo(string $key, Stringable|string|int|bool|null $value): self;

    /**
     * Sorts the query string by offset, maintaining offset to data correlations.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified query
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-sort
     */
    public function sort(): self;

    /**
     * Returns an instance without duplicate key/value pair.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the query component normalized by removing
     * duplicate pairs whose key/value are the same.
     */
    public function withoutDuplicates(): self;

    /**
     * Returns an instance without empty key/value where the value is the null value.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the query component normalized by removing
     * empty pairs.
     *
     * A pair is considered empty if its value is equal to the null value
     */
    public function withoutEmptyPairs(): self;

    /**
     * Returns an instance where numeric indices associated to PHP's array like key are removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the query component normalized so that numeric indexes
     * are removed from the pair key value.
     *
     * i.e.: toto[3]=bar[3]&foo=bar becomes toto[]=bar[3]&foo=bar
     */
    public function withoutNumericIndices(): self;

    /**
     * Returns an instance with a new key/value pair added to it.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified query
     *
     * If the pair already exists the value will replace the existing value.
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-set
     */
    public function withPair(string $key, Stringable|string|int|float|bool|null $value): self;

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.3.0
     * @codeCoverageIgnore
     * @see QueryInterface::withoutPairByKey()
     *
     * Returns an instance without the specified keys.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified component
     */
    #[Deprecated(message:'use League\Uri\Contracts\QueryInterface::withoutPairByKey() instead', since:'league/uri-interfaces:7.3.0')]
    public function withoutPair(string ...$keys): self;

    /**
     * Returns an instance without the specified params.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified component without PHP's value.
     * PHP's mangled is not taken into account.
     */
    public function withoutParameters(string ...$names): self;
}
