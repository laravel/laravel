<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\TestData;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class DataFromDataProvider extends TestData
{
    private int|string $dataSetName;
    private string $dataAsStringForResultOutput;

    public static function from(int|string $dataSetName, string $data, string $dataAsStringForResultOutput): self
    {
        return new self($dataSetName, $data, $dataAsStringForResultOutput);
    }

    protected function __construct(int|string $dataSetName, string $data, string $dataAsStringForResultOutput)
    {
        $this->dataSetName                 = $dataSetName;
        $this->dataAsStringForResultOutput = $dataAsStringForResultOutput;

        parent::__construct($data);
    }

    public function dataSetName(): int|string
    {
        return $this->dataSetName;
    }

    /**
     * @internal This method is not covered by the backward compatibility promise for PHPUnit
     */
    public function dataAsStringForResultOutput(): string
    {
        return $this->dataAsStringForResultOutput;
    }

    public function isFromDataProvider(): true
    {
        return true;
    }
}
