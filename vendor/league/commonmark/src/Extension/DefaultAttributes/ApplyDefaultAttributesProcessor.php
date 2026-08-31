<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\DefaultAttributes;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class ApplyDefaultAttributesProcessor implements ConfigurationAwareInterface
{
    private ConfigurationInterface $config;

    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        /** @var array<string, array<string, mixed>> $map */
        $map = $this->config->get('default_attributes');

        // Don't bother iterating if no default attributes are configured
        if (! $map) {
            return;
        }

        foreach ($event->getDocument()->iterator() as $node) {
            // Check to see if any default attributes were defined
            if (($attributesToApply = $map[\get_class($node)] ?? []) === []) {
                continue;
            }

            $newAttributes = [];
            foreach ($attributesToApply as $name => $value) {
                if (\is_callable($value)) {
                    $value = $value($node);
                    // Callables are allowed to return `null` indicating that no changes should be made
                    if ($value !== null) {
                        $newAttributes[$name] = $value;
                    }
                } else {
                    $newAttributes[$name] = $value;
                }
            }

            // Merge these attributes into the node
            if (\count($newAttributes) > 0) {
                $node->data->set('attributes', AttributesHelper::mergeAttributes($node, $newAttributes));
            }
        }
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }
}
