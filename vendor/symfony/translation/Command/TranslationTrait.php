<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Command;

use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBag;

/**
 * @internal
 */
trait TranslationTrait
{
    private function readLocalTranslations(array $locales, array $domains, array $transPaths): TranslatorBag
    {
        $bag = new TranslatorBag();

        foreach ($locales as $locale) {
            $catalogue = new MessageCatalogue($locale);
            foreach ($transPaths as $path) {
                $this->reader->read($path, $catalogue);
            }

            if ($domains) {
                foreach ($domains as $domain) {
                    $bag->addCatalogue($this->filterCatalogue($catalogue, $domain));
                }
            } else {
                $bag->addCatalogue($catalogue);
            }
        }

        return $bag;
    }

    private function filterCatalogue(MessageCatalogue $catalogue, string $domain): MessageCatalogue
    {
        $filteredCatalogue = new MessageCatalogue($catalogue->getLocale());

        // extract intl-icu messages only
        $intlDomain = $domain.MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;
        if ($intlMessages = $catalogue->all($intlDomain)) {
            $filteredCatalogue->add($intlMessages, $intlDomain);
        }

        // extract all messages and subtract intl-icu messages
        if ($messages = array_diff($catalogue->all($domain), $intlMessages)) {
            $filteredCatalogue->add($messages, $domain);
        }
        foreach ($catalogue->getResources() as $resource) {
            $filteredCatalogue->addResource($resource);
        }

        if ($metadata = $catalogue->getMetadata('', $intlDomain)) {
            foreach ($metadata as $k => $v) {
                $filteredCatalogue->setMetadata($k, $v, $intlDomain);
            }
        }

        if ($metadata = $catalogue->getMetadata('', $domain)) {
            foreach ($metadata as $k => $v) {
                $filteredCatalogue->setMetadata($k, $v, $domain);
            }
        }

        return $filteredCatalogue;
    }
}
