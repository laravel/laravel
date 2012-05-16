<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\File\MimeType;

/**
 * A singleton mime type to file extension guesser.
 *
 * A default guesser is provided.
 * You can register custom guessers by calling the register()
 * method on the singleton instance.
 *
 * <code>
 * $guesser = ExtensionGuesser::getInstance();
 * $guesser->register(new MyCustomExtensionGuesser());
 * </code>
 *
 * The last registered guesser is preferred over previously registered ones.
 *
 */
class ExtensionGuesser implements ExtensionGuesserInterface
{
    /**
     * The singleton instance
     * @var ExtensionGuesser
     */
    static private $instance = null;

    /**
     * All registered ExtensionGuesserInterface instances
     * @var array
     */
    protected $guessers = array();

    /**
     * Returns the singleton instance
     *
     * @return ExtensionGuesser
     */
    static public function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Registers all natively provided extension guessers
     */
    private function __construct()
    {
        $this->register(new MimeTypeExtensionGuesser());
    }

    /**
     * Registers a new extension guesser
     *
     * When guessing, this guesser is preferred over previously registered ones.
     *
     * @param ExtensionGuesserInterface $guesser
     */
    public function register(ExtensionGuesserInterface $guesser)
    {
        array_unshift($this->guessers, $guesser);
    }

    /**
     * Tries to guess the extension
     *
     * The mime type is passed to each registered mime type guesser in reverse order
     * of their registration (last registered is queried first). Once a guesser
     * returns a value that is not NULL, this method terminates and returns the
     * value.
     *
     * @param  string $mimeType   The mime type
     * @return string             The guessed extension or NULL, if none could be guessed
     */
    public function guess($mimeType)
    {
        foreach ($this->guessers as $guesser) {
            $extension = $guesser->guess($mimeType);

            if (null !== $extension) {
                break;
            }
        }

        return $extension;
    }
}
