<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Loader;

use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * CsvFileLoader loads translations from CSV files.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CsvFileLoader extends FileLoader
{
    private string $delimiter = ';';
    private string $enclosure = '"';
    /**
     * @deprecated since Symfony 7.2, to be removed in 8.0
     */
    private string $escape = '';

    protected function loadResource(string $resource): array
    {
        $messages = [];

        try {
            $file = new \SplFileObject($resource, 'rb');
        } catch (\RuntimeException $e) {
            throw new NotFoundResourceException(\sprintf('Error opening file "%s".', $resource), 0, $e);
        }

        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        $file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);

        foreach ($file as $data) {
            if (false === $data) {
                continue;
            }

            if (!str_starts_with($data[0], '#') && isset($data[1]) && 2 === \count($data)) {
                $messages[$data[0]] = $data[1];
            }
        }

        return $messages;
    }

    /**
     * Sets the delimiter, enclosure, and escape character for CSV.
     */
    public function setCsvControl(string $delimiter = ';', string $enclosure = '"', string $escape = ''): void
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        if ('' !== $escape) {
            trigger_deprecation('symfony/translation', '7.2', 'The "escape" parameter of the "%s" method is deprecated. It will be removed in 8.0.', __METHOD__);
        }

        $this->escape = $escape;
    }
}
