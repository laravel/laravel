<?php

declare(strict_types=1);

namespace Faker\Core;

use Faker\Calculator;
use Faker\Extension;

/**
 * @experimental This class is experimental and does not fall under our BC promise
 */
final class Barcode implements Extension\BarcodeExtension
{
    private Extension\NumberExtension $numberExtension;

    public function __construct(?Extension\NumberExtension $numberExtension = null)
    {
        $this->numberExtension = $numberExtension ?: new Number();
    }

    private function ean(int $length = 13): string
    {
        $code = Extension\Helper::numerify(str_repeat('#', $length - 1));

        return sprintf('%s%s', $code, Calculator\Ean::checksum($code));
    }

    public function ean13(): string
    {
        return $this->ean();
    }

    public function ean8(): string
    {
        return $this->ean(8);
    }

    public function isbn10(): string
    {
        $code = Extension\Helper::numerify(str_repeat('#', 9));

        return sprintf('%s%s', $code, Calculator\Isbn::checksum($code));
    }

    public function isbn13(): string
    {
        $code = '97' . $this->numberExtension->numberBetween(8, 9) . Extension\Helper::numerify(str_repeat('#', 9));

        return sprintf('%s%s', $code, Calculator\Ean::checksum($code));
    }
}
