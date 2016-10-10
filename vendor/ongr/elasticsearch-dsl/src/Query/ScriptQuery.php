<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Query;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\ParametersTrait;

/**
 * Represents Elasticsearch "script" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-script-query.html
 */
class ScriptQuery implements BuilderInterface
{
    use ParametersTrait;

    /**
     * @var string
     */
    private $script;

    /**
     * @param string $script     Script
     * @param array  $parameters Optional parameters
     */
    public function __construct($script, array $parameters = [])
    {
        $this->script = $script;
        $this->setParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'script';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $query = ['inline' => $this->script];
        $output = $this->processArray($query);

        return [$this->getType() => ['script' => $output]];
    }
}
