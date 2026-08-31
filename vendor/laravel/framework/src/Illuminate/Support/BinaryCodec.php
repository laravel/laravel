<?php

namespace Illuminate\Support;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Uid\Ulid;

class BinaryCodec
{
    /** @var array<string, array{encode: callable(UuidInterface|Ulid|string|null): ?string, decode: callable(?string): ?string}> */
    protected static array $customCodecs = [];

    /**
     * Register a custom codec.
     */
    public static function register(string $name, callable $encode, callable $decode): void
    {
        self::$customCodecs[$name] = [
            'encode' => $encode,
            'decode' => $decode,
        ];
    }

    /**
     * Encode a value to binary.
     */
    public static function encode(UuidInterface|Ulid|string|null $value, string $format): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (isset(self::$customCodecs[$format])) {
            return (self::$customCodecs[$format]['encode'])($value);
        }

        return match ($format) {
            'uuid' => match (true) {
                $value instanceof UuidInterface => $value->getBytes(),
                self::isBinary($value) => $value,
                default => Uuid::fromString($value)->getBytes(),
            },
            'ulid' => match (true) {
                $value instanceof Ulid => $value->toBinary(),
                self::isBinary($value) => $value,
                default => Ulid::fromString($value)->toBinary(),
            },
            default => throw new InvalidArgumentException("Format [$format] is invalid."),
        };
    }

    /**
     * Decode a binary value to string.
     */
    public static function decode(?string $value, string $format): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (isset(self::$customCodecs[$format])) {
            return (self::$customCodecs[$format]['decode'])($value);
        }

        return match ($format) {
            'uuid' => (self::isBinary($value) ? Uuid::fromBytes($value) : Uuid::fromString($value))->toString(),
            'ulid' => (self::isBinary($value) ? Ulid::fromBinary($value) : Ulid::fromString($value))->toString(),
            default => throw new InvalidArgumentException("Format [$format] is invalid."),
        };
    }

    /**
     * Get all available format names.
     *
     * @return list<string>
     */
    public static function formats(): array
    {
        return array_unique([...['uuid', 'ulid'], ...array_keys(self::$customCodecs)]);
    }

    /**
     * Determine if the given value is binary data.
     */
    public static function isBinary(mixed $value): bool
    {
        if (! is_string($value) || $value === '') {
            return false;
        }

        if (str_contains($value, "\0")) {
            return true;
        }

        return ! mb_check_encoding($value, 'UTF-8');
    }
}
