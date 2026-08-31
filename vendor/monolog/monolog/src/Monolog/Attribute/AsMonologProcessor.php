<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Attribute;

/**
 * A reusable attribute to help configure a class or a method as a processor.
 *
 * Using it offers no guarantee: it needs to be leveraged by a Monolog third-party consumer.
 *
 * Using it with the Monolog library only has no effect at all: processors should still be turned into a callable if
 * needed and manually pushed to the loggers and to the processable handlers.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class AsMonologProcessor
{
    /**
     * @param string|null $channel  The logging channel the processor should be pushed to.
     * @param string|null $handler  The handler the processor should be pushed to.
     * @param string|null $method   The method that processes the records (if the attribute is used at the class level).
     * @param int|null    $priority The priority of the processor so the order can be determined.
     */
    public function __construct(
        public readonly ?string $channel = null,
        public readonly ?string $handler = null,
        public readonly ?string $method = null,
        public readonly ?int $priority = null
    ) {
    }
}
