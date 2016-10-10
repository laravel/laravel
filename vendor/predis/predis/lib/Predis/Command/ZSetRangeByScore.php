<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command;

/**
 * @link http://redis.io/commands/zrangebyscore
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetRangeByScore extends ZSetRange
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZRANGEBYSCORE';
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareOptions($options)
    {
        $opts = array_change_key_case($options, CASE_UPPER);
        $finalizedOpts = array();

        if (isset($opts['LIMIT']) && is_array($opts['LIMIT'])) {
            $limit = array_change_key_case($opts['LIMIT'], CASE_UPPER);

            $finalizedOpts[] = 'LIMIT';
            $finalizedOpts[] = isset($limit['OFFSET']) ? $limit['OFFSET'] : $limit[0];
            $finalizedOpts[] = isset($limit['COUNT']) ? $limit['COUNT'] : $limit[1];
        }

        return array_merge($finalizedOpts, parent::prepareOptions($options));
    }

    /**
     * {@inheritdoc}
     */
    protected function withScores()
    {
        $arguments = $this->getArguments();

        for ($i = 3; $i < count($arguments); $i++) {
            switch (strtoupper($arguments[$i])) {
                case 'WITHSCORES':
                    return true;

                case 'LIMIT':
                    $i += 2;
                    break;
            }
        }

        return false;
    }
}
