<?php

namespace GuzzleHttp\Handler;

use GuzzleHttp\TransportSharing;

/**
 * @internal
 */
final class CurlShareHandleState
{
    /**
     * @var resource|\CurlShareHandle|null
     */
    public $handle;

    /**
     * @var string
     */
    public $mode;

    /**
     * @param resource|\CurlShareHandle|null $handle
     */
    private function __construct(string $mode, $handle)
    {
        $this->mode = $mode;
        $this->handle = $handle;
    }

    /**
     * @param mixed $sharing
     */
    public static function fromOption($sharing): ?self
    {
        if ($sharing instanceof self) {
            return $sharing;
        }

        $mode = self::normalizeMode($sharing, 'transport_sharing');
        if ($mode === TransportSharing::NONE) {
            return null;
        }

        if ($mode === TransportSharing::HANDLER_PREFER) {
            return self::createHandlerShareOrNull($mode);
        }

        return self::createHandlerShare($mode);
    }

    /**
     * @param mixed $sharing
     */
    public static function normalizeMode($sharing, string $option): string
    {
        if ($sharing instanceof self) {
            return $sharing->mode;
        }

        if ($sharing === null || $sharing === TransportSharing::NONE) {
            return TransportSharing::NONE;
        }

        if ($sharing === TransportSharing::HANDLER_PREFER || $sharing === TransportSharing::HANDLER_REQUIRE) {
            return $sharing;
        }

        throw new \InvalidArgumentException(\sprintf(
            'The "%s" option must be null or a GuzzleHttp\\TransportSharing::* constant; received %s.',
            $option,
            \get_debug_type($sharing)
        ));
    }

    public static function assertNoRequiredSharingCustomFactoryConflict(array $options, string $handlerName): void
    {
        if (!\array_key_exists('handle_factory', $options) || $options['handle_factory'] === null) {
            return;
        }

        $mode = self::normalizeMode($options['transport_sharing'] ?? null, 'transport_sharing');
        if ($mode !== TransportSharing::HANDLER_REQUIRE) {
            return;
        }

        throw new \InvalidArgumentException(\sprintf(
            'The "transport_sharing" %s option cannot require sharing with a custom "handle_factory" because Guzzle cannot ensure that the custom factory applies CURLOPT_SHARE.',
            $handlerName
        ));
    }

    private static function createHandlerShareOrNull(string $mode): ?self
    {
        try {
            return self::createHandlerShare($mode);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function createHandlerShare(string $mode): self
    {
        if (!\function_exists('curl_share_init') || !\function_exists('curl_share_setopt')) {
            throw new \InvalidArgumentException('The "transport_sharing" option requires cURL share support.');
        }

        self::requireCurlConstant('CURLOPT_SHARE');
        $shareOption = self::requireCurlConstant('CURLSHOPT_SHARE');
        $locks = self::handlerLocks($mode);
        $handle = curl_share_init();

        try {
            foreach ($locks as $lock) {
                try {
                    $success = curl_share_setopt($handle, $shareOption, $lock);
                } catch (\Throwable $e) {
                    throw new \InvalidArgumentException('Unable to configure cURL share handle: '.$e->getMessage(), 0, $e);
                }

                if (!$success) {
                    throw new \InvalidArgumentException(\sprintf('Unable to configure cURL share handle with lock data %d.', $lock));
                }
            }
        } catch (\Throwable $e) {
            self::closeHandlerShareHandleOnPhp7($handle);

            throw $e;
        }

        return new self($mode, $handle);
    }

    /**
     * @return int[]
     */
    private static function handlerLocks(string $mode): array
    {
        CurlVersion::ensureHandlerSharingSupported();

        if ($mode === TransportSharing::HANDLER_REQUIRE) {
            CurlVersion::ensureSslSessionSharingSupported();
        }

        $locks = [
            self::requireCurlConstant('CURL_LOCK_DATA_DNS'),
        ];

        if (CurlVersion::supportsSslSessionSharing()) {
            $locks[] = self::requireCurlConstant('CURL_LOCK_DATA_SSL_SESSION');
        }

        return $locks;
    }

    private static function requireCurlConstant(string $constant): int
    {
        if (!\defined($constant)) {
            throw new \InvalidArgumentException(\sprintf(
                'The "transport_sharing" option requires %s, but it is not available in the installed PHP cURL extension.',
                $constant
            ));
        }

        $value = \constant($constant);
        if (!\is_int($value)) {
            throw new \InvalidArgumentException(\sprintf('The cURL constant %s must resolve to an integer.', $constant));
        }

        return $value;
    }

    /**
     * @param resource|\CurlShareHandle $handle
     */
    private static function closeHandlerShareHandleOnPhp7($handle): void
    {
        if (\PHP_VERSION_ID < 80000 && \is_resource($handle)) {
            curl_share_close($handle);
        }
    }
}
