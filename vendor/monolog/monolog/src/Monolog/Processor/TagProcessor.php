<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use Monolog\LogRecord;

/**
 * Adds a tags array into record
 *
 * @author Martijn Riemers
 */
class TagProcessor implements ProcessorInterface
{
    /** @var string[] */
    private array $tags;

    /**
     * @param string[] $tags
     */
    public function __construct(array $tags = [])
    {
        $this->setTags($tags);
    }

    /**
     * @param  string[] $tags
     * @return $this
     */
    public function addTags(array $tags = []): self
    {
        $this->tags = array_merge($this->tags, $tags);

        return $this;
    }

    /**
     * @param  string[] $tags
     * @return $this
     */
    public function setTags(array $tags = []): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['tags'] = $this->tags;

        return $record;
    }
}
