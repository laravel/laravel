<?php

namespace Illuminate\JsonSchema;

use InvalidArgumentException;

class Deserializer
{
    /**
     * The maximum number of schema fragments that may be expanded.
     */
    protected const MAX_NODES = 20000;

    /**
     * The root schema being deserialized (used to resolve local $refs).
     *
     * @var array<string, mixed>
     */
    protected array $root;

    /**
     * The number of schema fragments expanded so far.
     */
    protected int $nodes = 0;

    /**
     * The cache of resolved local "$ref" targets, keyed by reference.
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $refCache = [];

    /**
     * Create a new deserializer instance.
     *
     * @param  array<string, mixed>  $root
     */
    protected function __construct(array $root)
    {
        $this->root = $root;
    }

    /**
     * Deserialize the Laravel-supported JSON Schema subset into a type.
     *
     * @param  array<string, mixed>  $schema
     *
     * @throws \InvalidArgumentException
     */
    public static function deserialize(array $schema): Types\Type
    {
        return (new static($schema))->build($schema);
    }

    /**
     * Build a type from the given schema fragment.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<int, string>  $refs
     *
     * @throws \InvalidArgumentException
     */
    protected function build(array $schema, array $refs = []): Types\Type
    {
        if (++$this->nodes > static::MAX_NODES) {
            throw new InvalidArgumentException(
                'The JSON Schema is too large to deserialize; it expands beyond ['.static::MAX_NODES.'] fragments.'
            );
        }

        [$schema, $refs] = $this->resolveRef($schema, $refs);

        [$schema, $nullableFromUnion, $refs] = $this->normalizeUnions($schema, $refs);

        [$name, $nullableFromType] = $this->resolveType($schema);

        if (is_array($name)) {
            $this->ensureUnionConstraintsAreSupported($schema);

            $type = new Types\UnionType($name);
        } else {
            $type = match ($name) {
                'object' => $this->buildObject($schema, $refs),
                'array' => $this->buildArray($schema, $refs),
                'string' => $this->buildString($schema),
                'integer' => $this->buildInteger($schema),
                'number' => $this->buildNumber($schema),
                'boolean' => new Types\BooleanType,
                default => throw new InvalidArgumentException("Unsupported JSON Schema type [{$name}]."),
            };
        }

        $this->applyCommon($type, $schema);

        if ($nullableFromUnion || $nullableFromType) {
            $type->nullable();
        }

        return $type;
    }

    /**
     * Build an object type from the given schema fragment.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<int, string>  $refs
     *
     * @throws \InvalidArgumentException
     */
    protected function buildObject(array $schema, array $refs = []): Types\ObjectType
    {
        $properties = [];

        if (isset($schema['properties']) && is_array($schema['properties'])) {
            $required = is_array($schema['required'] ?? null)
                ? array_flip(array_map('strval', $schema['required']))
                : [];

            foreach ($schema['properties'] as $key => $definition) {
                if (! is_array($definition)) {
                    throw new InvalidArgumentException(
                        "Unable to represent the schema for property [{$key}]; boolean schemas are not supported."
                    );
                }

                $property = $this->build($definition, $refs);

                if (isset($required[(string) $key])) {
                    $property->required();
                }

                $properties[$key] = $property;
            }
        }

        $type = new Types\ObjectType($properties);

        if (($schema['additionalProperties'] ?? null) === false) {
            $type->withoutAdditionalProperties();
        }

        return $type;
    }

    /**
     * Build an array type from the given schema fragment.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<int, string>  $refs
     *
     * @throws \InvalidArgumentException
     */
    protected function buildArray(array $schema, array $refs = []): Types\ArrayType
    {
        $type = new Types\ArrayType;

        if (isset($schema['items']) && $schema['items'] !== []) {
            if (! is_array($schema['items']) || array_is_list($schema['items'])) {
                throw new InvalidArgumentException('Tuple and boolean JSON Schema "items" are not supported.');
            }

            $type->items($this->build($schema['items'], $refs));
        }

        if (isset($schema['minItems'])) {
            $type->min((int) $schema['minItems']);
        }

        if (isset($schema['maxItems'])) {
            $type->max((int) $schema['maxItems']);
        }

        if (isset($schema['uniqueItems'])) {
            $type->unique((bool) $schema['uniqueItems']);
        }

        return $type;
    }

    /**
     * Build a string type from the given schema fragment.
     *
     * @param  array<string, mixed>  $schema
     */
    protected function buildString(array $schema): Types\StringType
    {
        $type = new Types\StringType;

        if (isset($schema['minLength'])) {
            $type->min((int) $schema['minLength']);
        }

        if (isset($schema['maxLength'])) {
            $type->max((int) $schema['maxLength']);
        }

        if (isset($schema['pattern'])) {
            $type->pattern((string) $schema['pattern']);
        }

        if (isset($schema['format'])) {
            $type->format((string) $schema['format']);
        }

        return $type;
    }

    /**
     * Build an integer type from the given schema fragment.
     *
     * @param  array<string, mixed>  $schema
     */
    protected function buildInteger(array $schema): Types\IntegerType
    {
        return $this->applyNumericBounds(new Types\IntegerType, $schema, $this->toInteger(...));
    }

    /**
     * Build a number type from the given schema fragment.
     *
     * @param  array<string, mixed>  $schema
     */
    protected function buildNumber(array $schema): Types\NumberType
    {
        return $this->applyNumericBounds(new Types\NumberType, $schema);
    }

    /**
     * Apply the numeric bound keywords to the given integer or number type.
     *
     * @template TType of Types\IntegerType|Types\NumberType
     *
     * @param  TType  $type
     * @param  array<string, mixed>  $schema
     * @param  (callable(int|float): (int|float))|null  $cast
     * @return TType
     *
     * @throws \InvalidArgumentException
     */
    protected function applyNumericBounds(Types\IntegerType|Types\NumberType $type, array $schema, ?callable $cast = null)
    {
        $cast ??= static fn (int|float $value) => $value;

        foreach (['minimum' => 'min', 'maximum' => 'max', 'multipleOf' => 'multipleOf'] as $keyword => $method) {
            if (! isset($schema[$keyword])) {
                continue;
            }

            if (($value = $this->toNumber($schema[$keyword])) === null) {
                throw new InvalidArgumentException("The JSON Schema [{$keyword}] constraint must be a number.");
            }

            $type->{$method}($cast($value));
        }

        return $type;
    }

    /**
     * Apply the keywords shared by every type to the given instance.
     *
     * @param  array<string, mixed>  $schema
     *
     * @throws \InvalidArgumentException
     */
    protected function applyCommon(Types\Type $type, array $schema): void
    {
        if (isset($schema['title'])) {
            $type->title((string) $schema['title']);
        }

        if (isset($schema['description'])) {
            $type->description((string) $schema['description']);
        }

        if (isset($schema['enum']) && is_array($schema['enum'])) {
            $type->enum($schema['enum']);
        }

        if (array_key_exists('default', $schema)) {
            if ($schema['default'] === null) {
                throw new InvalidArgumentException('A null JSON Schema [default] is not supported.');
            }

            // The "default" setter is typed per concrete type, so assign it directly...
            (fn () => $this->default = $schema['default'])->call($type);
        }
    }

    /**
     * Resolve the base type name and whether the schema is nullable.
     *
     * @param  array<string, mixed>  $schema
     * @return array{0: string|array<int, string>, 1: bool}
     *
     * @throws \InvalidArgumentException
     */
    protected function resolveType(array $schema): array
    {
        $type = $schema['type'] ?? null;
        $nullable = false;

        if (is_array($type)) {
            $nullable = in_array('null', $type, true);

            $names = array_values(array_unique(array_map('strval', array_filter(
                $type,
                static fn ($value) => $value !== 'null',
            ))));

            if (count($names) > 1) {
                return [$names, $nullable];
            }

            $type = $names[0] ?? null;
        }

        $type ??= $this->inferType($schema);

        if (! is_string($type)) {
            throw new InvalidArgumentException('Unable to determine the JSON Schema type for the given schema.');
        }

        return [$type, $nullable];
    }

    /**
     * Ensure a multi-type union carries no type-specific constraint keywords.
     *
     * @param  array<string, mixed>  $schema
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureUnionConstraintsAreSupported(array $schema): void
    {
        $keywords = [
            'minLength', 'maxLength', 'pattern', 'format',
            'minimum', 'maximum', 'multipleOf',
            'items', 'minItems', 'maxItems', 'uniqueItems',
            'properties', 'required', 'additionalProperties',
        ];

        $unsupported = array_values(array_intersect($keywords, array_keys($schema)));

        if ($unsupported !== []) {
            throw new InvalidArgumentException(
                'Type-specific keywords ['.implode(', ', $unsupported).'] are not supported on a multi-type JSON Schema union.'
            );
        }
    }

    /**
     * Infer the type name when "type" is absent but the shape is unambiguous.
     *
     * @param  array<string, mixed>  $schema
     */
    protected function inferType(array $schema): ?string
    {
        return match (true) {
            isset($schema['properties']), isset($schema['additionalProperties']), isset($schema['required']) => 'object',
            isset($schema['items']), isset($schema['minItems']), isset($schema['maxItems']), isset($schema['uniqueItems']) => 'array',
            isset($schema['enum']) && is_array($schema['enum']) => $this->inferEnumType($schema['enum']),
            isset($schema['minLength']), isset($schema['maxLength']), isset($schema['pattern']), isset($schema['format']) => 'string',
            isset($schema['minimum']), isset($schema['maximum']), isset($schema['multipleOf']) => 'number',
            default => null,
        };
    }

    /**
     * Infer the scalar type shared by a homogeneous enum of scalars.
     *
     * @param  array<int, mixed>  $enum
     */
    protected function inferEnumType(array $enum): ?string
    {
        $resolved = null;

        foreach ($enum as $value) {
            $current = match (true) {
                is_bool($value) => 'boolean',
                is_int($value) => 'integer',
                is_float($value) => 'number',
                is_string($value) => 'string',
                default => null,
            };

            if ($current === null) {
                return null;
            }

            if ($resolved === null || $resolved === $current) {
                $resolved = $current;

                continue;
            }

            // A mix of integers and floats is still numeric; anything else is ambiguous...
            if (in_array($resolved, ['integer', 'number'], true) && in_array($current, ['integer', 'number'], true)) {
                $resolved = 'number';

                continue;
            }

            return null;
        }

        return $resolved;
    }

    /**
     * Collapse "anyOf" / "oneOf" null branches into a single effective schema.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<int, string>  $refs
     * @return array{0: array<string, mixed>, 1: bool, 2: array<int, string>}
     *
     * @throws \InvalidArgumentException
     */
    protected function normalizeUnions(array $schema, array $refs = []): array
    {
        foreach (['anyOf', 'oneOf'] as $key) {
            if (! isset($schema[$key]) || ! is_array($schema[$key])) {
                continue;
            }

            $nullable = false;
            $branches = [];

            foreach ($schema[$key] as $branch) {
                if (! is_array($branch)) {
                    continue;
                }

                [$branch, $branchRefs] = $this->resolveRef($branch, $refs);

                if ($this->isNullBranch($branch)) {
                    $nullable = true;
                } else {
                    $branches[] = [$branch, $branchRefs];
                }
            }

            if (! $nullable || count($branches) !== 1) {
                throw new InvalidArgumentException(
                    "Only a nullable \"{$key}\" (a single schema plus a \"null\" branch) is supported."
                );
            }

            [$branch, $branchRefs] = $branches[0];

            $siblings = $schema;
            unset($siblings[$key]);

            foreach ($siblings as $siblingKey => $value) {
                if (array_key_exists($siblingKey, $branch) && $branch[$siblingKey] !== $value) {
                    throw new InvalidArgumentException(
                        "Conflicting [{$siblingKey}] between a \"{$key}\" branch and its sibling keys."
                    );
                }
            }

            return [array_merge($siblings, $branch), true, $branchRefs];
        }

        return [$schema, false, $refs];
    }

    /**
     * Determine if the given schema branch describes only the "null" type.
     *
     * @param  array<string, mixed>  $branch
     */
    protected function isNullBranch(array $branch): bool
    {
        $type = $branch['type'] ?? null;

        return $type === 'null' || $type === ['null'];
    }

    /**
     * Resolve a local "$ref" against the root schema, merging sibling keys.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<int, string>  $refs
     * @return array{0: array<string, mixed>, 1: array<int, string>}
     *
     * @throws \InvalidArgumentException
     */
    protected function resolveRef(array $schema, array $refs = []): array
    {
        if (! isset($schema['$ref']) || ! is_string($schema['$ref'])) {
            return [$schema, $refs];
        }

        $ref = $schema['$ref'];

        if (in_array($ref, $refs, true)) {
            throw new InvalidArgumentException("Circular JSON Schema \$ref [{$ref}] detected.");
        }

        $refs[] = $ref;

        $resolved = $this->lookupRef($ref);

        $siblings = $schema;
        unset($siblings['$ref']);

        return $this->resolveRef(array_merge($resolved, $siblings), $refs);
    }

    /**
     * Look up a local JSON pointer reference within the root schema.
     *
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     */
    protected function lookupRef(string $ref): array
    {
        if (isset($this->refCache[$ref])) {
            return $this->refCache[$ref];
        }

        if ($ref === '#') {
            return $this->refCache[$ref] = $this->root;
        }

        if (! str_starts_with($ref, '#/')) {
            throw new InvalidArgumentException("Unable to resolve non-local JSON Schema \$ref [{$ref}].");
        }

        $target = $this->root;

        foreach (explode('/', substr($ref, 2)) as $segment) {
            $segment = str_replace(['~1', '~0'], ['/', '~'], rawurldecode($segment));

            if (! is_array($target) || ! array_key_exists($segment, $target)) {
                throw new InvalidArgumentException("Unable to resolve JSON Schema \$ref [{$ref}].");
            }

            $target = $target[$segment];
        }

        if (! is_array($target)) {
            throw new InvalidArgumentException("The JSON Schema \$ref [{$ref}] does not point to a schema.");
        }

        return $this->refCache[$ref] = $target;
    }

    /**
     * Normalize the given value to an integer or float, or null when not numeric.
     */
    protected function toNumber(mixed $value): int|float|null
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return $value + 0;
        }

        return null;
    }

    /**
     * Cast the given number to an integer, rejecting non-integer values.
     *
     * @throws \InvalidArgumentException
     */
    protected function toInteger(int|float $value): int
    {
        if (is_float($value) && floor($value) !== $value) {
            throw new InvalidArgumentException("The JSON Schema integer constraint [{$value}] must be an integer.");
        }

        return (int) $value;
    }
}
