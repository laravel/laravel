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

use DOMDocument;
use DOMElement;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ConvertLogTypes implements Migration
{
    public function migrate(DOMDocument $document): void
    {
        $logging = $document->getElementsByTagName('logging')->item(0);

        if (!$logging instanceof DOMElement) {
            return;
        }
        $types = [
            'junit'        => 'junit',
            'teamcity'     => 'teamcity',
            'testdox-html' => 'testdoxHtml',
            'testdox-text' => 'testdoxText',
            'testdox-xml'  => 'testdoxXml',
            'plain'        => 'text',
        ];

        $logNodes = [];

        foreach ($logging->getElementsByTagName('log') as $logNode) {
            if (!isset($types[$logNode->getAttribute('type')])) {
                continue;
            }

            $logNodes[] = $logNode;
        }

        foreach ($logNodes as $oldNode) {
            $newLogNode = $document->createElement($types[$oldNode->getAttribute('type')]);
            $newLogNode->setAttribute('outputFile', $oldNode->getAttribute('target'));

            $logging->replaceChild($newLogNode, $oldNode);
        }
    }
}
