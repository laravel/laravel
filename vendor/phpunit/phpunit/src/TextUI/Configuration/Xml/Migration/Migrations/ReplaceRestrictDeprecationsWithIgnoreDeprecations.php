<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use DOMDocument;
use DOMElement;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ReplaceRestrictDeprecationsWithIgnoreDeprecations implements Migration
{
    /**
     * @throws MigrationException
     */
    public function migrate(DOMDocument $document): void
    {
        $source = $document->getElementsByTagName('source')->item(0);

        if ($source === null) {
            return;
        }

        assert($source instanceof DOMElement);

        if (!$source->hasAttribute('restrictDeprecations')) {
            return;
        }

        $restrictDeprecations = $source->getAttribute('restrictDeprecations') === 'true';

        $source->removeAttribute('restrictDeprecations');

        if (!$restrictDeprecations ||
            $source->hasAttribute('ignoreIndirectDeprecations')) {
            return;
        }

        $source->setAttribute('ignoreIndirectDeprecations', 'true');
    }
}
