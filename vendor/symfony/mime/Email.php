<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime;

use Symfony\Component\Mime\Exception\LogicException;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;
use Symfony\Component\Mime\Part\Multipart\MixedPart;
use Symfony\Component\Mime\Part\Multipart\RelatedPart;
use Symfony\Component\Mime\Part\TextPart;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Email extends Message
{
    public const PRIORITY_HIGHEST = 1;
    public const PRIORITY_HIGH = 2;
    public const PRIORITY_NORMAL = 3;
    public const PRIORITY_LOW = 4;
    public const PRIORITY_LOWEST = 5;

    private const PRIORITY_MAP = [
        self::PRIORITY_HIGHEST => 'Highest',
        self::PRIORITY_HIGH => 'High',
        self::PRIORITY_NORMAL => 'Normal',
        self::PRIORITY_LOW => 'Low',
        self::PRIORITY_LOWEST => 'Lowest',
    ];

    /**
     * @var resource|string|null
     */
    private $text;

    private ?string $textCharset = null;

    /**
     * @var resource|string|null
     */
    private $html;

    private ?string $htmlCharset = null;
    private array $attachments = [];
    private ?AbstractPart $cachedBody = null; // Used to avoid wrong body hash in DKIM signatures with multiple parts (e.g. HTML + TEXT) due to multiple boundaries.

    /**
     * @return $this
     */
    public function subject(string $subject): static
    {
        return $this->setHeaderBody('Text', 'Subject', $subject);
    }

    public function getSubject(): ?string
    {
        return $this->getHeaders()->getHeaderBody('Subject');
    }

    /**
     * @return $this
     */
    public function date(\DateTimeInterface $dateTime): static
    {
        return $this->setHeaderBody('Date', 'Date', $dateTime);
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->getHeaders()->getHeaderBody('Date');
    }

    /**
     * @return $this
     */
    public function returnPath(Address|string $address): static
    {
        return $this->setHeaderBody('Path', 'Return-Path', Address::create($address));
    }

    public function getReturnPath(): ?Address
    {
        return $this->getHeaders()->getHeaderBody('Return-Path');
    }

    /**
     * @return $this
     */
    public function sender(Address|string $address): static
    {
        return $this->setHeaderBody('Mailbox', 'Sender', Address::create($address));
    }

    public function getSender(): ?Address
    {
        return $this->getHeaders()->getHeaderBody('Sender');
    }

    /**
     * @return $this
     */
    public function addFrom(Address|string ...$addresses): static
    {
        return $this->addListAddressHeaderBody('From', $addresses);
    }

    /**
     * @return $this
     */
    public function from(Address|string ...$addresses): static
    {
        if (!$addresses) {
            throw new LogicException('"from()" must be called with at least one address.');
        }

        return $this->setListAddressHeaderBody('From', $addresses);
    }

    /**
     * @return Address[]
     */
    public function getFrom(): array
    {
        return $this->getHeaders()->getHeaderBody('From') ?: [];
    }

    /**
     * @return $this
     */
    public function addReplyTo(Address|string ...$addresses): static
    {
        return $this->addListAddressHeaderBody('Reply-To', $addresses);
    }

    /**
     * @return $this
     */
    public function replyTo(Address|string ...$addresses): static
    {
        return $this->setListAddressHeaderBody('Reply-To', $addresses);
    }

    /**
     * @return Address[]
     */
    public function getReplyTo(): array
    {
        return $this->getHeaders()->getHeaderBody('Reply-To') ?: [];
    }

    /**
     * @return $this
     */
    public function addTo(Address|string ...$addresses): static
    {
        return $this->addListAddressHeaderBody('To', $addresses);
    }

    /**
     * @return $this
     */
    public function to(Address|string ...$addresses): static
    {
        return $this->setListAddressHeaderBody('To', $addresses);
    }

    /**
     * @return Address[]
     */
    public function getTo(): array
    {
        return $this->getHeaders()->getHeaderBody('To') ?: [];
    }

    /**
     * @return $this
     */
    public function addCc(Address|string ...$addresses): static
    {
        return $this->addListAddressHeaderBody('Cc', $addresses);
    }

    /**
     * @return $this
     */
    public function cc(Address|string ...$addresses): static
    {
        return $this->setListAddressHeaderBody('Cc', $addresses);
    }

    /**
     * @return Address[]
     */
    public function getCc(): array
    {
        return $this->getHeaders()->getHeaderBody('Cc') ?: [];
    }

    /**
     * @return $this
     */
    public function addBcc(Address|string ...$addresses): static
    {
        return $this->addListAddressHeaderBody('Bcc', $addresses);
    }

    /**
     * @return $this
     */
    public function bcc(Address|string ...$addresses): static
    {
        return $this->setListAddressHeaderBody('Bcc', $addresses);
    }

    /**
     * @return Address[]
     */
    public function getBcc(): array
    {
        return $this->getHeaders()->getHeaderBody('Bcc') ?: [];
    }

    /**
     * Sets the priority of this message.
     *
     * The value is an integer where 1 is the highest priority and 5 is the lowest.
     *
     * @return $this
     */
    public function priority(int $priority): static
    {
        if ($priority > 5) {
            $priority = 5;
        } elseif ($priority < 1) {
            $priority = 1;
        }

        return $this->setHeaderBody('Text', 'X-Priority', \sprintf('%d (%s)', $priority, self::PRIORITY_MAP[$priority]));
    }

    /**
     * Get the priority of this message.
     *
     * The returned value is an integer where 1 is the highest priority and 5
     * is the lowest.
     */
    public function getPriority(): int
    {
        [$priority] = sscanf($this->getHeaders()->getHeaderBody('X-Priority') ?? '', '%[1-5]');

        return $priority ?? 3;
    }

    /**
     * @param resource|string|null $body
     *
     * @return $this
     */
    public function text($body, string $charset = 'utf-8'): static
    {
        if (null !== $body && !\is_string($body) && !\is_resource($body)) {
            throw new \TypeError(\sprintf('The body must be a string, a resource or null (got "%s").', get_debug_type($body)));
        }

        $this->cachedBody = null;
        $this->text = $body;
        $this->textCharset = $charset;

        return $this;
    }

    /**
     * @return resource|string|null
     */
    public function getTextBody()
    {
        return $this->text;
    }

    public function getTextCharset(): ?string
    {
        return $this->textCharset;
    }

    /**
     * @param resource|string|null $body
     *
     * @return $this
     */
    public function html($body, string $charset = 'utf-8'): static
    {
        if (null !== $body && !\is_string($body) && !\is_resource($body)) {
            throw new \TypeError(\sprintf('The body must be a string, a resource or null (got "%s").', get_debug_type($body)));
        }

        $this->cachedBody = null;
        $this->html = $body;
        $this->htmlCharset = $charset;

        return $this;
    }

    /**
     * @return resource|string|null
     */
    public function getHtmlBody()
    {
        return $this->html;
    }

    public function getHtmlCharset(): ?string
    {
        return $this->htmlCharset;
    }

    /**
     * @param resource|string $body
     *
     * @return $this
     */
    public function attach($body, ?string $name = null, ?string $contentType = null): static
    {
        return $this->addPart(new DataPart($body, $name, $contentType));
    }

    /**
     * @return $this
     */
    public function attachFromPath(string $path, ?string $name = null, ?string $contentType = null): static
    {
        return $this->addPart(new DataPart(new File($path), $name, $contentType));
    }

    /**
     * @param resource|string $body
     *
     * @return $this
     */
    public function embed($body, ?string $name = null, ?string $contentType = null): static
    {
        return $this->addPart((new DataPart($body, $name, $contentType))->asInline());
    }

    /**
     * @return $this
     */
    public function embedFromPath(string $path, ?string $name = null, ?string $contentType = null): static
    {
        return $this->addPart((new DataPart(new File($path), $name, $contentType))->asInline());
    }

    /**
     * @return $this
     */
    public function addPart(DataPart $part): static
    {
        $this->cachedBody = null;
        $this->attachments[] = $part;

        return $this;
    }

    /**
     * @return DataPart[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function getBody(): AbstractPart
    {
        if (null !== $body = parent::getBody()) {
            return $body;
        }

        return $this->generateBody();
    }

    public function ensureValidity(): void
    {
        $this->ensureBodyValid();

        if ('1' === $this->getHeaders()->getHeaderBody('X-Unsent')) {
            throw new LogicException('Cannot send messages marked as "draft".');
        }

        parent::ensureValidity();
    }

    private function ensureBodyValid(): void
    {
        if (null === $this->text && null === $this->html && !$this->attachments && null === parent::getBody()) {
            throw new LogicException('A message must have a text or an HTML part or attachments.');
        }
    }

    /**
     * Generates an AbstractPart based on the raw body of a message.
     *
     * The most "complex" part generated by this method is when there is text and HTML bodies
     * with related images for the HTML part and some attachments:
     *
     * multipart/mixed
     *         |
     *         |------------> multipart/related
     *         |                      |
     *         |                      |------------> multipart/alternative
     *         |                      |                      |
     *         |                      |                       ------------> text/plain (with content)
     *         |                      |                      |
     *         |                      |                       ------------> text/html (with content)
     *         |                      |
     *         |                       ------------> image/png (with content)
     *         |
     *          ------------> application/pdf (with content)
     */
    private function generateBody(): AbstractPart
    {
        if (null !== $this->cachedBody) {
            return $this->cachedBody;
        }

        $this->ensureBodyValid();

        [$htmlPart, $otherParts, $relatedParts] = $this->prepareParts();

        $part = null === $this->text ? null : new TextPart($this->text, $this->textCharset);
        if (null !== $htmlPart) {
            if (null !== $part) {
                $part = new AlternativePart($part, $htmlPart);
            } else {
                $part = $htmlPart;
            }
        }

        if ($relatedParts) {
            $part = new RelatedPart($part, ...$relatedParts);
        }

        if ($otherParts) {
            if ($part) {
                $part = new MixedPart($part, ...$otherParts);
            } else {
                $part = new MixedPart(...$otherParts);
            }
        }

        return $this->cachedBody = $part;
    }

    private function prepareParts(): ?array
    {
        $names = [];
        $htmlPart = null;
        $html = $this->html;
        if (null !== $html) {
            $htmlPart = new TextPart($html, $this->htmlCharset, 'html');
            $html = $htmlPart->getBody();

            $regexes = [
                '<img\s+[^>]*src\s*=\s*(?:([\'"])cid:(.+?)\\1|cid:([^>\s]+))',
                '<\w+\s+[^>]*background\s*=\s*(?:([\'"])cid:(.+?)\\1|cid:([^>\s]+))',
            ];
            $tmpMatches = [];
            foreach ($regexes as $regex) {
                preg_match_all('/'.$regex.'/i', $html, $tmpMatches);
                $names = array_merge($names, $tmpMatches[2], $tmpMatches[3]);
            }
            $names = array_filter(array_unique($names));
        }

        $otherParts = $relatedParts = [];
        foreach ($this->attachments as $part) {
            foreach ($names as $name) {
                if ($name !== $part->getName() && (!$part->hasContentId() || $name !== $part->getContentId())) {
                    continue;
                }
                if (isset($relatedParts[$name])) {
                    continue 2;
                }

                if ($name !== $part->getContentId()) {
                    $html = str_replace('cid:'.$name, 'cid:'.$part->getContentId(), $html);
                }
                $relatedParts[$name] = $part;
                $part->setName($part->getName() ?? $part->getContentId())->asInline();

                continue 2;
            }

            $otherParts[] = $part;
        }
        if (null !== $htmlPart) {
            $htmlPart = new TextPart($html, $this->htmlCharset, 'html');
        }

        return [$htmlPart, $otherParts, array_values($relatedParts)];
    }

    /**
     * @return $this
     */
    private function setHeaderBody(string $type, string $name, $body): static
    {
        $this->getHeaders()->setHeaderBody($type, $name, $body);

        return $this;
    }

    /**
     * @return $this
     */
    private function addListAddressHeaderBody(string $name, array $addresses): static
    {
        if (!$header = $this->getHeaders()->get($name)) {
            return $this->setListAddressHeaderBody($name, $addresses);
        }
        $header->addAddresses(Address::createArray($addresses));

        return $this;
    }

    /**
     * @return $this
     */
    private function setListAddressHeaderBody(string $name, array $addresses): static
    {
        $addresses = Address::createArray($addresses);
        $headers = $this->getHeaders();
        if ($header = $headers->get($name)) {
            $header->setAddresses($addresses);
        } else {
            $headers->addMailboxListHeader($name, $addresses);
        }

        return $this;
    }

    /**
     * @internal
     */
    public function __serialize(): array
    {
        if (\is_resource($this->text)) {
            $this->text = (new TextPart($this->text))->getBody();
        }

        if (\is_resource($this->html)) {
            $this->html = (new TextPart($this->html))->getBody();
        }

        return [$this->text, $this->textCharset, $this->html, $this->htmlCharset, $this->attachments, parent::__serialize()];
    }

    /**
     * @internal
     */
    public function __unserialize(array $data): void
    {
        if (($data[1] ?? null) instanceof \Stringable || ($data[3] ?? null) instanceof \Stringable) {
            throw new \BadMethodCallException('Cannot unserialize '.self::class);
        }

        [$this->text, $this->textCharset, $this->html, $this->htmlCharset, $this->attachments, $parentData] = $data;

        parent::__unserialize($parentData);
    }
}
