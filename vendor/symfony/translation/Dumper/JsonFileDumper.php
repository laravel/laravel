<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Dumper;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * JsonFileDumper generates an json formatted string representation of a message catalogue.
 *
 * @author singles
 */
class JsonFileDumper extends FileDumper
{
    public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = []): string
    {
        $flags = $options['json_encoding'] ?? \JSON_PRETTY_PRINT;

        return json_encode($messages->all($domain), $flags);
    }

    protected function getExtension(): string
    {
        return 'json';
    }
}
