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

use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Header\Headers;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DataPart extends TextPart
{
    /** @internal, to be removed in 8.0 */
    protected array $_parent;

    private ?string $filename = null;
    private string $mediaType;
    private ?string $cid = null;

    /**
     * @param resource|string|File $body Use a File instance to defer loading the file until rendering
     */
    public function __construct($body, ?string $filename = null, ?string $contentType = null, ?string $encoding = null)
    {
        if ($body instanceof File && !$filename) {
            $filename = $body->getFilename();
        }

        $contentType ??= $body instanceof File ? $body->getContentType() : 'application/octet-stream';
        [$this->mediaType, $subtype] = explode('/', $contentType);

        parent::__construct($body, null, $subtype, $encoding);

        if (null !== $filename) {
            $this->filename = $filename;
            $this->setName($filename);
        }
        $this->setDisposition('attachment');
    }

    public static function fromPath(string $path, ?string $name = null, ?string $contentType = null): self
    {
        return new self(new File($path), $name, $contentType);
    }

    /**
     * @return $this
     */
    public function asInline(): static
    {
        return $this->setDisposition('inline');
    }

    /**
     * @return $this
     */
    public function setContentId(string $cid): static
    {
        if (!str_contains($cid, '@')) {
            throw new InvalidArgumentException(\sprintf('The "%s" CID is invalid as it doesn\'t contain an "@".', $cid));
        }

        $this->cid = $cid;

        return $this;
    }

    public function getContentId(): string
    {
        return $this->cid ?: $this->cid = $this->generateContentId();
    }

    public function hasContentId(): bool
    {
        return null !== $this->cid;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function getPreparedHeaders(): Headers
    {
        $headers = parent::getPreparedHeaders();

        if (null !== $this->cid) {
            $headers->setHeaderBody('Id', 'Content-ID', $this->cid);
        }

        if (null !== $this->filename) {
            $headers->setHeaderParameter('Content-Disposition', 'filename', $this->filename);
        }

        return $headers;
    }

    public function asDebugString(): string
    {
        $str = parent::asDebugString();
        if (null !== $this->filename) {
            $str .= ' filename: '.$this->filename;
        }

        return $str;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getContentType(): string
    {
        return implode('/', [$this->getMediaType(), $this->getMediaSubtype()]);
    }

    private function generateContentId(): string
    {
        return bin2hex(random_bytes(16)).'@symfony';
    }

    public function __serialize(): array
    {
        if (self::class === (new \ReflectionMethod($this, '__sleep'))->class || self::class !== (new \ReflectionMethod($this, '__serialize'))->class) {
            $parent = parent::__serialize();
            $headers = $parent['_headers'];
            unset($parent['_headers']);

            return [
                '_headers' => $headers,
                '_parent' => $parent,
                'filename' => $this->filename,
                'mediaType' => $this->mediaType,
                'cid' => $this->cid,
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
        if ($wakeup = self::class !== (new \ReflectionMethod($this, '__wakeup'))->class && self::class === (new \ReflectionMethod($this, '__unserialize'))->class) {
            trigger_deprecation('symfony/mime', '7.4', 'Implementing "%s::__wakeup()" is deprecated, use "__unserialize()" instead.', get_debug_type($this));
        }

        if (['_headers', '_parent', 'filename', 'mediaType'] === array_keys($data) || ['_headers', '_parent', 'filename', 'mediaType', 'cid'] === array_keys($data)) {
            parent::__unserialize(['_headers' => $data['_headers'], ...$data['_parent']]);
            $this->filename = $data['filename'];
            $this->mediaType = $data['mediaType'];
            $this->cid = $data['cid'] ?? null;

            if ($wakeup) {
                $this->__wakeup();
            }

            return;
        }

        if (["\0*\0_headers", "\0*\0_parent", "\0".self::class."\0filename", "\0".self::class."\0mediaType"] === array_keys($data)) {
            parent::__unserialize(['_headers' => $data["\0*\0_headers"], ...$data["\0*\0_parent"]]);
            $this->filename = $data["\0".self::class."\0filename"];
            $this->mediaType = $data["\0".self::class."\0mediaType"];

            if ($wakeup) {
                $this->__wakeup();
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
        // converts the body to a string
        parent::__sleep();

        $this->_parent = [];
        foreach (['body', 'charset', 'subtype', 'disposition', 'name', 'encoding'] as $name) {
            $r = new \ReflectionProperty(TextPart::class, $name);
            $this->_parent[$name] = $r->getValue($this);
        }
        $this->_headers = $this->getHeaders();

        return ['_headers', '_parent', 'filename', 'mediaType', 'cid'];
    }

    /**
     * @deprecated since Symfony 7.4, will be replaced by `__unserialize()` in 8.0
     */
    public function __wakeup(): void
    {
        $r = new \ReflectionProperty(AbstractPart::class, 'headers');
        $r->setValue($this, $this->_headers);
        unset($this->_headers);

        if (!\is_array($this->_parent)) {
            throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
        }
        foreach (['body', 'charset', 'subtype', 'disposition', 'name', 'encoding'] as $name) {
            if (null !== $this->_parent[$name] && !\is_string($this->_parent[$name]) && !$this->_parent[$name] instanceof File) {
                throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
            }
            $r = new \ReflectionProperty(TextPart::class, $name);
            $r->setValue($this, $this->_parent[$name]);
        }
        unset($this->_parent);
    }
}
