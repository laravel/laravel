<?php

namespace Illuminate\Database\Eloquent\Casts;

use BackedEnum;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;

use function Illuminate\Support\enum_value;

class AsEnumCollection implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @template TEnum of \UnitEnum
     *
     * @param  array{class-string<TEnum>}  $arguments
     * @return \Illuminate\Contracts\Database\Eloquent\CastsAttributes<\Illuminate\Support\Collection<array-key, TEnum>, iterable<TEnum>>
     */
    public static function castUsing(array $arguments)
    {
        return new class($arguments) implements CastsAttributes
        {
            protected $arguments;

            public function __construct(array $arguments)
            {
                $this->arguments = $arguments;
            }

            public function get($model, $key, $value, $attributes)
            {
                if (! isset($attributes[$key])) {
                    return;
                }

                $data = Json::decode($attributes[$key]);

                if (! is_array($data)) {
                    return;
                }

                $enumClass = $this->arguments[0];

                return (new Collection($data))->map(function ($value) use ($enumClass) {
                    return is_subclass_of($enumClass, BackedEnum::class)
                        ? $enumClass::from($value)
                        : constant($enumClass.'::'.$value);
                });
            }

            public function set($model, $key, $value, $attributes)
            {
                $value = $value !== null
                    ? Json::encode((new Collection($value))->map(function ($enum) {
                        return $this->getStorableEnumValue($enum);
                    })->jsonSerialize())
                    : null;

                return [$key => $value];
            }

            public function serialize($model, string $key, $value, array $attributes)
            {
                return (new Collection($value))
                    ->map(fn ($enum) => $this->getStorableEnumValue($enum))
                    ->toArray();
            }

            protected function getStorableEnumValue($enum)
            {
                if (is_string($enum) || is_int($enum)) {
                    return $enum;
                }

                return enum_value($enum);
            }
        };
    }

    /**
     * Specify the Enum for the cast.
     *
     * @param  class-string  $class
     * @return string
     */
    public static function of($class)
    {
        return static::class.':'.$class;
    }
}
