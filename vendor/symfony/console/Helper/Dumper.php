<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Cloner\ClonerInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class Dumper
{
    private \Closure $handler;

    public function __construct(
        private OutputInterface $output,
        private ?CliDumper $dumper = null,
        private ?ClonerInterface $cloner = null,
    ) {
        if (class_exists(CliDumper::class)) {
            $this->handler = function ($var): string {
                $dumper = $this->dumper ??= new CliDumper(null, null, CliDumper::DUMP_LIGHT_ARRAY | CliDumper::DUMP_COMMA_SEPARATOR);
                $dumper->setColors($this->output->isDecorated());

                return rtrim($dumper->dump(($this->cloner ??= new VarCloner())->cloneVar($var)->withRefHandles(false), true));
            };
        } else {
            $this->handler = static fn ($var): string => match (true) {
                null === $var => 'null',
                true === $var => 'true',
                false === $var => 'false',
                \is_string($var) => '"'.$var.'"',
                default => rtrim(print_r($var, true)),
            };
        }
    }

    public function __invoke(mixed $var): string
    {
        return ($this->handler)($var);
    }
}
