<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Catalogue;

use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\Exception\LogicException;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Base catalogues binary operation class.
 *
 * A catalogue binary operation performs operation on
 * source (the left argument) and target (the right argument) catalogues.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
abstract class AbstractOperation implements OperationInterface
{
    public const OBSOLETE_BATCH = 'obsolete';
    public const NEW_BATCH = 'new';
    public const ALL_BATCH = 'all';

    protected MessageCatalogue $result;

    /**
     * This array stores 'all', 'new' and 'obsolete' messages for all valid domains.
     *
     * The data structure of this array is as follows:
     *
     *     [
     *         'domain 1' => [
     *             'all' => [...],
     *             'new' => [...],
     *             'obsolete' => [...]
     *         ],
     *         'domain 2' => [
     *             'all' => [...],
     *             'new' => [...],
     *             'obsolete' => [...]
     *         ],
     *         ...
     *     ]
     *
     * @var array The array that stores 'all', 'new' and 'obsolete' messages
     */
    protected array $messages;

    private array $domains;

    /**
     * @throws LogicException
     */
    public function __construct(
        protected MessageCatalogueInterface $source,
        protected MessageCatalogueInterface $target,
    ) {
        if ($source->getLocale() !== $target->getLocale()) {
            throw new LogicException('Operated catalogues must belong to the same locale.');
        }

        $this->result = new MessageCatalogue($source->getLocale());
        $this->messages = [];
    }

    public function getDomains(): array
    {
        if (!isset($this->domains)) {
            $domains = [];
            foreach ([$this->source, $this->target] as $catalogue) {
                foreach ($catalogue->getDomains() as $domain) {
                    $domains[$domain] = $domain;

                    if ($catalogue->all($domainIcu = $domain.MessageCatalogueInterface::INTL_DOMAIN_SUFFIX)) {
                        $domains[$domainIcu] = $domainIcu;
                    }
                }
            }

            $this->domains = array_values($domains);
        }

        return $this->domains;
    }

    public function getMessages(string $domain): array
    {
        if (!\in_array($domain, $this->getDomains(), true)) {
            throw new InvalidArgumentException(\sprintf('Invalid domain: "%s".', $domain));
        }

        if (!isset($this->messages[$domain][self::ALL_BATCH])) {
            $this->processDomain($domain);
        }

        return $this->messages[$domain][self::ALL_BATCH];
    }

    public function getNewMessages(string $domain): array
    {
        if (!\in_array($domain, $this->getDomains(), true)) {
            throw new InvalidArgumentException(\sprintf('Invalid domain: "%s".', $domain));
        }

        if (!isset($this->messages[$domain][self::NEW_BATCH])) {
            $this->processDomain($domain);
        }

        return $this->messages[$domain][self::NEW_BATCH];
    }

    public function getObsoleteMessages(string $domain): array
    {
        if (!\in_array($domain, $this->getDomains(), true)) {
            throw new InvalidArgumentException(\sprintf('Invalid domain: "%s".', $domain));
        }

        if (!isset($this->messages[$domain][self::OBSOLETE_BATCH])) {
            $this->processDomain($domain);
        }

        return $this->messages[$domain][self::OBSOLETE_BATCH];
    }

    public function getResult(): MessageCatalogueInterface
    {
        foreach ($this->getDomains() as $domain) {
            if (!isset($this->messages[$domain])) {
                $this->processDomain($domain);
            }
        }

        return $this->result;
    }

    /**
     * @param self::*_BATCH $batch
     */
    public function moveMessagesToIntlDomainsIfPossible(string $batch = self::ALL_BATCH): void
    {
        // If MessageFormatter class does not exist, intl domains are not supported.
        if (!class_exists(\MessageFormatter::class)) {
            return;
        }

        foreach ($this->getDomains() as $domain) {
            $intlDomain = $domain.MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;
            $messages = match ($batch) {
                self::OBSOLETE_BATCH => $this->getObsoleteMessages($domain),
                self::NEW_BATCH => $this->getNewMessages($domain),
                self::ALL_BATCH => $this->getMessages($domain),
                default => throw new \InvalidArgumentException(\sprintf('$batch argument must be one of ["%s", "%s", "%s"].', self::ALL_BATCH, self::NEW_BATCH, self::OBSOLETE_BATCH)),
            };

            if (!$messages || (!$this->source->all($intlDomain) && $this->source->all($domain))) {
                continue;
            }

            $result = $this->getResult();
            $allIntlMessages = $result->all($intlDomain);
            $currentMessages = array_diff_key($messages, $result->all($domain));
            $result->replace($currentMessages, $domain);
            $result->replace($allIntlMessages + $messages, $intlDomain);

            foreach ($result->getCatalogueMetadata('', $domain) ?? [] as $key => $value) {
                if (null === $this->result->getCatalogueMetadata($key, $intlDomain)) {
                    $result->setCatalogueMetadata($key, $value, $intlDomain);
                }
            }
        }
    }

    /**
     * Performs operation on source and target catalogues for the given domain and
     * stores the results.
     *
     * @param string $domain The domain which the operation will be performed for
     */
    abstract protected function processDomain(string $domain): void;
}
