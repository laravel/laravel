<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Baseline;

use const DIRECTORY_SEPARATOR;
use function assert;
use function dirname;
use function is_file;
use function realpath;
use function sprintf;
use function str_replace;
use function trim;
use DOMElement;
use DOMXPath;
use PHPUnit\Util\Xml\Loader as XmlLoader;
use PHPUnit\Util\Xml\XmlException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Reader
{
    /**
     * @param non-empty-string $baselineFile
     *
     * @throws CannotLoadBaselineException
     */
    public function read(string $baselineFile): Baseline
    {
        if (!is_file($baselineFile)) {
            throw new CannotLoadBaselineException(
                sprintf(
                    'Cannot read baseline %s, file does not exist',
                    $baselineFile,
                ),
            );
        }

        try {
            $document = (new XmlLoader)->loadFile($baselineFile);
        } catch (XmlException $e) {
            throw new CannotLoadBaselineException(
                sprintf(
                    'Cannot read baseline %s: %s',
                    $baselineFile,
                    trim($e->getMessage()),
                ),
            );
        }

        $version = (int) $document->documentElement->getAttribute('version');

        if ($version !== Baseline::VERSION) {
            throw new CannotLoadBaselineException(
                sprintf(
                    'Cannot read baseline %s, version %d is not supported',
                    $baselineFile,
                    $version,
                ),
            );
        }

        $baseline          = new Baseline;
        $baselineDirectory = dirname(realpath($baselineFile));
        $xpath             = new DOMXPath($document);

        foreach ($xpath->query('file') as $fileElement) {
            assert($fileElement instanceof DOMElement);

            $file = $baselineDirectory . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $fileElement->getAttribute('path'));

            foreach ($xpath->query('line', $fileElement) as $lineElement) {
                assert($lineElement instanceof DOMElement);

                $line = (int) $lineElement->getAttribute('number');
                $hash = $lineElement->getAttribute('hash');

                foreach ($xpath->query('issue', $lineElement) as $issueElement) {
                    assert($issueElement instanceof DOMElement);

                    $description = $issueElement->textContent;

                    assert($line > 0);
                    assert(!empty($hash));
                    assert(!empty($description));

                    $baseline->add(Issue::from($file, $line, $hash, $description));
                }
            }
        }

        return $baseline;
    }
}
