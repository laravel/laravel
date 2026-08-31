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

use Symfony\Component\Mime\Header\Headers;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class SMimePart extends AbstractPart
{
    /** @internal, to be removed in 8.0 */
    protected Headers $_headers;

    public function __construct(
        private iterable|string $body,
        private string $type,
        private string $subtype,
        private array $parameters,
    ) {
        parent::__construct();
    }

    public function getMediaType(): string
    {
        return $this->type;
    }

    public function getMediaSubtype(): string
    {
        return $this->subtype;
    }

    public function bodyToString(): string
    {
        if (\is_string($this->body)) {
            return $this->body;
        }

        $body = '';
        foreach ($this->body as $chunk) {
            $body .= $chunk;
        }
        $this->body = $body;

        return $body;
    }

    public function bodyToIterable(): iterable
    {
        if (\is_string($this->body)) {
            yield $this->body;

            return;
        }

        $body = '';
        foreach ($this->body as $chunk) {
            $body .= $chunk;
            yield $chunk;
        }
        $this->body = $body;
    }

    public function getPreparedHeaders(): Headers
    {
        $headers = clone parent::getHeaders();

        $headers->setHeaderBody('Parameterized', 'Content-Type', $this->getMediaType().'/'.$this->getMediaSubtype());

        foreach ($this->parameters as $name => $value) {
            $headers->setHeaderParameter('Content-Type', $name, $value);
        }

        return $headers;
    }

    public function __serialize(): array
    {
        if (self::class === (new \ReflectionMethod($this, '__sleep'))->class || self::class !== (new \ReflectionMethod($this, '__serialize'))->class) {
            // convert iterables to strings for serialization
            if (is_iterable($this->body)) {
                $this->body = $this->bodyToString();
            }

            return [
                '_headers' => $this->getHeaders(),
                'body' => $this->body,
                'type' => $this->type,
                'subtype' => $this->subtype,
                'parameters' => $this->parameters,
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
        foreach (['body', 'type', 'subtype'] as $prop) {
            if (($data[$prop] ?? $data["\0".self::class."\0".$prop] ?? $data["\0*\0".$prop] ?? null) instanceof \Stringable) {
                throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
            }
        }

        if ($wakeup = self::class !== (new \ReflectionMethod($this, '__wakeup'))->class && self::class === (new \ReflectionMethod($this, '__unserialize'))->class) {
            trigger_deprecation('symfony/mime', '7.4', 'Implementing "%s::__wakeup()" is deprecated, use "__unserialize()" instead.', get_debug_type($this));
        }

        if (['_headers', 'body', 'type', 'subtype', 'parameters'] === array_keys($data)) {
            parent::__unserialize(['headers' => $data['_headers']]);
            $this->body = $data['body'];
            $this->type = $data['type'];
            $this->subtype = $data['subtype'];
            $this->parameters = $data['parameters'];

            if ($wakeup) {
                $this->__wakeup();
            }

            return;
        }

        $p = "\0".self::class."\0";
        if (["\0*\0_headers", $p.'body', $p.'type', $p.'subtype', $p.'parameters'] === array_keys($data)) {
            $r = new \ReflectionProperty(parent::class, 'headers');
            $r->setValue($this, $data["\0*\0_headers"]);

            $this->body = $data[$p.'body'];
            $this->type = $data[$p.'type'];
            $this->subtype = $data[$p.'subtype'];
            $this->parameters = $data[$p.'parameters'];

            if ($wakeup) {
                $this->_headers = $data["\0*\0_headers"];
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

        // convert iterables to strings for serialization
        if (is_iterable($this->body)) {
            $this->body = $this->bodyToString();
        }

        $this->_headers = $this->getHeaders();

        return ['_headers', 'body', 'type', 'subtype', 'parameters'];
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
