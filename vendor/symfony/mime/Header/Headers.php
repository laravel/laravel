<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Header;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Exception\LogicException;

/**
 * A collection of headers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class Headers
{
    private const UNIQUE_HEADERS = [
        'date', 'from', 'sender', 'reply-to', 'to', 'cc', 'bcc',
        'message-id', 'in-reply-to', 'references', 'subject',
    ];
    private const HEADER_CLASS_MAP = [
        'date' => DateHeader::class,
        'from' => MailboxListHeader::class,
        'sender' => MailboxHeader::class,
        'reply-to' => MailboxListHeader::class,
        'to' => MailboxListHeader::class,
        'cc' => MailboxListHeader::class,
        'bcc' => MailboxListHeader::class,
        'message-id' => IdentificationHeader::class,
        'in-reply-to' => [UnstructuredHeader::class, IdentificationHeader::class], // `In-Reply-To` and `References` are less strict than RFC 2822 (3.6.4) to allow users entering the original email's ...
        'references' => [UnstructuredHeader::class, IdentificationHeader::class], // ... `Message-ID`, even if that is no valid `msg-id`
        'return-path' => PathHeader::class,
    ];

    /**
     * @var HeaderInterface[][]
     */
    private array $headers = [];
    private int $lineLength = 76;

    public function __construct(HeaderInterface ...$headers)
    {
        foreach ($headers as $header) {
            $this->add($header);
        }
    }

    public function __clone()
    {
        foreach ($this->headers as $name => $collection) {
            foreach ($collection as $i => $header) {
                $this->headers[$name][$i] = clone $header;
            }
        }
    }

    public function setMaxLineLength(int $lineLength): void
    {
        $this->lineLength = $lineLength;
        foreach ($this->all() as $header) {
            $header->setMaxLineLength($lineLength);
        }
    }

    public function getMaxLineLength(): int
    {
        return $this->lineLength;
    }

    /**
     * @param array<Address|string> $addresses
     *
     * @return $this
     */
    public function addMailboxListHeader(string $name, array $addresses): static
    {
        return $this->add(new MailboxListHeader($name, Address::createArray($addresses)));
    }

    /**
     * @return $this
     */
    public function addMailboxHeader(string $name, Address|string $address): static
    {
        return $this->add(new MailboxHeader($name, Address::create($address)));
    }

    /**
     * @return $this
     */
    public function addIdHeader(string $name, string|array $ids): static
    {
        return $this->add(new IdentificationHeader($name, $ids));
    }

    /**
     * @return $this
     */
    public function addPathHeader(string $name, Address|string $path): static
    {
        return $this->add(new PathHeader($name, $path instanceof Address ? $path : new Address($path)));
    }

    /**
     * @return $this
     */
    public function addDateHeader(string $name, \DateTimeInterface $dateTime): static
    {
        return $this->add(new DateHeader($name, $dateTime));
    }

    /**
     * @return $this
     */
    public function addTextHeader(string $name, string $value): static
    {
        return $this->add(new UnstructuredHeader($name, $value));
    }

    /**
     * @return $this
     */
    public function addParameterizedHeader(string $name, string $value, array $params = []): static
    {
        return $this->add(new ParameterizedHeader($name, $value, $params));
    }

    /**
     * @return $this
     */
    public function addHeader(string $name, mixed $argument, array $more = []): static
    {
        $headerClass = self::HEADER_CLASS_MAP[strtolower($name)] ?? UnstructuredHeader::class;
        if (\is_array($headerClass)) {
            $headerClass = $headerClass[0];
        }
        $parts = explode('\\', $headerClass);
        $method = 'add'.ucfirst(array_pop($parts));
        if ('addUnstructuredHeader' === $method) {
            $method = 'addTextHeader';
        } elseif ('addIdentificationHeader' === $method) {
            $method = 'addIdHeader';
        } elseif ('addMailboxListHeader' === $method && !\is_array($argument)) {
            $argument = [$argument];
        }

        return $this->$method($name, $argument, $more);
    }

    public function has(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * @return $this
     */
    public function add(HeaderInterface $header): static
    {
        self::checkHeaderClass($header);

        $header->setMaxLineLength($this->lineLength);
        $name = strtolower($header->getName());

        if (\in_array($name, self::UNIQUE_HEADERS, true) && isset($this->headers[$name]) && \count($this->headers[$name]) > 0) {
            throw new LogicException(\sprintf('Impossible to set header "%s" as it\'s already defined and must be unique.', $header->getName()));
        }

        $this->headers[$name][] = $header;

        return $this;
    }

    public function get(string $name): ?HeaderInterface
    {
        $name = strtolower($name);
        if (!isset($this->headers[$name])) {
            return null;
        }

        $values = array_values($this->headers[$name]);

        return array_shift($values);
    }

    public function all(?string $name = null): iterable
    {
        if (null === $name) {
            foreach ($this->headers as $name => $collection) {
                foreach ($collection as $header) {
                    yield $name => $header;
                }
            }
        } elseif (isset($this->headers[strtolower($name)])) {
            foreach ($this->headers[strtolower($name)] as $header) {
                yield $header;
            }
        }
    }

    public function getNames(): array
    {
        return array_keys($this->headers);
    }

    public function remove(string $name): void
    {
        unset($this->headers[strtolower($name)]);
    }

    public static function isUniqueHeader(string $name): bool
    {
        return \in_array(strtolower($name), self::UNIQUE_HEADERS, true);
    }

    /**
     * @throws LogicException if the header name and class are not compatible
     */
    public static function checkHeaderClass(HeaderInterface $header): void
    {
        $name = strtolower($header->getName());
        $headerClasses = self::HEADER_CLASS_MAP[$name] ?? [];
        if (!\is_array($headerClasses)) {
            $headerClasses = [$headerClasses];
        }

        if (!$headerClasses) {
            return;
        }

        foreach ($headerClasses as $c) {
            if ($header instanceof $c) {
                return;
            }
        }

        throw new LogicException(\sprintf('The "%s" header must be an instance of "%s" (got "%s").', $header->getName(), implode('" or "', $headerClasses), get_debug_type($header)));
    }

    public function toString(): string
    {
        $string = '';
        foreach ($this->toArray() as $str) {
            $string .= $str."\r\n";
        }

        return $string;
    }

    public function toArray(): array
    {
        $arr = [];
        foreach ($this->all() as $header) {
            if ('' !== $header->getBodyAsString()) {
                $arr[] = $header->toString();
            }
        }

        return $arr;
    }

    public function getHeaderBody(string $name): mixed
    {
        return $this->has($name) ? $this->get($name)->getBody() : null;
    }

    /**
     * @internal
     */
    public function setHeaderBody(string $type, string $name, mixed $body): void
    {
        if ($this->has($name)) {
            $this->get($name)->setBody($body);
        } else {
            $this->{'add'.$type.'Header'}($name, $body);
        }
    }

    public function getHeaderParameter(string $name, string $parameter): ?string
    {
        if (!$this->has($name)) {
            return null;
        }

        $header = $this->get($name);
        if (!$header instanceof ParameterizedHeader) {
            throw new LogicException(\sprintf('Unable to get parameter "%s" on header "%s" as the header is not of class "%s".', $parameter, $name, ParameterizedHeader::class));
        }

        return $header->getParameter($parameter);
    }

    /**
     * @internal
     */
    public function setHeaderParameter(string $name, string $parameter, ?string $value): void
    {
        if (!$this->has($name)) {
            throw new LogicException(\sprintf('Unable to set parameter "%s" on header "%s" as the header is not defined.', $parameter, $name));
        }

        $header = $this->get($name);
        if (!$header instanceof ParameterizedHeader) {
            throw new LogicException(\sprintf('Unable to set parameter "%s" on header "%s" as the header is not of class "%s".', $parameter, $name, ParameterizedHeader::class));
        }

        $header->setParameter($parameter, $value);
    }
}
