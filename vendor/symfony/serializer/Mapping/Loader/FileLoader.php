<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Mapping\Loader;

use Symfony\Component\Serializer\Exception\MappingException;

/**
 * Base class for all file based loaders.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
abstract class FileLoader implements LoaderInterface
{
    /**
     * @var string
     */
    protected $file;

    /**
     * Constructor.
     *
     * @param string $file The mapping file to load
     *
     * @throws MappingException if the mapping file does not exist or is not readable
     */
    public function __construct($file)
    {
        if (!is_file($file)) {
            throw new MappingException(sprintf('The mapping file %s does not exist', $file));
        }

        if (!is_readable($file)) {
            throw new MappingException(sprintf('The mapping file %s is not readable', $file));
        }

        $this->file = $file;
    }
}
