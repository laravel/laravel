<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Highlight;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\ParametersTrait;

/**
 * Data holder for highlight api.
 */
class Highlight implements BuilderInterface
{
    use ParametersTrait;

    /**
     * @var array Holds fields for highlight.
     */
    private $fields = [];

    /**
     * @var array
     */
    private $tags;

    /**
     * @param string $name   Field name to highlight.
     * @param array  $params
     *
     * @return $this
     */
    public function addField($name, array $params = [])
    {
        $this->fields[$name] = $params;

        return $this;
    }

    /**
     * Sets html tag and its class used in highlighting.
     *
     * @param array $preTags
     * @param array $postTags
     *
     * @return $this
     */
    public function setTags(array $preTags, array $postTags)
    {
        $this->tags['pre_tags'] = $preTags;
        $this->tags['post_tags'] = $postTags;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'highlight';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $output = [];

        if (is_array($this->tags)) {
            $output = $this->tags;
        }

        $output = $this->processArray($output);

        foreach ($this->fields as $field => $params) {
            $output['fields'][$field] = count($params) ? $params : new \stdClass();
        }

        return $output;
    }
}
