<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Attributes\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Attributes\Node\Attributes;
use League\CommonMark\Extension\Attributes\Node\AttributesInline;
use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Node;

final class AttributesListener
{
    private const DIRECTION_PREFIX = 'prefix';
    private const DIRECTION_SUFFIX = 'suffix';

    /** @var list<string> */
    private array $allowList;
    private bool $allowUnsafeLinks;

    /**
     * @param list<string> $allowList
     */
    public function __construct(array $allowList = [], bool $allowUnsafeLinks = true)
    {
        $this->allowList        = $allowList;
        $this->allowUnsafeLinks = $allowUnsafeLinks;
    }

    public function processDocument(DocumentParsedEvent $event): void
    {
        foreach ($event->getDocument()->iterator() as $node) {
            if (! ($node instanceof Attributes || $node instanceof AttributesInline)) {
                continue;
            }

            [$target, $direction] = self::findTargetAndDirection($node);

            if ($target instanceof Node) {
                $parent = $target->parent();
                if ($parent instanceof ListItem && $parent->parent() instanceof ListBlock && $parent->parent()->isTight()) {
                    $target = $parent;
                }

                if ($direction === self::DIRECTION_SUFFIX) {
                    $attributes = AttributesHelper::mergeAttributes($target, $node->getAttributes());
                } else {
                    $attributes = AttributesHelper::mergeAttributes($node->getAttributes(), $target);
                }

                $target->data->set('attributes', AttributesHelper::filterAttributes($attributes, $this->allowList, $this->allowUnsafeLinks));
            }

            $node->detach();
        }
    }

    /**
     * @param Attributes|AttributesInline $node
     *
     * @return array<Node|string|null>
     */
    private static function findTargetAndDirection($node): array
    {
        $target    = null;
        $direction = null;
        $previous  = $next = $node;
        while (true) {
            $previous = self::getPrevious($previous);
            $next     = self::getNext($next);

            if ($previous === null && $next === null) {
                if (! $node->parent() instanceof FencedCode) {
                    $target    = $node->parent();
                    $direction = self::DIRECTION_SUFFIX;
                }

                break;
            }

            if ($node instanceof AttributesInline && ($previous === null || ($previous instanceof AbstractInline && $node->isBlock()))) {
                continue;
            }

            if ($previous !== null && ! self::isAttributesNode($previous)) {
                $target    = $previous;
                $direction = self::DIRECTION_SUFFIX;

                break;
            }

            if ($next !== null && ! self::isAttributesNode($next)) {
                $target    = $next;
                $direction = self::DIRECTION_PREFIX;

                break;
            }
        }

        return [$target, $direction];
    }

    /**
     * Get any previous block (sibling or parent) this might apply to
     */
    private static function getPrevious(?Node $node = null): ?Node
    {
        if ($node instanceof Attributes) {
            if ($node->getTarget() === Attributes::TARGET_NEXT) {
                return null;
            }

            if ($node->getTarget() === Attributes::TARGET_PARENT) {
                return $node->parent();
            }
        }

        return $node instanceof Node ? $node->previous() : null;
    }

    /**
     * Get any previous block (sibling or parent) this might apply to
     */
    private static function getNext(?Node $node = null): ?Node
    {
        if ($node instanceof Attributes && $node->getTarget() !== Attributes::TARGET_NEXT) {
            return null;
        }

        return $node instanceof Node ? $node->next() : null;
    }

    private static function isAttributesNode(Node $node): bool
    {
        return $node instanceof Attributes || $node instanceof AttributesInline;
    }
}
