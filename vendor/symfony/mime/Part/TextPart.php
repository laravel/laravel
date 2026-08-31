<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Part;

use Symfony\Component\Mime\Encoder\Base64ContentEncoder;
use Symfony\Component\Mime\Encoder\ContentEncoderInterface;
use Symfony\Component\Mime\Encoder\EightBitContentEncoder;
use Symfony\Component\Mime\Encoder\QpContentEncoder;
use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Header\Headers;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TextPart extends AbstractPart
{
    private const DEFAULT_ENCODERS = ['quoted-printable', 'base64', '8bit'];

    /** @internal, to be removed in 8.0 */
    protected Headers $_headers;

    private static array $encoders = [];

    /** @var resource|string|File */
    private $body;
    private ?string $charset;
    private string $subtype;
    private ?string $disposition = null;
    private ?string $name = null;
    private string $encoding;
    private ?bool $seekable = null;

    /**
     * @param resource|string|File $body Use a File instance to defer loading the file until rendering
     */
    public function __construct($body, ?string $charset = 'utf-8', string $subtype = 'plain', ?string $encoding = null)
    {
        parent::__construct();

        if (!\is_string($body) && !\is_resource($body) && !$body instanceof File) {
            throw new \TypeError(\sprintf('The body of "%s" must be a string, a resource, or an instance of "%s" (got "%s").', self::class, File::class, get_debug_type($body)));
        }

        if ($body instanceof File) {
            $path = $body->getPath();
            if ((is_file($path) && !is_readable($path)) || is_dir($path)) {
                throw new InvalidArgumentException(\sprintf('Path "%s" is not readable.', $path));
            }
        }

        $this->body = $body;
        $this->charset = $charset;
        $this->subtype = $subtype;
        $this->seekable = \is_resource($body) ? stream_get_meta_data($body)['seekable'] && 0 === fseek($body, 0, \SEEK_CUR) : null;

        if (null === $encoding) {
            $this->encoding = $this->chooseEncoding();
        } else {
            if (!\in_array($encoding, self::DEFAULT_ENCODERS, true) && !\array_key_exists($encoding, self::$encoders)) {
                throw new InvalidArgumentException(\sprintf('The encoding must be one of "%s" ("%s" given).', implode('", "', array_unique(array_merge(self::DEFAULT_ENCODERS, array_keys(self::$encoders)))), $encoding));
            }
            $this->encoding = $encoding;
        }
    }

    public function getMediaType(): string
    {
        return 'text';
    }

    public function getMediaSubtype(): string
    {
        return $this->subtype;
    }

    /**
     * @param string $disposition one of attachment, inline, or form-data
     *
     * @return $this
     */
    public function setDisposition(string $disposition): static
    {
        $this->disposition = $disposition;

        return $this;
    }

    /**
     * @return ?string null or one of attachment, inline, or form-data
     */
    public function getDisposition(): ?string
    {
        return $this->disposition;
    }

    /**
     * Sets the name of the file (used by FormDataPart).
     *
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the name of the file.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function getBody(): string
    {
        if ($this->body instanceof File) {
            if (false === $ret = @file_get_contents($this->body->getPath())) {
                throw new InvalidArgumentException(error_get_last()['message']);
            }

            return $ret;
        }

        if (null === $this->seekable) {
            return $this->body;
        }

        if ($this->seekable) {
            rewind($this->body);
        }

        return stream_get_contents($this->body) ?: '';
    }

    public function bodyToString(): string
    {
        return $this->getEncoder()->encodeString($this->getBody(), $this->charset);
    }

    public function bodyToIterable(): iterable
    {
        if ($this->body instanceof File) {
            $path = $this->body->getPath();
            if (false === $handle = @fopen($path, 'r', false)) {
                throw new InvalidArgumentException(\sprintf('Unable to open path "%s".', $path));
            }

            yield from $this->getEncoder()->encodeByteStream($handle);
        } elseif (null !== $this->seekable) {
            if ($this->seekable) {
                rewind($this->body);
            }
            yield from $this->getEncoder()->encodeByteStream($this->body);
        } else {
            yield $this->getEncoder()->encodeString($this->body);
        }
    }

    public function getPreparedHeaders(): Headers
    {
        $headers = parent::getPreparedHeaders();

        $headers->setHeaderBody('Parameterized', 'Content-Type', $this->getMediaType().'/'.$this->getMediaSubtype());
        if ($this->charset) {
            $headers->setHeaderParameter('Content-Type', 'charset', $this->charset);
        }
        if ($this->name && 'form-data' !== $this->disposition) {
            $headers->setHeaderParameter('Content-Type', 'name', $this->name);
        }
        $headers->setHeaderBody('Text', 'Content-Transfer-Encoding', $this->encoding);

        if (!$headers->has('Content-Disposition') && null !== $this->disposition) {
            $headers->setHeaderBody('Parameterized', 'Content-Disposition', $this->disposition);
            if ($this->name) {
                $headers->setHeaderParameter('Content-Disposition', 'name', $this->name);
            }
        }

        return $headers;
    }

    public function asDebugString(): string
    {
        $str = parent::asDebugString();
        if (null !== $this->charset) {
            $str .= ' charset: '.$this->charset;
        }
        if (null !== $this->disposition) {
            $str .= ' disposition: '.$this->disposition;
        }

        return $str;
    }

    private function getEncoder(): ContentEncoderInterface
    {
        if ('8bit' === $this->encoding) {
            return self::$encoders[$this->encoding] ??= new EightBitContentEncoder();
        }

        if ('quoted-printable' === $this->encoding) {
            return self::$encoders[$this->encoding] ??= new QpContentEncoder();
        }

        if ('base64' === $this->encoding) {
            return self::$encoders[$this->encoding] ??= new Base64ContentEncoder();
        }

        return self::$encoders[$this->encoding];
    }

    public static function addEncoder(ContentEncoderInterface $encoder): void
    {
        if (\in_array($encoder->getName(), self::DEFAULT_ENCODERS, true)) {
            throw new InvalidArgumentException('You are not allowed to change the default encoders ("quoted-printable", "base64", and "8bit").');
        }

        self::$encoders[$encoder->getName()] = $encoder;
    }

    private function chooseEncoding(): string
    {
        if (null === $this->charset) {
            return 'base64';
        }

        return 'quoted-printable';
    }

    public function __serialize(): array
    {
        if (self::class === (new \ReflectionMethod($this, '__sleep'))->class || self::class !== (new \ReflectionMethod($this, '__serialize'))->class) {
            // convert resources to strings for serialization
            if (null !== $this->seekable) {
                $this->body = $this->getBody();
                $this->seekable = null;
            }

            return [
                '_headers' => $this->getHeaders(),
                'body' => $this->body,
                'charset' => $this->charset,
                'subtype' => $this->subtype,
                'disposition' => $this->disposition,
                'name' => $this->name,
                'encoding' => $this->encoding,
            ];
        }

        trigger_deprecation('symfony/mime', '7.4', 'Implementing "%s::__sleep()" is deprecated, use "__serialize()" instead.', get_debug_type($this));

        $data = [];
        foreach ($this->__sleep() as $key) {
            try {
                if (($r = new \ReflectionProperty($this, $key))->isInitialized($this)) {
                    $data[$key] = $r->getValue($this);
                }
            } catch (\ReflectionException) {
                $data[$key] = $this->$key;
            }
        }

        return $data;
    }

    public function __unserialize(array $data): void
    {
        foreach (['charset', 'subtype', 'disposition', 'name', 'encoding'] as $prop) {
            if (($data[$prop] ?? $data["\0".self::class."\0".$prop] ?? $data["\0*\0".$prop] ?? null) instanceof \Stringable) {
                throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
            }
        }

        if ($wakeup = self::class !== (new \ReflectionMethod($this, '__wakeup'))->class && self::class === (new \ReflectionMethod($this, '__unserialize'))->class) {
            trigger_deprecation('symfony/mime', '7.4', 'Implementing "%s::__wakeup()" is deprecated, use "__unserialize()" instead.', get_debug_type($this));
        }

        if ($headers = $data['_headers'] ?? $data["\0*\0_headers"] ?? null) {
            unset($data['_headers'], $data["\0*\0_headers"]);
            parent::__unserialize(['headers' => $headers]);
        }

        if (['body', 'charset', 'subtype', 'disposition', 'name', 'encoding'] === array_keys($data)) {
            parent::__unserialize(['headers' => $headers]);
            $this->body = $data['body'];
            $this->charset = $data['charset'];
            $this->subtype = $data['subtype'];
            $this->disposition = $data['disposition'];
            $this->name = $data['name'];
            $this->encoding = $data['encoding'];

            if ($wakeup) {
                $this->__wakeup();
            } elseif (!\is_string($this->body) && !$this->body instanceof File) {
                throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
            }

            return;
        }

        if (["\0".self::class."\0body", "\0".self::class."\0charset", "\0".self::class."\0subtype", "\0".self::class."\0disposition", "\0".self::class."\0name", "\0".self::class."\0encoding"] === array_keys($data)) {
            $this->body = $data["\0".self::class."\0body"];
            $this->charset = $data["\0".self::class."\0charset"];
            $this->subtype = $data["\0".self::class."\0subtype"];
            $this->disposition = $data["\0".self::class."\0disposition"];
            $this->name = $data["\0".self::class."\0name"];
            $this->encoding = $data["\0".self::class."\0encoding"];

            if ($wakeup) {
                $this->_headers = $headers;
                $this->__wakeup();
            } elseif (!\is_string($this->body) && !$this->body instanceof File) {
                throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
            }

            return;
        }

        trigger_deprecation('symfony/mime', '7.4', 'Passing extra keys to "%s::__unserialize()" is deprecated, populate properties in "%s::__unserialize()" instead.', self::class, get_debug_type($this));

        \Closure::bind(function ($data) use ($wakeup) {
            foreach ($data as $key => $value) {
                $this->{("\0" === $key[0] ?? '') ? substr($key, 1 + strrpos($key, "\0")) : $key} = $value;
            }

            if ($wakeup) {
                $this->__wakeup();
            }
        }, $this, static::class)($data);
    }

    /**
     * @deprecated since Symfony 7.4, will be replaced by `__serialize()` in 8.0
     */
    public function __sleep(): array
    {
        trigger_deprecation('symfony/mime', '7.4', 'Calling "%s::__sleep()" is deprecated, use "__serialize()" instead.', get_debug_type($this));

        // convert resources to strings for serialization
        if (null !== $this->seekable) {
            $this->body = $this->getBody();
            $this->seekable = null;
        }

        $this->_headers = $this->getHeaders();

        return ['_headers', 'body', 'charset', 'subtype', 'disposition', 'name', 'encoding'];
    }

    /**
     * @deprecated since Symfony 7.4, will be replaced by `__unserialize()` in 8.0
     */
    public function __wakeup(): void
    {
        trigger_deprecation('symfony/mime', '7.4', 'Calling "%s::__wakeup()" is deprecated, use "__unserialize()" instead.', get_debug_type($this));

        $r = new \ReflectionProperty(AbstractPart::class, 'headers');
        $r->setValue($this, $this->_headers);
        unset($this->_headers);
    }
}
