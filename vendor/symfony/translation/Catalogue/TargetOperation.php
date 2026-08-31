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

use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Target operation between two catalogues:
 * intersection = source ∩ target = {x: x ∈ source ∧ x ∈ target}
 * all = intersection ∪ (target ∖ intersection) = target
 * new = all ∖ source = {x: x ∈ target ∧ x ∉ source}
 * obsolete = source ∖ all = source ∖ target = {x: x ∈ source ∧ x ∉ target}
 * Basically, the result contains messages from the target catalogue.
 *
 * @author Michael Lee <michael.lee@zerustech.com>
 */
class TargetOperation extends AbstractOperation
{
    protected function processDomain(string $domain): void
    {
        $this->messages[$domain] = [
            'all' => [],
            'new' => [],
            'obsolete' => [],
        ];
        $intlDomain = $domain.MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;

        foreach ($this->target->getCatalogueMetadata('', $domain) ?? [] as $key => $value) {
            if (null === $this->result->getCatalogueMetadata($key, $domain)) {
                $this->result->setCatalogueMetadata($key, $value, $domain);
            }
        }

        foreach ($this->target->getCatalogueMetadata('', $intlDomain) ?? [] as $key => $value) {
            if (null === $this->result->getCatalogueMetadata($key, $intlDomain)) {
                $this->result->setCatalogueMetadata($key, $value, $intlDomain);
            }
        }

        // For 'all' messages, the code can't be simplified as ``$this->messages[$domain]['all'] = $target->all($domain);``,
        // because doing so will drop messages like {x: x ∈ source ∧ x ∉ target.all ∧ x ∈ target.fallback}
        //
        // For 'new' messages, the code can't be simplified as ``array_diff_assoc($this->target->all($domain), $this->source->all($domain));``
        // because doing so will not exclude messages like {x: x ∈ target ∧ x ∉ source.all ∧ x ∈ source.fallback}
        //
        // For 'obsolete' messages, the code can't be simplified as ``array_diff_assoc($this->source->all($domain), $this->target->all($domain))``
        // because doing so will not exclude messages like {x: x ∈ source ∧ x ∉ target.all ∧ x ∈ target.fallback}

        foreach ($this->source->all($domain) as $id => $message) {
            if ($this->target->has($id, $domain)) {
                $this->messages[$domain]['all'][$id] = $message;
                $d = $this->source->defines($id, $intlDomain) ? $intlDomain : $domain;
                $this->result->add([$id => $message], $d);
                if (null !== $keyMetadata = $this->source->getMetadata($id, $d)) {
                    $this->result->setMetadata($id, $keyMetadata, $d);
                }
            } else {
                $this->messages[$domain]['obsolete'][$id] = $message;
            }
        }

        foreach ($this->target->all($domain) as $id => $message) {
            if (!$this->source->has($id, $domain)) {
                $this->messages[$domain]['all'][$id] = $message;
                $this->messages[$domain]['new'][$id] = $message;
                $d = $this->target->defines($id, $intlDomain) ? $intlDomain : $domain;
                $this->result->add([$id => $message], $d);
                if (null !== $keyMetadata = $this->target->getMetadata($id, $d)) {
                    $this->result->setMetadata($id, $keyMetadata, $d);
                }
            }
        }
    }
}
