<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Command\Descriptor;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
interface DumpDescriptorInterface
{
    public function describe(OutputInterface $output, Data $data, array $context, int $clientId): void;
}
