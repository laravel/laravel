<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Test;

use PHPUnit\Framework\Attributes\After;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait VarDumperTestTrait
{
    /**
     * @internal
     */
    private array $varDumperConfig = [
        'casters' => [],
        'flags' => null,
    ];

    /**
     * @param array<string, callable> $casters
     */
    protected function setUpVarDumper(array $casters, ?int $flags = null): void
    {
        $this->varDumperConfig['casters'] = $casters;
        $this->varDumperConfig['flags'] = $flags;
    }

    /**
     * @after
     */
    #[After]
    protected function tearDownVarDumper(): void
    {
        $this->varDumperConfig['casters'] = [];
        $this->varDumperConfig['flags'] = null;
    }

    /**
     * @return void
     */
    public function assertDumpEquals(mixed $expected, mixed $data, int $filter = 0, string $message = '')
    {
        $this->assertSame($this->prepareExpectation($expected, $filter), $this->getDump($data, null, $filter), $message);
    }

    /**
     * @return void
     */
    public function assertDumpMatchesFormat(mixed $expected, mixed $data, int $filter = 0, string $message = '')
    {
        $this->assertStringMatchesFormat($this->prepareExpectation($expected, $filter), $this->getDump($data, null, $filter), $message);
    }

    protected function getDump(mixed $data, string|int|null $key = null, int $filter = 0): ?string
    {
        if (null === $flags = $this->varDumperConfig['flags']) {
            $flags = getenv('DUMP_LIGHT_ARRAY') ? CliDumper::DUMP_LIGHT_ARRAY : 0;
            $flags |= getenv('DUMP_STRING_LENGTH') ? CliDumper::DUMP_STRING_LENGTH : 0;
            $flags |= getenv('DUMP_COMMA_SEPARATOR') ? CliDumper::DUMP_COMMA_SEPARATOR : 0;
        }

        $cloner = new VarCloner();
        $cloner->addCasters($this->varDumperConfig['casters']);
        $cloner->setMaxItems(-1);
        $dumper = new CliDumper(null, null, $flags);
        $dumper->setColors(false);
        $data = $cloner->cloneVar($data, $filter)->withRefHandles(false);
        if (null !== $key && null === $data = $data->seek($key)) {
            return null;
        }

        return rtrim($dumper->dump($data, true));
    }

    private function prepareExpectation(mixed $expected, int $filter): string
    {
        if (!\is_string($expected)) {
            $expected = $this->getDump($expected, null, $filter);
        }

        return rtrim($expected);
    }
}
