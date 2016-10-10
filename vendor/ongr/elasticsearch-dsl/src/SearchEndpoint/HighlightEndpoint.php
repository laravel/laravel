<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\SearchEndpoint;

use ONGR\ElasticsearchDSL\BuilderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Search highlight dsl endpoint.
 */
class HighlightEndpoint extends AbstractSearchEndpoint
{
    /**
     * Endpoint name
     */
    const NAME = 'highlight';

    /**
     * @var BuilderInterface
     */
    private $highlight;

    /**
     * @var string Key for highlight storing.
     */
    private $key;

    /**
     * {@inheritdoc}
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = [])
    {
        if ($this->highlight) {
            return $this->highlight->toArray();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function add(BuilderInterface $builder, $key = null)
    {
        if ($this->highlight) {
            throw new \OverflowException('Only one highlight can be set');
        }

        $this->key = $key;
        $this->highlight = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll($boolType = null)
    {
        return [$this->key => $this->highlight];
    }

    /**
     * @return BuilderInterface
     */
    public function getHighlight()
    {
        return $this->highlight;
    }
}
