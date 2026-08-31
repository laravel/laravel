<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Stream decorator that begins dropping data once the size of the underlying
 * stream becomes too full.
 */
final class DroppingStream implements StreamInterface
{
    use StreamDecoratorTrait;

    /** @var int */
    private $maxLength;

    /** @var StreamInterface */
    private $stream;

    /**
     * @param StreamInterface $stream    Underlying stream to decorate.
     * @param int             $maxLength Maximum size before dropping data.
     */
    public function __construct(StreamInterface $stream, int $maxLength)
    {
        $this->stream = $stream;
        $this->maxLength = $maxLength;
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

        $diff = $this->maxLength - $this->stream->getSize();

        // Begin returning 0 when the underlying stream is too large.
        if ($diff <= 0) {
            return 0;
        }

        // Write the stream or a subset of the stream if needed.
        if (strlen($string) < $diff) {
            return $this->stream->write($string);
        }

        return $this->stream->write(substr($string, 0, $diff));
    }
}
