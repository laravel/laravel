<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Dumper;

use Symfony\Component\VarDumper\Cloner\Data;

/**
 * DataDumperInterface for dumping Data objects.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface DataDumperInterface
{
    /**
     * @return string|null
     */
    public function dump(Data $data);
}
