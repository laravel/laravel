<?php

namespace Illuminate\Database\Eloquent\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\BinaryCodec;
use InvalidArgumentException;

class AsBinary implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array{string}  $arguments
     * @return \Illuminate\Contracts\Database\Eloquent\CastsAttributes
     */
    public static function castUsing(array $arguments)
    {
        return new class($arguments) implements CastsAttributes
        {
            protected string $format;

            public function __construct(protected array $arguments)
            {
                $this->format = $this->arguments[0]
                    ?? throw new InvalidArgumentException('The binary codec format is required.');

                if (! in_array($this->format, BinaryCodec::formats(), true)) {
                    throw new InvalidArgumentException(sprintf(
                        'Unsupported binary codec format [%s]. Allowed formats are: %s.',
                        $this->format,
                        implode(', ', BinaryCodec::formats()),
                    ));
                }
            }

            public function get($model, $key, $value, $attributes)
            {
                return BinaryCodec::decode($attributes[$key] ?? null, $this->format);
            }

            public function set($model, $key, $value, $attributes)
            {
                return [$key => BinaryCodec::encode($value, $this->format)];
            }
        };
    }

    /**
     * Encode / decode values as binary UUIDs.
     */
    public static function uuid(): string
    {
        return self::class.':uuid';
    }

    /**
     * Encode / decode values as binary ULIDs.
     */
    public static function ulid(): string
    {
        return self::class.':ulid';
    }

    /**
     * Encode / decode values using the given format.
     */
    public static function of(string $format): string
    {
        return self::class.':'.$format;
    }
}
