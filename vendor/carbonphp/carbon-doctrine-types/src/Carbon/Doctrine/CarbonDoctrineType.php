<?php

declare(strict_types=1);

namespace Carbon\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;

interface CarbonDoctrineType
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform);

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform);

    public function convertToDatabaseValue($value, AbstractPlatform $platform);
}
