<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util\Http;

use function file_get_contents;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @codeCoverageIgnore
 */
final class PhpDownloader implements Downloader
{
    /**
     * @param non-empty-string $url
     */
    public function download(string $url): false|string
    {
        return file_get_contents($url);
    }
}
