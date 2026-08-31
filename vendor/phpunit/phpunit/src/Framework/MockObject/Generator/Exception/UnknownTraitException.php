<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Generator;

use function sprintf;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5243
 */
final class UnknownTraitException extends \PHPUnit\Framework\Exception implements Exception
{
    public function __construct(string $traitName)
    {
        parent::__construct(
            sprintf(
                'Trait "%s" does not exist',
                $traitName,
            ),
        );
    }
}
