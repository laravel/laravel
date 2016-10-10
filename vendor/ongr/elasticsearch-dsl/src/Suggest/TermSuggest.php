<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Suggest;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\ParametersTrait;

class TermSuggest implements BuilderInterface
{
    use ParametersTrait;

    const DEFAULT_SIZE = 3;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $text;

    public function __construct($name, $text, $parameters = [])
    {
        $this->name = $name;
        $this->text = $text;
        $this->setParameters($parameters);
    }

    /**
     * Returns element type.
     *
     * @return string
     */
    public function getType()
    {
        return 'term_suggest';
    }

    /**
     * Returns suggest name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        if (!$this->hasParameter('field')) {
            $this->addParameter('field', '_all');
        }

        if (!$this->hasParameter('size')) {
            $this->addParameter('size', self::DEFAULT_SIZE);
        }

        $output = [$this->name => [
            'text' => $this->text,
            'term' => $this->getParameters(),
        ]];

        return $output;
    }
}
