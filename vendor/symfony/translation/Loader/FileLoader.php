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

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 */
abstract class FileLoader extends ArrayLoader
{
    public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
    {
        if (!stream_is_local($resource)) {
            throw new InvalidResourceException(\sprintf('This is not a local file "%s".', $resource));
        }

        if (!file_exists($resource)) {
            throw new NotFoundResourceException(\sprintf('File "%s" not found.', $resource));
        }

        $messages = $this->loadResource($resource);

        // empty resource
        $messages ??= [];

        // not an array
        if (!\is_array($messages)) {
            throw new InvalidResourceException(\sprintf('Unable to load file "%s".', $resource));
        }

        $catalogue = parent::load($messages, $locale, $domain);

        if (class_exists(FileResource::class)) {
            $catalogue->addResource(new FileResource($resource));
        }

        return $catalogue;
    }

    /**
     * @throws InvalidResourceException if stream content has an invalid format
     */
    abstract protected function loadResource(string $resource): array;
}
