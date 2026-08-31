<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Output;

use function assert;
use function count;
use function dirname;
use function explode;
use function fclose;
use function fopen;
use function fsockopen;
use function fwrite;
use function str_replace;
use function str_starts_with;
use PHPUnit\Runner\DirectoryDoesNotExistException;
use PHPUnit\TextUI\CannotOpenSocketException;
use PHPUnit\TextUI\InvalidSocketException;
use PHPUnit\Util\Filesystem;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class DefaultPrinter implements Printer
{
    /**
     * @var closed-resource|resource
     */
    private $stream;
    private readonly bool $isPhpStream;
    private bool $isOpen;

    /**
     * @throws CannotOpenSocketException
     * @throws DirectoryDoesNotExistException
     * @throws InvalidSocketException
     */
    public static function from(string $out): self
    {
        return new self($out);
    }

    /**
     * @throws CannotOpenSocketException
     * @throws DirectoryDoesNotExistException
     * @throws InvalidSocketException
     */
    public static function standardOutput(): self
    {
        return new self('php://stdout');
    }

    /**
     * @throws CannotOpenSocketException
     * @throws DirectoryDoesNotExistException
     * @throws InvalidSocketException
     */
    public static function standardError(): self
    {
        return new self('php://stderr');
    }

    /**
     * @throws CannotOpenSocketException
     * @throws DirectoryDoesNotExistException
     * @throws InvalidSocketException
     */
    private function __construct(string $out)
    {
        $this->isPhpStream = str_starts_with($out, 'php://');

        if (str_starts_with($out, 'socket://')) {
            $tmp = explode(':', str_replace('socket://', '', $out));

            if (count($tmp) !== 2) {
                throw new InvalidSocketException($out);
            }

            $stream = @fsockopen($tmp[0], (int) $tmp[1]);

            if ($stream === false) {
                throw new CannotOpenSocketException($tmp[0], (int) $tmp[1]);
            }

            $this->stream = $stream;
            $this->isOpen = true;

            return;
        }

        if (!$this->isPhpStream && !Filesystem::createDirectory(dirname($out))) {
            throw new DirectoryDoesNotExistException(dirname($out));
        }

        $stream = fopen($out, 'wb');

        assert($stream !== false);

        $this->stream = $stream;
        $this->isOpen = true;
    }

    public function print(string $buffer): void
    {
        assert($this->isOpen);

        fwrite($this->stream, $buffer);
    }

    public function flush(): void
    {
        if ($this->isOpen && $this->isPhpStream) {
            fclose($this->stream);

            $this->isOpen = false;
        }
    }
}
