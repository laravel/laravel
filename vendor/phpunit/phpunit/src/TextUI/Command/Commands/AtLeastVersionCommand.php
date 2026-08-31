<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Command;

use function version_compare;
use PHPUnit\Runner\Version;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class AtLeastVersionCommand implements Command
{
    private string $version;

    public function __construct(string $version)
    {
        $this->version = $version;
    }

    public function execute(): Result
    {
        if (version_compare(Version::id(), $this->version, '>=')) {
            return Result::from();
        }

        return Result::from('', Result::FAILURE);
    }
}
