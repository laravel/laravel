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
 * CsvFileDumper generates a csv formatted string representation of a message catalogue.
 *
 * @author Stealth35
 */
class CsvFileDumper extends FileDumper
{
    private string $delimiter = ';';
    private string $enclosure = '"';

    public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = []): string
    {
        $handle = fopen('php://memory', 'r+');

        foreach ($messages->all($domain) as $source => $target) {
            fputcsv($handle, [$source, $target], $this->delimiter, $this->enclosure, '\\');
        }

        rewind($handle);
        $output = stream_get_contents($handle);
        fclose($handle);

        return $output;
    }

    /**
     * Sets the delimiter and escape character for CSV.
     */
    public function setCsvControl(string $delimiter = ';', string $enclosure = '"'): void
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
    }

    protected function getExtension(): string
    {
        return 'csv';
    }
}
