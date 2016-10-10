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
 * @link http://redis.io/commands/info
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerInfoV26x extends ServerInfo
{
    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        $info = array();
        $current = null;
        $infoLines = preg_split('/\r?\n/', $data);

        if (isset($infoLines[0]) && $infoLines[0][0] !== '#') {
            return parent::parseResponse($data);
        }

        foreach ($infoLines as $row) {
            if ($row === '') {
                continue;
            }

            if (preg_match('/^# (\w+)$/', $row, $matches)) {
                $info[$matches[1]] = array();
                $current = &$info[$matches[1]];
                continue;
            }

            list($k, $v) = $this->parseRow($row);
            $current[$k] = $v;
        }

        return $info;
    }
}
