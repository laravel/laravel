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

use Symfony\Component\Mime\Exception\RuntimeException;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;
use Symfony\Component\Mime\Part\Multipart\MixedPart;
use Symfony\Component\Mime\Part\Multipart\RelatedPart;
use Symfony\Component\Mime\Part\TextPart;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class MessageConverter
{
    /**
     * @throws RuntimeException when unable to convert the message to an email
     */
    public static function toEmail(Message $message): Email
    {
        if ($message instanceof Email) {
            return $message;
        }

        // try to convert to a "simple" Email instance
        $body = $message->getBody();
        if ($body instanceof TextPart) {
            return self::createEmailFromTextPart($message, $body);
        }

        if ($body instanceof AlternativePart) {
            return self::createEmailFromAlternativePart($message, $body);
        }

        if ($body instanceof RelatedPart) {
            return self::createEmailFromRelatedPart($message, $body);
        }

        if ($body instanceof MixedPart) {
            $parts = $body->getParts();
            if ($parts[0] instanceof RelatedPart) {
                $email = self::createEmailFromRelatedPart($message, $parts[0]);
            } elseif ($parts[0] instanceof AlternativePart) {
                $email = self::createEmailFromAlternativePart($message, $parts[0]);
            } elseif ($parts[0] instanceof TextPart) {
                $email = self::createEmailFromTextPart($message, $parts[0]);
            } else {
                throw new RuntimeException(\sprintf('Unable to create an Email from an instance of "%s" as the body is too complex.', get_debug_type($message)));
            }

            return self::addParts($email, \array_slice($parts, 1));
        }

        throw new RuntimeException(\sprintf('Unable to create an Email from an instance of "%s" as the body is too complex.', get_debug_type($message)));
    }

    private static function createEmailFromTextPart(Message $message, TextPart $part): Email
    {
        if ('text' === $part->getMediaType() && 'plain' === $part->getMediaSubtype()) {
            return (new Email(clone $message->getHeaders()))->text($part->getBody(), $part->getPreparedHeaders()->getHeaderParameter('Content-Type', 'charset') ?: 'utf-8');
        }
        if ('text' === $part->getMediaType() && 'html' === $part->getMediaSubtype()) {
            return (new Email(clone $message->getHeaders()))->html($part->getBody(), $part->getPreparedHeaders()->getHeaderParameter('Content-Type', 'charset') ?: 'utf-8');
        }

        throw new RuntimeException(\sprintf('Unable to create an Email from an instance of "%s" as the body is too complex.', get_debug_type($message)));
    }

    private static function createEmailFromAlternativePart(Message $message, AlternativePart $part): Email
    {
        $parts = $part->getParts();
        if (
            2 === \count($parts)
            && $parts[0] instanceof TextPart && 'text' === $parts[0]->getMediaType() && 'plain' === $parts[0]->getMediaSubtype()
            && $parts[1] instanceof TextPart && 'text' === $parts[1]->getMediaType() && 'html' === $parts[1]->getMediaSubtype()
        ) {
            return (new Email(clone $message->getHeaders()))
                ->text($parts[0]->getBody(), $parts[0]->getPreparedHeaders()->getHeaderParameter('Content-Type', 'charset') ?: 'utf-8')
                ->html($parts[1]->getBody(), $parts[1]->getPreparedHeaders()->getHeaderParameter('Content-Type', 'charset') ?: 'utf-8')
            ;
        }

        throw new RuntimeException(\sprintf('Unable to create an Email from an instance of "%s" as the body is too complex.', get_debug_type($message)));
    }

    private static function createEmailFromRelatedPart(Message $message, RelatedPart $part): Email
    {
        $parts = $part->getParts();
        if ($parts[0] instanceof AlternativePart) {
            $email = self::createEmailFromAlternativePart($message, $parts[0]);
        } elseif ($parts[0] instanceof TextPart) {
            $email = self::createEmailFromTextPart($message, $parts[0]);
        } else {
            throw new RuntimeException(\sprintf('Unable to create an Email from an instance of "%s" as the body is too complex.', get_debug_type($message)));
        }

        return self::addParts($email, \array_slice($parts, 1));
    }

    private static function addParts(Email $email, array $parts): Email
    {
        foreach ($parts as $part) {
            if (!$part instanceof DataPart) {
                throw new RuntimeException(\sprintf('Unable to create an Email from an instance of "%s" as the body is too complex.', get_debug_type($email)));
            }

            $email->addPart($part);
        }

        return $email;
    }
}
