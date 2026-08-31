<?php

namespace Illuminate\JsonSchema;

use Closure;
use Illuminate\Contracts\JsonSchema\JsonSchema as JsonSchemaContract;

class JsonSchemaTypeFactory extends JsonSchema implements JsonSchemaContract
{
    /**
     * Create a new object schema instance.
     *
     * @param  (Closure(JsonSchemaTypeFactory): array<string, Types\Type>)|array<string, Types\Type>  $properties
     */
    public function object(Closure|array $properties = []): Types\ObjectType
    {
        if ($properties instanceof Closure) {
            $properties = $properties($this);
        }

        return new Types\ObjectType($properties);
    }

    /**
     * Create a new array property instance.
     */
    public function array(): Types\ArrayType
    {
        return new Types\ArrayType;
    }

    /**
     * Create a new string property instance.
     */
    public function string(): Types\StringType
    {
        return new Types\StringType;
    }

    /**
     * Create a new integer property instance.
     */
    public function integer(): Types\IntegerType
    {
        return new Types\IntegerType;
    }

    /**
     * Create a new number property instance.
     */
    public function number(): Types\NumberType
    {
        return new Types\NumberType;
    }

    /**
     * Create a new boolean property instance.
     */
    public function boolean(): Types\BooleanType
    {
        return new Types\BooleanType;
    }

    /**
     * Create a new multi-type union instance.
     *
     * @param  array<int, string>  $types
     */
    public function union(array $types): Types\UnionType
    {
        return new Types\UnionType($types);
    }
}
