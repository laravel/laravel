<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Part\Multipart;

use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Part\AbstractMultipartPart;
use Symfony\Component\Mime\Part\TextPart;

/**
 * Implements RFC 7578.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class FormDataPart extends AbstractMultipartPart
{
    /**
     * @param array<string|array|TextPart> $fields
     */
    public function __construct(
        private array $fields = [],
    ) {
        parent::__construct();

        // HTTP does not support \r\n in header values
        $this->getHeaders()->setMaxLineLength(\PHP_INT_MAX);
    }

    public function getMediaSubtype(): string
    {
        return 'form-data';
    }

    public function getParts(): array
    {
        return $this->prepareFields($this->fields);
    }

    private function prepareFields(array $fields): array
    {
        $values = [];

        $prepare = function ($item, $key, $root = null) use (&$values, &$prepare) {
            if (null === $root && \is_int($key) && \is_array($item)) {
                if (1 !== \count($item)) {
                    throw new InvalidArgumentException(\sprintf('Form field values with integer keys can only have one array element, the key being the field name and the value being the field value, %d provided.', \count($item)));
                }

                $key = key($item);
                $item = $item[$key];
            }

            $fieldName = null !== $root ? \sprintf('%s[%s]', $root, $key) : $key;

            if (\is_array($item)) {
                array_walk($item, $prepare, $fieldName);

                return;
            }

            if (!\is_string($item) && !$item instanceof TextPart) {
                throw new InvalidArgumentException(\sprintf('The value of the form field "%s" can only be a string, an array, or an instance of TextPart, "%s" given.', $fieldName, get_debug_type($item)));
            }

            $values[] = $this->preparePart($fieldName, $item);
        };

        array_walk($fields, $prepare);

        return $values;
    }

    private function preparePart(string $name, string|TextPart $value): TextPart
    {
        if (\is_string($value)) {
            return $this->configurePart($name, new TextPart($value, 'utf-8', 'plain', '8bit'));
        }

        return $this->configurePart($name, $value);
    }

    private function configurePart(string $name, TextPart $part): TextPart
    {
        static $r;

        $r ??= new \ReflectionProperty(TextPart::class, 'encoding');

        $part->setDisposition('form-data');
        $part->setName($name);
        // HTTP does not support \r\n in header values
        $part->getHeaders()->setMaxLineLength(\PHP_INT_MAX);
        $r->setValue($part, '8bit');

        return $part;
    }
}
