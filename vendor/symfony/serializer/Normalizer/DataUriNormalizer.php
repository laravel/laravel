<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Normalizer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Normalizes an {@see \SplFileInfo} object to a data URI.
 * Denormalizes a data URI to a {@see \SplFileObject} object.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class DataUriNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var MimeTypeGuesserInterface
     */
    private $mimeTypeGuesser;

    public function __construct(MimeTypeGuesserInterface $mimeTypeGuesser = null)
    {
        if (null === $mimeTypeGuesser && class_exists('Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser')) {
            $mimeTypeGuesser = MimeTypeGuesser::getInstance();
        }

        $this->mimeTypeGuesser = $mimeTypeGuesser;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if (!$object instanceof \SplFileInfo) {
            throw new InvalidArgumentException('The object must be an instance of "\SplFileInfo".');
        }

        $mimeType = $this->getMimeType($object);
        $splFileObject = $this->extractSplFileObject($object);

        $data = '';

        $splFileObject->rewind();
        while (!$splFileObject->eof()) {
            $data .= $splFileObject->fgets();
        }

        if ('text' === explode('/', $mimeType, 2)[0]) {
            return sprintf('data:%s,%s', $mimeType, rawurlencode($data));
        }

        return sprintf('data:%s;base64,%s', $mimeType, base64_encode($data));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \SplFileInfo;
    }

    /**
     * {@inheritdoc}
     *
     * Regex adapted from Brian Grinstead code.
     *
     * @see https://gist.github.com/bgrins/6194623
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (!preg_match('/^data:([a-z0-9]+\/[a-z0-9]+(;[a-z0-9\-]+\=[a-z0-9\-]+)?)?(;base64)?,[a-z0-9\!\$\&\\\'\,\(\)\*\+\,\;\=\-\.\_\~\:\@\/\?\%\s]*\s*$/i', $data)) {
            throw new UnexpectedValueException('The provided "data:" URI is not valid.');
        }

        try {
            switch ($class) {
                case 'Symfony\Component\HttpFoundation\File\File':
                    return new File($data, false);

                case 'SplFileObject':
                case 'SplFileInfo':
                    return new \SplFileObject($data);
            }
        } catch (\RuntimeException $exception) {
            throw new UnexpectedValueException($exception->getMessage(), $exception->getCode(), $exception);
        }

        throw new InvalidArgumentException(sprintf('The class parameter "%s" is not supported. It must be one of "SplFileInfo", "SplFileObject" or "Symfony\Component\HttpFoundation\File\File".', $class));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        $supportedTypes = array(
            \SplFileInfo::class => true,
            \SplFileObject::class => true,
            'Symfony\Component\HttpFoundation\File\File' => true,
        );

        return isset($supportedTypes[$type]);
    }

    /**
     * Gets the mime type of the object. Defaults to application/octet-stream.
     *
     * @param \SplFileInfo $object
     *
     * @return string
     */
    private function getMimeType(\SplFileInfo $object)
    {
        if ($object instanceof File) {
            return $object->getMimeType();
        }

        if ($this->mimeTypeGuesser && $mimeType = $this->mimeTypeGuesser->guess($object->getPathname())) {
            return $mimeType;
        }

        return 'application/octet-stream';
    }

    /**
     * Returns the \SplFileObject instance associated with the given \SplFileInfo instance.
     *
     * @param \SplFileInfo $object
     *
     * @return \SplFileObject
     */
    private function extractSplFileObject(\SplFileInfo $object)
    {
        if ($object instanceof \SplFileObject) {
            return $object;
        }

        return $object->openFile();
    }
}
