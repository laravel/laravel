<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Compose stream implementations based on a hash of callables.
 *
 * Allows for easy testing and extension of a provided stream without needing
 * to create a concrete class for a simple extension point.
 */
#[\AllowDynamicProperties]
final class FnStream implements StreamInterface
{
    private const SLOTS = [
        '__toString', 'close', 'detach', 'rewind',
        'getSize', 'tell', 'eof', 'isSeekable', 'seek', 'isWritable', 'write',
        'isReadable', 'read', 'getContents', 'getMetadata',
    ];

    /** @var array<string, callable> */
    private $methods;

    /**
     * @param array<string, callable> $methods Hash of method name to a callable.
     */
    public function __construct(array $methods)
    {
        $this->methods = $methods;

        // Create the callables on the class
        foreach ($methods as $name => $fn) {
            $this->{'_fn_'.$name} = $fn;
        }
    }

    /**
     * Lazily determine which methods are not implemented.
     *
     * @throws \BadMethodCallException
     */
    public function __get(string $name): void
    {
        throw new \BadMethodCallException(str_replace('_fn_', '', $name)
            .'() is not implemented in the FnStream');
    }

    /**
     * The close method is called on the underlying stream only if possible.
     */
    public function __destruct()
    {
        if (isset($this->_fn_close)) {
            ($this->_fn_close)();
        }
    }

    /**
     * An unserialize would allow the __destruct to run when the unserialized value goes out of scope.
     *
     * @throws \LogicException
     */
    public function __wakeup(): void
    {
        throw new \LogicException('FnStream should never be unserialized');
    }

    /**
     * Adds custom functionality to an underlying stream by intercepting
     * specific method calls.
     *
     * @param StreamInterface         $stream  Stream to decorate
     * @param array<string, callable> $methods Hash of method name to a callable
     *
     * @return FnStream
     */
    public static function decorate(StreamInterface $stream, array $methods)
    {
        // If any of the required methods were not provided, then simply
        // proxy to the decorated stream.
        foreach (array_diff(self::SLOTS, array_keys($methods)) as $diff) {
            /** @var callable $callable */
            $callable = [$stream, $diff];
            $methods[$diff] = $callable;
        }

        return new self($methods);
    }

    public function __toString(): string
    {
        try {
            /** @var string */
            return ($this->_fn___toString)();
        } catch (\Throwable $e) {
            if (\PHP_VERSION_ID >= 70400) {
                throw $e;
            }
            trigger_error(sprintf('%s::__toString exception: %s', self::class, (string) $e), E_USER_ERROR);

            return '';
        }
    }

    public function close(): void
    {
        ($this->_fn_close)();
    }

    public function detach()
    {
        return ($this->_fn_detach)();
    }

    public function getSize(): ?int
    {
        return ($this->_fn_getSize)();
    }

    public function tell(): int
    {
        return ($this->_fn_tell)();
    }

    public function eof(): bool
    {
        return ($this->_fn_eof)();
    }

    public function isSeekable(): bool
    {
        return ($this->_fn_isSeekable)();
    }

    public function rewind(): void
    {
        ($this->_fn_rewind)();
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!\is_int($offset)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to StreamInterface::seek() is deprecated; guzzlehttp/psr7 3.0 requires int for $offset.',
                \get_debug_type($offset)
            );
        }

        if (!\is_int($whence)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to StreamInterface::seek() is deprecated; guzzlehttp/psr7 3.0 requires int for $whence.',
                \get_debug_type($whence)
            );
        }

        ($this->_fn_seek)($offset, $whence);
    }

    public function isWritable(): bool
    {
        return ($this->_fn_isWritable)();
    }

    public function write($string): int
    {
        if (!\is_string($string)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to StreamInterface::write() is deprecated; guzzlehttp/psr7 3.0 requires string for $string.',
                \get_debug_type($string)
            );
        }

        return ($this->_fn_write)($string);
    }

    public function isReadable(): bool
    {
        return ($this->_fn_isReadable)();
    }

    public function read($length): string
    {
        if (!\is_int($length)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to StreamInterface::read() is deprecated; guzzlehttp/psr7 3.0 requires int for $length.',
                \get_debug_type($length)
            );
        }

        return ($this->_fn_read)($length);
    }

    public function getContents(): string
    {
        return ($this->_fn_getContents)();
    }

    /**
     * @return mixed
     */
    public function getMetadata($key = null)
    {
        if ($key !== null && !\is_string($key)) {
            \trigger_deprecation(
                'guzzlehttp/psr7',
                '2.11',
                'Passing %s to StreamInterface::getMetadata() is deprecated; guzzlehttp/psr7 3.0 requires string|null for $key.',
                \get_debug_type($key)
            );
        }

        return ($this->_fn_getMetadata)($key);
    }
}
