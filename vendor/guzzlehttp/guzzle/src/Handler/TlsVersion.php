<?php

namespace GuzzleHttp\Handler;

/**
 * @internal
 */
final class TlsVersion
{
    /**
     * @param mixed $value
     */
    public static function ordinal(string $option, $value): int
    {
        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT) {
            return 10;
        }
        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT) {
            return 11;
        }
        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT) {
            return 12;
        }
        if (\defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT') && $value === \STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT) {
            return 13;
        }

        throw new \InvalidArgumentException(\sprintf('Invalid %s request option: unknown version provided', $option));
    }

    /**
     * @param mixed $min
     * @param mixed $max
     */
    public static function assertRange($min, $max): void
    {
        if ($min === null || $max === null) {
            return;
        }

        if (self::ordinal('crypto_method_max', $max) < self::ordinal('crypto_method', $min)) {
            throw new \InvalidArgumentException('Invalid crypto_method_max request option: maximum TLS version must be greater than or equal to crypto_method');
        }
    }

    /**
     * @param mixed $value
     */
    public static function streamProtocolVersion(string $option, $value): int
    {
        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT) {
            return self::requireStreamProto('STREAM_CRYPTO_PROTO_TLSv1_0', $option);
        }
        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT) {
            return self::requireStreamProto('STREAM_CRYPTO_PROTO_TLSv1_1', $option);
        }
        if ($value === \STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT) {
            return self::requireStreamProto('STREAM_CRYPTO_PROTO_TLSv1_2', $option);
        }
        if (\defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT') && $value === \STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT) {
            return self::requireStreamProto('STREAM_CRYPTO_PROTO_TLSv1_3', $option);
        }

        throw new \InvalidArgumentException(\sprintf('Invalid %s request option: unknown version provided', $option));
    }

    /**
     * Resolves a STREAM_CRYPTO_PROTO_* constant. The ssl.max_proto_version
     * context option and these constants were added in PHP 7.3.0 (TLS 1.3 in
     * 7.4.0); on older runtimes the option cannot be honored, so reject loudly.
     */
    private static function requireStreamProto(string $constant, string $option): int
    {
        if (\defined($constant)) {
            /** @var int */
            return \constant($constant);
        }

        throw new \InvalidArgumentException(\sprintf(
            'Invalid %s request option: maximum TLS version control is not supported by your version of PHP',
            $option
        ));
    }
}
