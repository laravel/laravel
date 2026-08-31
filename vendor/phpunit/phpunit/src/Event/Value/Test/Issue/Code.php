<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Code\IssueTrigger;

enum Code: string
{
    public function isFirstPartyOrTest(): bool
    {
        return $this === self::FirstParty || $this === self::Test;
    }

    public function isThirdPartyOrPhpunitOrPhp(): bool
    {
        return $this === self::ThirdParty || $this === self::PHPUnit || $this === self::PHP;
    }
    case FirstParty = 'first-party code';
    case ThirdParty = 'third-party code';
    case Test       = 'test code';
    case PHP        = 'PHP runtime';
    case PHPUnit    = 'PHPUnit';
}
