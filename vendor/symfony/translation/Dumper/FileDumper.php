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

use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\Exception\RuntimeException;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * FileDumper is an implementation of DumperInterface that dump a message catalogue to file(s).
 *
 * Options:
 * - path (mandatory): the directory where the files should be saved
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 */
abstract class FileDumper implements DumperInterface
{
    /**
     * A template for the relative paths to files.
     */
    protected string $relativePathTemplate = '%domain%.%locale%.%extension%';

    /**
     * Sets the template for the relative paths to files.
     */
    public function setRelativePathTemplate(string $relativePathTemplate): void
    {
        $this->relativePathTemplate = $relativePathTemplate;
    }

    public function dump(MessageCatalogue $messages, array $options = []): void
    {
        if (!\array_key_exists('path', $options)) {
            throw new InvalidArgumentException('The file dumper needs a path option.');
        }

        // save a file for each domain
        foreach ($messages->getDomains() as $domain) {
            $fullpath = $options['path'].'/'.$this->getRelativePath($domain, $messages->getLocale());
            if (!file_exists($fullpath)) {
                $directory = \dirname($fullpath);
                if (!file_exists($directory) && !@mkdir($directory, 0o777, true)) {
                    throw new RuntimeException(\sprintf('Unable to create directory "%s".', $directory));
                }
            }

            $intlDomain = $domain.MessageCatalogue::INTL_DOMAIN_SUFFIX;
            $intlMessages = $messages->all($intlDomain);

            if ($intlMessages) {
                $intlPath = $options['path'].'/'.$this->getRelativePath($intlDomain, $messages->getLocale());
                file_put_contents($intlPath, $this->formatCatalogue($messages, $intlDomain, $options));

                $messages->replace([], $intlDomain);

                try {
                    if ($messages->all($domain)) {
                        file_put_contents($fullpath, $this->formatCatalogue($messages, $domain, $options));
                    }
                    continue;
                } finally {
                    $messages->replace($intlMessages, $intlDomain);
                }
            }

            file_put_contents($fullpath, $this->formatCatalogue($messages, $domain, $options));
        }
    }

    /**
     * Transforms a domain of a message catalogue to its string representation.
     */
    abstract public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = []): string;

    /**
     * Gets the file extension of the dumper.
     */
    abstract protected function getExtension(): string;

    /**
     * Gets the relative file path using the template.
     */
    private function getRelativePath(string $domain, string $locale): string
    {
        return strtr($this->relativePathTemplate, [
            '%domain%' => $domain,
            '%locale%' => $locale,
            '%extension%' => $this->getExtension(),
        ]);
    }
}
