<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation;

use Symfony\Component\Translation\Catalogue\AbstractOperation;
use Symfony\Component\Translation\Catalogue\TargetOperation;

final class TranslatorBag implements TranslatorBagInterface
{
    /** @var MessageCatalogue[] */
    private array $catalogues = [];

    public function addCatalogue(MessageCatalogue $catalogue): void
    {
        if (null !== $existingCatalogue = $this->getCatalogue($catalogue->getLocale())) {
            $catalogue->addCatalogue($existingCatalogue);
        }

        $this->catalogues[$catalogue->getLocale()] = $catalogue;
    }

    public function addBag(TranslatorBagInterface $bag): void
    {
        foreach ($bag->getCatalogues() as $catalogue) {
            $this->addCatalogue($catalogue);
        }
    }

    public function getCatalogue(?string $locale = null): MessageCatalogueInterface
    {
        if (null === $locale || !isset($this->catalogues[$locale])) {
            $this->catalogues[$locale] = new MessageCatalogue($locale);
        }

        return $this->catalogues[$locale];
    }

    public function getCatalogues(): array
    {
        return array_values($this->catalogues);
    }

    public function diff(TranslatorBagInterface $diffBag): self
    {
        $diff = new self();

        foreach ($this->catalogues as $locale => $catalogue) {
            if (null === $diffCatalogue = $diffBag->getCatalogue($locale)) {
                $diff->addCatalogue($catalogue);

                continue;
            }

            $operation = new TargetOperation($diffCatalogue, $catalogue);
            $operation->moveMessagesToIntlDomainsIfPossible(AbstractOperation::NEW_BATCH);
            $newCatalogue = new MessageCatalogue($locale);

            foreach ($catalogue->getDomains() as $domain) {
                $newCatalogue->add($operation->getNewMessages($domain), $domain);
            }

            $diff->addCatalogue($newCatalogue);
        }

        return $diff;
    }

    public function intersect(TranslatorBagInterface $intersectBag): self
    {
        $diff = new self();

        foreach ($this->catalogues as $locale => $catalogue) {
            if (null === $intersectCatalogue = $intersectBag->getCatalogue($locale)) {
                continue;
            }

            $operation = new TargetOperation($catalogue, $intersectCatalogue);
            $operation->moveMessagesToIntlDomainsIfPossible(AbstractOperation::OBSOLETE_BATCH);
            $obsoleteCatalogue = new MessageCatalogue($locale);

            foreach ($operation->getDomains() as $domain) {
                $obsoleteCatalogue->add(
                    array_diff($operation->getMessages($domain), $operation->getNewMessages($domain)),
                    $domain
                );
            }

            $diff->addCatalogue($obsoleteCatalogue);
        }

        return $diff;
    }
}
